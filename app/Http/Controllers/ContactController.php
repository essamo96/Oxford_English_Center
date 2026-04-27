<?php

namespace App\Http\Controllers;

use Hash;
use Config;
use Carbon\Carbon;
use App\Models\Contacts;
use App\Models\Students;
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
            $myarray['name'] = $name;
            $myarray['email'] = $email;
            $myarray['details'] = $details;
            $add = $this->send_mail($myarray);

            if ($add) {
                $request->session()->flash('success', 'Successfully Sent ');
                return redirect('contact')->withInput();
            } else {
                $request->session()->flash('danger', 'There is a Problem Try Again');
                return redirect('contact')->withInput();
            }
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

        return view('frontend.contact.book', parent::$data);
    }

    public function postBook(Request $request)
    {
        /**
         * Validate booking request using Laravel's validation
         * This provides server-side security and error handling
         */
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:100',
            'dob' => 'required|date|before:today|after:1900-01-01',
            'email' => [
                'required',
                'email',
                'regex:/^[^\s]+@gmail\.com$/',
            ],
            'phone' => [
                'required',
                'regex:/^([0-9\s\-\+\(\)]*)$/',
                'digits:10',
                'unique:students,mobile',
            ],
            'address' => 'required|string|min:3|max:255',
            'major' => 'required|string|min:2|max:100',
            'gender' => 'required|in:Male,Female',
            'how' => 'required|in:Google Search,Social Media,Friend Referral,Advertisement,Other',
            'agree' => 'required|accepted',
        ], [
            // Custom error messages for better UX
            'name.required' => 'Full name is required.',
            'name.min' => 'Full name must be at least 3 characters.',
            'name.max' => 'Full name cannot exceed 100 characters.',

            'dob.required' => 'Date of birth is required.',
            'dob.date' => 'Please enter a valid date of birth.',
            'dob.before' => 'Date of birth must be in the past.',
            'dob.after' => 'Date of birth is invalid.',

            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.regex' => 'Email must be a Gmail address (example@gmail.com).',

            'phone.required' => 'Phone number is required.',
            'phone.regex' => 'Phone number contains invalid characters.',
            'phone.digits' => 'Phone number must be exactly 10 digits.',
            'phone.unique' => 'This phone number is already registered.',

            'address.required' => 'Address is required.',
            'address.min' => 'Address must be at least 5 characters.',
            'address.max' => 'Address cannot exceed 255 characters.',

            'major.required' => 'Major / Field of Study is required.',
            'major.min' => 'Major must be at least 2 characters.',
            'major.max' => 'Major cannot exceed 100 characters.',

            'gender.required' => 'Please select your gender.',
            'gender.in' => 'Invalid gender selection.',

            'how.required' => 'Please tell us how you heard about us.',
            'how.in' => 'Invalid option selected.',

            'agree.required' => 'You must agree to the terms and conditions.',
            'agree.accepted' => 'You must accept the terms and conditions.',
        ]);

        // All validation passed - Create new student record
        try {
            $username = substr($validated['phone'], 3, 7);
            $password = substr($validated['phone'], 3, 7);

            // Prepare student data
            $studentData = [
                'name' => $validated['name'],
                'username' => $username,
                'password' => Hash::make($password),
                'mobile' => $validated['phone'],
                'dob' => date('Y-m-d', strtotime($validated['dob'])),
                'job' => $validated['major'], // Map major to job field
                'email' => $validated['email'],
                'gender' => $validated['gender'],
                'status' => 0, // New student pending approval
                'delaying' => 0,
                'join_date' => null,
                'exam_date' => null,
                'exam_degree' => null,
            ];

            // Create student using model
            $student = new Students();
            $result = $student->addStudent(
                $studentData['name'],
                $studentData['username'],
                $studentData['password'],
                $studentData['mobile'],
                $studentData['dob'],
                $studentData['job'],
                $studentData['email'],
                $studentData['join_date'],
                $studentData['exam_date'],
                $studentData['exam_degree'],
                $studentData['status'],
                $studentData['delaying'],
                $studentData['gender']
            );

            if ($result) {
                // Success - Store message and redirect
                $message = 'Your booking request has been submitted successfully. Please visit the center to complete registration and pay the required fees.';
                $request->session()->flash('success', $message);
                return redirect('book');
            } else {
                // Model error
                $request->session()->flash('danger', 'An error occurred while processing your booking. Please try again.');
                return redirect('book')->withInput();
            }

        } catch (\Exception $e) {
            // Unexpected error
            \Log::error('Booking submission error: ' . $e->getMessage());
            $request->session()->flash('danger', 'An unexpected error occurred. Please contact support.');
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
        $data = [
            'email' => 'no-replay@oxford.ps',
            'name' => 'Oxford',
        ];
        Config::set('mail.driver', 'smtp');
        Config::set('mail.host', 'mail.oxford.ps');
        Config::set('mail.port', 465);
        Config::set('mail.email', 'no-replay@oxford.ps');
        Config::set('mail.encryption', 'ssl');
        Config::set('mail.password', '10GlUmR)1nlf');

        Mail::send('emails.send', $form_data, function ($message) use ($data, $files) {
            $message->to($data['email'], $data['name'])->subject($data['name'])->from($data['email'], $data['name']);
            if ($files) {
                foreach ($files as $file) {
                    $message->attach(
                        $file->getRealPath(),
                        array(
                            'as' => $file->getClientOriginalName(), // If you want you can chnage original name to custom name
                            'mime' => $file->getMimeType()
                        )
                    );
                }
            }
        });
        if (Mail::failures()) {
            return FALSE;
        } else {
            return True;
        }
    }
}
