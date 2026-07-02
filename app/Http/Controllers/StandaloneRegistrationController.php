<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Programs;
use App\Models\StudentCompo;
use App\Models\ParentsCompoStudent;
use App\Models\Settings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StandaloneRegistrationController extends Controller
{
    public function index()
    {
        $programs = Programs::where('status', 1)
            ->whereIn('id', function($query) {
                $query->select('program_id')->from('fee_settings');
            })->get();
            
        $settings = \App\Models\Settings::find(1);
        $allFees = DB::table('fee_settings')
            ->join('fee_types', 'fee_settings.type', '=', 'fee_types.slug')
            ->select('fee_settings.*', 'fee_types.name_en', 'fee_types.name_ar')
            ->get();
            
        $feesByProgram = $allFees->groupBy('program_id');
        $paymentMethods = DB::table('payment_methods')
            ->where('is_active', 1)
            ->whereNotNull('credentials')
            ->where('credentials', '!=', '[]')
            ->where('credentials', '!=', '{}')
            ->where('credentials', '!=', 'null')
            ->get();
            
        $socials = \App\Models\Socials::all();
        
        return view('frontend.standalone_registration', compact('programs', 'settings', 'feesByProgram', 'paymentMethods', 'socials'));
    }

    public function store(Request $request)
    {
        $messages = [
            'full_name_ar.regex' => 'الرجاء إدخال حروف عربية فقط',
            'phone.regex' => 'رقم الجوال يجب أن يبدأ بـ 05 ويتكون من 10 أرقام',
            'phone.unique' => 'رقم الجوال هذا مسجل مسبقاً',
            'email.unique' => 'البريد الإلكتروني هذا مسجل مسبقاً'
        ];

        $rules = [
            'full_name_ar' => 'required|string|max:255|regex:/^[\p{Arabic}\s]+$/u',
            'full_name_en' => 'required|string|max:255',
            'phone' => 'required|string|regex:/^05[0-9]{8}$/|unique:student_compos,phone',
            'email' => 'required|email|max:255|unique:student_compos,email',
            'dob' => 'required|date',
            'gender' => 'required|in:Male,Female',
            'address' => 'required|string',
            'branch' => 'required|string',
            'major_profession' => 'required|string',
            'health_issues' => 'required|boolean',
            'health_issues_details' => 'nullable|string',
            'placement_test' => 'required|boolean',
            'placement_test_date' => 'nullable|required_if:placement_test,1|date',
            'program_type' => 'required|in:kids,adults',
            'program_id' => 'required|exists:programs,id',
        ];

        // Age calculation logic for backend validation (Optional but good practice)
        $age = \Carbon\Carbon::parse($request->dob)->age;
        if ($age <= 15) {
            $rules['parent_name'] = 'required|string|max:255';
            $rules['parent_phone'] = 'required|string|max:50';
            $rules['parent_email'] = 'nullable|email|max:255';
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $student = StudentCompo::create([
                'full_name_ar' => $request->full_name_ar,
                'full_name_en' => $request->full_name_en,
                'phone' => $request->phone,
                'email' => $request->email,
                'dob' => $request->dob,
                'gender' => $request->gender,
                'address' => $request->address,
                'branch' => $request->branch,
                'major_profession' => $request->major_profession,
                'health_issues' => $request->health_issues,
                'health_issues_details' => $request->health_issues ? $request->health_issues_details : null,
                'placement_test' => $request->placement_test,
                'placement_test_date' => $request->placement_test ? $request->placement_test_date : null,
                'program_type' => $request->program_type,
                'program_id' => $request->program_id,
                'is_invoiced' => true, // Assuming success means invoiced
                'is_read' => false,
            ]);

            if ($age <= 15) {
                ParentsCompoStudent::create([
                    'student_compo_id' => $student->id,
                    'parent_name' => $request->parent_name,
                    'parent_phone' => $request->parent_phone,
                    'parent_email' => $request->parent_email,
                ]);
            }

            DB::commit();

            // Dispatch realtime event
            $comboCount = \App\Support\NotifyCounts::unreadComboRegistrations();
            $totalCount = \App\Support\NotifyCounts::total();
            event(new \App\Events\NewComboRegistrationEvent($student, $comboCount, $totalCount));
            // Trigger global counter update
            event(new \App\Events\CountersUpdated());

            return response()->json([
                'success' => true, 
                'message' => 'Registration successful. (تم التسجيل بنجاح)',
                'student_id' => $student->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'An error occurred. Please try again. ' . $e->getMessage()], 500);
        }
    }
}
