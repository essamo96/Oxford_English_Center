<?php

namespace App\Http\Controllers;

use Hash;
use Config;
use Carbon\Carbon;
use App\Models\Contacts;
use App\Models\Students;
use App\Mail\WelcomeStudentMail;
use Illuminate\Http\Request;
use App\Mail\NewStudentEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Notifications\newAdminCreatedNotification;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    ///////////////////////////
    public function getIndex()
    {
        return view('frontend.contact.view', parent::$data);
    }

    public function postContact(Request $request)
    {
        ////////////////////////////////////////////
        $name = $request->get('name');
        $email = $request->get('email');
        $mobile = $request->get('mobile');
        $subject = $request->get('subject');
        $details = $request->get('message');

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'details' => $details
        ], [
            'name' => 'required',
            'email' => ['required','regex:/^[^\s@]+@gmail\.com$/'],
            'details' => 'required'
        ], [
            'email.regex' => 'You should enter a Gmail address without spaces.',
        ]);



        //////////////////////////////////////////////////////////
        if ($validator->fails()) {
            $msg= $validator->errors()->all();
            $request->session()->flash('danger', $msg);
            return redirect('contact')->withInput();
        } else {
            // Persist the submission so admins can manage it from "Contact Management"
            $contact = (new Contacts())->addContactUs($name, $email, $mobile, $subject, $details, 0);

            // Real-time notification to the admin dashboard (laravel-websockets).
            // Never let a websocket outage break the public contact form.
            try {
                broadcast(new \App\Events\NewContactEvent(
                    contact: $contact,
                    unreadContacts: \App\Support\NotifyCounts::unreadContacts(),
                    totalNotifyCount: \App\Support\NotifyCounts::total(),
                ));
                // Also broadcast a counters update so sidebar badges refresh in all open tabs.
                broadcast(new \App\Events\CountersUpdated());
            } catch (\Throwable $e) {
                Log::error('Contact broadcast failed: ' . $e->getMessage());
            }

            $myarray['name'] = $name;
            $myarray['email'] = $email;
            $myarray['details'] = $details;
            // best-effort notification email; the submission is already saved, and
            // send_mail no longer throws if SMTP is unavailable.
            $this->send_mail($myarray);

            $request->session()->flash('success', 'Successfully Sent ');
            return redirect('contact')->withInput();
        }
    }

    public function getExam()
    {
        return view('frontend.contact.exam', parent::$data);
    }

    public function postExam(Request $request)
    {
        ////////////////////////////////////////////
        $name = $request->get('name');
        $email = $request->get('email');
        $phone = $request->get('phone');
        $gender = $request->get('gender');
        $days = $request->get('days');
        $time = $request->get('time');

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'gender' => $gender,
            'days' => $days,
            'time' => $time,
        ], [
            'name' => 'required',
            'gender' => 'required',
            'days' => 'required',
            'time' => 'required',
            'email' => [
                'required',
                'regex:/^[^\s]+@gmail\.com$/',
            ],
        ],
            [
                'email.regex' => 'You should enter a valid Gmail address without spaces and ending with "gmail.com".',
            ]
        );
        //////////////////////////////////////////////////////////
        if ($validator->fails()) {
            $msg = $validator->errors()->first();
            $request->session()->flash('danger', $msg);
            return redirect('exam')->withInput();
        } else {
            $myarray['name'] = $name;
            $myarray['email'] = $email;
            $myarray['phone'] = $phone;
            $myarray['gender'] = $gender;
            $myarray['days'] = $days;
            $myarray['time'] = $time;

            $add = $this->send_mail($myarray);
            if ($add) {

                $request->session()->flash('success', 'Successfully Sent ');
                return redirect('exam')->withInput();
            } else {
                $request->session()->flash('danger', 'There is a Problem Try Again');
                return redirect('exam')->withInput();
            }
        }
    }

    public function getBook()
    {
        $payment_methods = \App\Models\PaymentMethods::where('is_active', true)->get();
        return view('frontend.contact.book', array_merge(parent::$data, compact('payment_methods')));
    }

    public function postBook(\App\Http\Requests\BookingRequest $request)
    {
        $validated = $request->validated();

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // 1. Handle Parent (if kids program)
            $parentId = null;
            if ($validated['program_type'] === 'kids') {
                $parent = \App\Models\Parents::updateOrCreate(
                    ['phone' => $validated['parent_phone']],
                    [
                        'name' => $validated['parent_name'],
                        'email' => $validated['parent_email'],
                        'relationship' => $validated['parent_relationship'],
                    ]
                );
                $parentId = $parent->id;
            }

            // 2. Handle Student
            $username = $validated['mobile'];
            $password = \Illuminate\Support\Facades\Hash::make($validated['mobile']);

            $student = new \App\Models\Students();
            $student->fill([
                'name' => $validated['name'],
                'name_en' => $validated['name_en'],
                'username' => $username,
                'password' => $password,
                'mobile' => $validated['mobile'],
                'dob' => $validated['dob'],
                'address' => $request->input('address'),
                'health_status' => $request->input('health_status', 'no'),
                'health_notes' => $request->input('health_notes'),
                'preferred_days' => $request->input('preferred_days'),
                'preferred_time' => $request->input('preferred_time'),
                'job' => $validated['program_type'] === 'adult' ? ($validated['major'] ?? 'Adult') : 'Student (Kid)',
                'major' => $validated['program_type'] === 'adult' ? ($validated['major'] ?? null) : null,
                'current_level' => $validated['current_level'] ?? null,
                'program_type' => $validated['program_type'],
                'parent_id' => $parentId,
                'email' => $validated['email'],
                'status' => 0, // Pending
                'gender' => $request->input('gender', 0),
            ]);
            $student->save();

            // Send Welcome Email (Queued)
            try {
                Mail::to($student->email)->send(new WelcomeStudentMail($student));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send welcome email: ' . $e->getMessage());
            }

            // 3. Handle Placement Test (if requested)
            if ($validated['take_test'] === 'yes') {
                $receiptPath = null;
                if ($request->hasFile('payment_receipt')) {
                    $file = $request->file('payment_receipt');
                    $fileName = time() . '_' . $student->id . '.' . $file->getClientOriginalExtension();
                    $receiptPath = $file->storeAs('placement_receipts', $fileName, 'uploads');
                }

                \App\Models\PlacementTests::create([
                    'student_id' => $student->id,
                    'test_date' => $validated['test_date'],
                    'test_time' => $request->input('preferred_time'), // Use preferred time if test time is merged
                    'status' => 'pending',
                    'payment_receipt' => $receiptPath,
                    'payment_method_id' => $validated['payment_method_id'],
                    'paid_amount' => 100.00,
                ]);
            }

            \Illuminate\Support\Facades\DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Your booking has been submitted successfully and will be reviewed by our team.'
                ]);
            }

            $request->session()->flash('success', 'Your booking has been submitted successfully.');
            return redirect('book');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Wizard Booking Error: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An unexpected error occurred: ' . $e->getMessage()
                ], 500);
            }

            $request->session()->flash('danger', 'An unexpected error occurred. Please try again.');
            return redirect('book')->withInput();
        }
    }

    public function getJob()
    {
        return view('frontend.contact.job', parent::$data);
    }

    public function postJob(Request $request)
    {
        ////////////////////////////////////////////
        $name = $request->get('name');
        $email = $request->get('email');
        $phone = $request->get('phone');
        $letter = $request->file('letter');
        $cv = $request->file('cv');

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'cv' => $cv,
            'letter' => $letter
        ], [
            'name' => 'required',
            'email' => 'required|email',
            'cv' => 'required',
            'letter' => 'required'
        ]);
        //////////////////////////////////////////////////////////
        if ($validator->fails()) {
            $request->session()->flash('danger', 'There is a Problem Try Again');
            return redirect('jobs')->withInput();
        } else {
            $myarray['name'] = $name;
            $myarray['email'] = $email;
            $myarray['phone'] = $phone;
            $files['cv'] = $cv;
            $files['letter'] = $letter;
            $add = $this->send_mail($myarray, $files);
            if ($add) {
                $request->session()->flash('success', 'Successfully Sent ');
                return redirect('jobs')->withInput();
            } else {
                $request->session()->flash('danger', 'There is a Problem Try Again');
                return redirect('jobs')->withInput();
            }
        }
    }

    public function send_mail($myarray, $files = NULL)
    {
        $form_data = [
            'myarray' => $myarray
        ];
        // Where contact notifications are delivered.
        $to = 'no-replay@oxford.ps';
        // SMTP credentials + sender come from .env (MAIL_*). Do NOT hardcode the
        // password here: a stale override previously mismatched the username and
        // caused "535 Incorrect authentication data" (SMTP 500 error).
        $fromAddress = config('mail.from.address') ?: 'campany@oxford.ps';
        $fromName = config('mail.from.name') ?: 'Oxford';

        try {
            Mail::send('emails.send', $form_data, function ($message) use ($to, $fromAddress, $fromName, $files) {
                $message->to($to, 'Oxford')->subject('Oxford')->from($fromAddress, $fromName);
                if ($files) {
                    foreach ($files as $file) {
                        $message->attach(
                            $file->getRealPath(),
                            array(
                                'as' => $file->getClientOriginalName(),
                                'mime' => $file->getMimeType()
                            )
                        );
                    }
                }
            });
        } catch (\Throwable $e) {
            // Don't crash the user flow if the mail server is unreachable / misconfigured.
            \Log::error('send_mail failed: ' . $e->getMessage());
            return FALSE;
        }

        return TRUE;
    }
}
