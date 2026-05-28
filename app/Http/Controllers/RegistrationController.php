<?php

namespace App\Http\Controllers;

use App\Models\Students;
use App\Models\Parents;
use App\Models\Groups;
use App\Models\GroupStudents;
use App\Models\GroupStudentsFees;
use App\Models\Programs;
use App\Models\FeeSettings;
use App\Models\PlacementTests;
use App\Models\PaymentMethods;
use App\Models\Times;
use App\Models\Contacts;
use App\Mail\WelcomeStudentMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Config;

class RegistrationController extends Controller
{
    /**
     * Show the registration wizard form.
     */
    public function showRegistrationForm($type = 'adults')
    {
        self::$data['type']            = $type;
        self::$data['payment_methods'] = PaymentMethods::where('is_active', 1)
            ->get()
            ->filter(function ($m) {
                // Hide methods without any credential value, even if active
                if (empty($m->credentials) || !is_array($m->credentials)) return false;
                foreach ($m->credentials as $v) {
                    if (!is_null($v) && trim((string) $v) !== '') return true;
                }
                return false;
            })
            ->values();
        // Only show ACTIVE programs that actually have at least one fee row defined
        // (so the public can't pick a program with no pricing yet).
        self::$data['programs'] = Programs::where('status', 1)
            ->whereExists(function ($q) {
                $q->select(\DB::raw(1))
                  ->from('fee_settings')
                  ->whereColumn('fee_settings.program_id', 'programs.id')
                  ->where('fee_settings.amount', '>', 0);
            })
            ->get();
        self::$data['groups']          = Groups::where('status', 1)->get();
        self::$data['placement_times'] = Times::where('status', 1)
            ->where('is_placement_test', 1)
            ->orderBy('id', 'asc')
            ->get();

        // Representative placement-test fee (used in the registration form notice).
        // Pulled from FeeSettings (avg across programs). Falls back to 100 ILS.
        self::$data['placement_test_fee'] = (float) FeeSettings::where('type', 'placement_test')
                                                                ->avg('amount') ?: 100.0;

        return view('frontend.contact.book', self::$data);
    }

    /**
     * AJAX: check if email exists in students table
     */
    public function checkEmail(Request $request)
    {
        $email = $request->query('email');
        if (!$email) return response()->json(['exists' => false]);
        $exists = Students::where('email', $email)->exists();
        return response()->json(['exists' => $exists]);
    }

    /**
     * AJAX: check if mobile exists in students table
     */
    public function checkMobile(Request $request)
    {
        $mobile = $request->query('mobile');
        if (!$mobile) return response()->json(['exists' => false]);
        $exists = Students::where('mobile', $mobile)->exists();
        return response()->json(['exists' => $exists]);
    }

    /**
     * Comprehensive Registration Logic (The Wizard)
     */
    public function postRegister(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email',
            'mobile' => 'required|string|max:20',
            'dob' => 'required|date',
            'gender' => 'required',
            'program_type' => 'required|in:adult,kids',
            'enrollment_type' => 'required|in:course,test',
            'address' => 'required|string',
            'health_status' => 'required|in:yes,no',
            'health_notes' => 'required_if:health_status,yes',
            'payment_method_id' => 'required',
            'student_fee_paid' => 'required|numeric|min:0',
            'payment_receipt' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ];

        // Path Specific Rules
        if ($request->enrollment_type === 'test') {
            $rules['test_date']       = 'required|date';
            $rules['preferred_days']  = 'required';
            $rules['preferred_time']  = 'required';
        } else {
            // Default to course path
            $rules['program_id'] = 'required|exists:programs,id';
            // Current level is required if enrolling directly
            $rules['current_level'] = 'required';
        }

        // Compute age from DOB to enforce guardian + program-type routing
        $age = null;
        if ($request->dob) {
            try {
                $age = \Carbon\Carbon::parse($request->dob)->age;
            } catch (\Exception $e) { $age = null; }
        }

        // Auto-route to the correct program based on age:
        // age <= 15  → kids program (even if applicant picked adult)
        // age >  15  → adult program (even if applicant picked kids by mistake)
        $effectiveProgramType = $request->program_type;
        if ($age !== null) {
            $effectiveProgramType = ($age <= 15) ? 'kids' : 'adult';
        }

        $requiresGuardian = ($effectiveProgramType === 'kids')
            || ($age !== null && $age <= 15);

        if ($requiresGuardian) {
            $rules['parent_name'] = 'required|string|max:255';
            $rules['parent_phone'] = 'required|string|max:20';
            $rules['parent_relationship'] = 'required|string';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $plainPassword = substr($request->mobile, -7);

            // 1. Create Student Record ($effectiveProgramType already age-corrected above)
            $student = Students::create([
                'name' => $request->name,
                'name_en' => $request->name_en,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'dob' => $request->dob,
                'gender' => $request->gender,
                'address' => $request->address,
                'major' => $request->major,
                'current_level' => $request->current_level,
                'program_type' => $effectiveProgramType,
                'requested_program_type' => $request->program_type, // original applicant choice (before age-routing)
                'enrollment_type' => $request->enrollment_type, // 'test' | 'course'
                'health_conditions' => $request->health_notes,
                'note' => $request->general_notes,
                'join_date' => now()->toDateString(),
                'status' => 0, // Inactive
                'is_verified' => 0, // Unverified
                'username' => $request->email,
                'password' => Hash::make($plainPassword),
            ]);

            // 2. Handle Parent Info (for Kids or underaged adult registrants)
            if ($requiresGuardian) {
                // Resolve the relationship label. If the applicant picked the "other"
                // option from the relationships table, store their free-text description.
                $relationshipLabel = $request->parent_relationship;
                $relSlug = $request->parent_relationship;
                if ($relSlug) {
                    $relRow = \App\Models\Relationship::where('slug', $relSlug)->first();
                    if ($relRow) {
                        if ($relRow->is_other) {
                            $custom = trim((string) $request->parent_relationship_other);
                            $relationshipLabel = $custom !== '' ? $custom : $relRow->name_ar;
                        } else {
                            $relationshipLabel = $relRow->name_ar;
                        }
                    }
                }

                $parent = Parents::create([
                    'name'         => $request->parent_name,
                    'phone'        => $request->parent_phone,
                    'email'        => $request->parent_email ?: $request->email,
                    'relationship' => $relationshipLabel,
                ]);
                $student->update(['parent_id' => $parent->id]);
            }

            // 3. Handle File Upload
            $receiptPath = null;
            if ($request->hasFile('payment_receipt')) {
                $file = $request->file('payment_receipt');
                $filename = time() . '_' . $student->id . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/receipts'), $filename);
                $receiptPath = 'receipts/' . $filename;
            }

            $paid = $request->student_fee_paid;

            // 4. Branch Logic based on Enrollment Type
            if ($request->enrollment_type == 'course') {
                $pId = $request->program_id;

                // Fetch all fees for this program EXCEPT placement tests
                $allFees = FeeSettings::where('program_id', $pId)
                    ->where('type', '!=', 'placement_test')
                    ->get();

                $totalDue = $allFees->sum('amount');

                // Minimum payment is configured on the PROGRAM (not per fee row)
                $program = \App\Models\Programs::find($pId);
                $minDue  = $program ? $program->computeMinimumDue((float) $totalDue) : 0.0;
                if ($minDue > 0 && (float) $paid < $minDue) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'الحد الأدنى المقبول للدفعة الأولى هو ' . number_format($minDue, 2) . ' ILS.',
                    ], 422);
                }

                GroupStudentsFees::create([
                    'student_id' => $student->id,
                    'student_fee_paid' => $paid,
                    'total_due_amount' => $totalDue,
                    'remaining_amount' => $totalDue - $paid,
                    'student_paid_type' => 'Course Enrollment',
                    'payment_receipt' => $receiptPath,
                    'status' => 'pending',
                    'audit_status' => 'pending',
                    'payment_method_id' => $request->payment_method_id,
                ]);
            } else {
                // If program_id is selected in Test path, use its specific placement test fee
                // Otherwise use general placement test fee
                $pId = $request->program_id;
                $testFeeQuery = FeeSettings::where('type', 'placement_test');
                if ($pId) {
                    $testFeeQuery->where('program_id', $pId);
                }
                
                $testFees = $testFeeQuery->get();
                $totalDue = $testFees->count() > 0 ? $testFees->sum('amount') : 100;

                // Min payment for the placement-test path comes from the program (if any)
                $program = $pId ? \App\Models\Programs::find($pId) : null;
                $minDue  = $program ? $program->computeMinimumDue((float) $totalDue) : 0.0;
                if ($minDue > 0 && (float) $paid < $minDue) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'الحد الأدنى المقبول للدفعة الأولى هو ' . number_format($minDue, 2) . ' ILS.',
                    ], 422);
                }
                
                PlacementTests::create([
                    'student_id' => $student->id,
                    'test_date' => $request->test_date,
                    'test_time' => $request->preferred_time, // mirror for admin filter
                    'preferred_days' => $request->preferred_days,
                    'preferred_time' => $request->preferred_time,
                    'total_amount' => $totalDue,
                    'paid_amount' => $paid,
                    'remaining_amount' => $totalDue - $paid,
                    'payment_method_id' => $request->payment_method_id,
                    'payment_receipt' => $receiptPath,
                    'status' => 'pending',
                    'payment_status' => ($paid >= $totalDue) ? 'paid' : 'partially_paid',
                ]);

                GroupStudentsFees::create([
                    'student_id' => $student->id,
                    'student_fee_paid' => $paid,
                    'total_due_amount' => $totalDue,
                    'remaining_amount' => $totalDue - $paid,
                    'student_paid_type' => 'Placement Test Fee',
                    'payment_receipt' => $receiptPath,
                    'status' => 'pending',
                    'audit_status' => 'pending',
                    'payment_method_id' => $request->payment_method_id,
                ]);
            }

            // 5. Send Emails (Logic from old ContactController)
            try {
                // Send Welcome Email to Student
                Mail::to($student->email)->send(new WelcomeStudentMail($student));
                
                // Optional: Send Notification to Admin
                // Mail::send('emails.new_registration', ['student' => $student], function($m) { ... });
            } catch (\Exception $e) {
                Log::error('Mail Error: ' . $e->getMessage());
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم التسجيل بنجاح. تم إرسال إيميل ترحيبي لبريدك الإلكتروني.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء التسجيل: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generic Contact Form Handler (From ContactController)
     */
    public function postContact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $myarray = [
            'name' => $request->name,
            'email' => $request->email,
            'details' => $request->message
        ];

        if ($this->send_custom_mail($myarray)) {
            return redirect()->back()->with('success', 'Successfully Sent');
        }

        return redirect()->back()->with('danger', 'Problem sending mail');
    }

    /**
     * Job Application Handler (From ContactController)
     */
    public function postJob(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'cv' => 'required|file',
            'letter' => 'required|file'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('danger', 'Validation failed');
        }

        $myarray = ['name' => $request->name, 'email' => $request->email, 'phone' => $request->phone];
        $files = ['cv' => $request->file('cv'), 'letter' => $request->file('letter')];

        if ($this->send_custom_mail($myarray, $files)) {
            return redirect()->back()->with('success', 'Application Sent');
        }

        return redirect()->back()->with('danger', 'Error sending application');
    }

    /**
     * Custom Mail Helper (Manual SMTP Logic from old ContactController)
     */
    private function send_custom_mail($myarray, $files = NULL)
    {
        try {
            $data = ['email' => 'no-replay@oxford.ps', 'name' => 'Oxford'];
            
            // Manual Config override if needed (though .env is better)
            Config::set('mail.host', 'mail.oxford.ps');
            Config::set('mail.port', 465);
            Config::set('mail.encryption', 'ssl');
            Config::set('mail.username', 'no-replay@oxford.ps');
            Config::set('mail.password', '10GlUmR)1nlf');

            Mail::send('emails.send', ['myarray' => $myarray], function ($message) use ($data, $files) {
                $message->to($data['email'], $data['name'])
                        ->subject('New Website Message')
                        ->from($data['email'], $data['name']);

                if ($files) {
                    foreach ($files as $file) {
                        $message->attach($file->getRealPath(), [
                            'as' => $file->getClientOriginalName(),
                            'mime' => $file->getMimeType()
                        ]);
                    }
                }
            });
            return true;
        } catch (\Exception $e) {
            Log::error('Custom Mail Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch program fee via AJAX.
     */
    public function getProgramFee($id)
    {
        $fee = FeeSettings::where('program_id', $id)->where('type', 'course')->first();
        if ($fee) return response()->json(['success' => true, 'amount' => $fee->amount]);

        $program = Programs::find($id);
        return response()->json(['success' => true, 'amount' => $program ? $program->fees : 0]);
    }
}
