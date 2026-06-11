<?php

namespace App\Http\Controllers\Admin;

use App\Models\PlacementTests;
use App\Mail\PlacementTestMail;
use App\Mail\PaymentConfirmationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\DataTables;

class PlacementTestsController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        parent::$data['active_menu'] = 'placement_tests';
    }

    public function getIndex()
    {
        return view('admin.placement_tests.index', parent::$data);
    }

    public function getList(Request $request)
    {
        $branchId = app(\App\Services\BranchContext::class)->getId()
                 ?? ($request->get('branch_id') ?: null);

        $tests = PlacementTests::with(['student', 'paymentMethod'])
            ->select('placement_tests.*')
            ->join('students', 'placement_tests.student_id', '=', 'students.id')
            ->whereNull('students.deleted_at')
            ->when($branchId, fn($q) => $q->where('students.branch_id', $branchId));

        // Apply Filters
        if ($request->has('test_date') && $request->test_date != '') {
            $tests->whereDate('placement_tests.test_date', $request->test_date);
        }
        if ($request->has('test_time') && $request->test_time != '') {
            $tests->where('placement_tests.test_time', 'like', '%' . $request->test_time . '%');
        }
        if ($request->has('gender') && $request->gender != '') {
            $tests->where('students.gender', $request->gender);
        }
        if ($request->has('program_type') && $request->program_type != '') {
            $tests->where('students.program_type', $request->program_type);
        }
        if ($request->has('search_text') && $request->search_text != '') {
            $search = $request->search_text;
            $tests->where(function($q) use ($search) {
                $q->where('students.name', 'like', "%$search%")
                  ->orWhere('students.mobile', 'like', "%$search%")
                  ->orWhere('students.email', 'like', "%$search%");
            });
        }

        // Age-group filter (kid ≤15  /  adult >15)
        // Uses DOB so it stays accurate regardless of stored program_type.
        if ($request->has('age_group') && $request->age_group != '') {
            $cutoffKidsDob = \Carbon\Carbon::today()->subYears(15)->toDateString();
            if ($request->age_group === 'kids') {
                // Born after cutoff → age ≤ 15
                $tests->whereNotNull('students.dob')
                      ->where('students.dob', '>=', $cutoffKidsDob);
            } elseif ($request->age_group === 'adult') {
                // Born on/before cutoff → age > 15 (or DOB missing & program_type adult)
                $tests->where(function ($q) use ($cutoffKidsDob) {
                    $q->where('students.dob', '<', $cutoffKidsDob)
                      ->orWhere(function ($q2) {
                          $q2->whereNull('students.dob')
                             ->where('students.program_type', 'adult');
                      });
                });
            }
        }

        return DataTables::of($tests)
            ->addColumn('action', function ($test) {
                $btns = '<div class="btn-group">';
                
                // Confirm Payment Button
                if ($test->status === 'pending') {
                    $btns .= '<button class="btn btn-sm btn-info confirm-payment-btn" data-id="' . $test->id . '" title="Confirm Payment"><i class="fa fa-check-double"></i></button>';
                }

                // Score Button (Only if payment confirmed)
                if (in_array($test->status, ['payment_confirmed', 'waiting_for_test'])) {
                    $studentName = $test->student ? addslashes($test->student->name) : '';
                    $btns .= '<button class="btn btn-sm btn-success score-btn" data-id="' . $test->id . '" data-name="' . e($studentName) . '" title="Record Score"><i class="fa fa-graduation-cap"></i></button>';
                }

                $btns .= '<button onclick="showStudentModal('.$test->student_id.')" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" title="عرض التفاصيل">
                            <i class="ki-duotone ki-eye fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                          </button>';
                
                $btns .= '<a href="' . route('placement_tests.edit', $test->id) . '" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></a>';
                $btns .= '<button class="btn btn-sm btn-danger delete-btn" data-id="' . $test->id . '"><i class="fa fa-trash"></i></button>';
                $btns .= '</div>';
                return $btns;
            })
            ->editColumn('student.name', function ($row) {
                if (!$row->student) return 'N/A';
                $pType = $row->student->program_type == 'kids' 
                    ? '<span class="badge badge-light-success fs-8 fw-bold ms-2">KIDS</span>'
                    : '<span class="badge badge-light-primary fs-8 fw-bold ms-2">ADULT</span>';
                return $row->student->name . $pType;
            })
            ->editColumn('status', function ($test) {
                $class = [
                    'pending' => 'bg-warning',
                    'payment_confirmed' => 'bg-info',
                    'waiting_for_test' => 'bg-primary',
                    'completed' => 'bg-success'
                ][$test->status] ?? 'bg-secondary';
                return '<span class="badge ' . $class . '">' . ucfirst(str_replace('_', ' ', $test->status)) . '</span>';
            })
            ->editColumn('payment_receipt', function ($test) {
                if ($test->payment_receipt) {
                    return '<a href="' . asset('uploads/' . $test->payment_receipt) . '" target="_blank" class="btn btn-sm btn-secondary"><i class="fa fa-eye"></i> View Receipt</a>';
                }
                return 'No Receipt';
            })
            ->rawColumns(['action', 'status', 'payment_receipt', 'student.name'])
            ->make(true);
    }

    public function confirmPayment($id)
    {
        $test = PlacementTests::with('student')->findOrFail($id);
        $test->status = 'payment_confirmed';
        $test->save();

        if ($test->student && $test->student->email) {
            Mail::to($test->student->email)->send(new PaymentConfirmationMail($test));
        }

        return response()->json(['success' => true, 'message' => 'Payment confirmed successfully and email sent to student.']);
    }

    public function postScore(Request $request, $id)
    {
        $test = PlacementTests::findOrFail($id);
        $validated = $request->validate([
            'score' => 'required|string|max:50',
            'assigned_level' => 'required|string|max:100',
        ]);

        $test->update(array_merge($validated, ['status' => 'completed']));
        
        // Also update student level if assigned
        if ($test->student) {
            $test->student->update(['current_level' => $validated['assigned_level']]);
        }

        return response()->json(['success' => true, 'message' => 'Score recorded and level assigned.']);
    }

    public function getEdit($id)
    {
        $test = PlacementTests::with('student')->findOrFail($id);
        return view('admin.placement_tests.edit', compact('test'));
    }

    public function postEdit(Request $request, $id)
    {
        $test = PlacementTests::findOrFail($id);
        $validated = $request->validate([
            'status' => 'required|in:pending,payment_confirmed,waiting_for_test,completed',
            'score' => 'nullable|string|max:50',
            'assigned_level' => 'nullable|string|max:100',
            'test_date' => 'required|date',
            'test_time' => 'required|string',
        ]);

        $test->update($validated);
        return redirect()->route('placement_tests.view')->with('success', 'Placement test updated successfully');
    }

    public function postDelete(Request $request)
    {
        $test = PlacementTests::findOrFail($request->id);
        $test->delete();
        return response()->json(['success' => true]);
    }

    public function sendBatchEmail(Request $request)
    {
        $validated = $request->validate([
            'test_ids' => 'required|array',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'test_date' => 'nullable|date',
            'test_time' => 'nullable|string',
        ]);

        foreach ($validated['test_ids'] as $id) {
            $test = PlacementTests::with('student')->findOrFail($id);
            
            // Update date/time if provided by admin in the modal
            if (!empty($validated['test_date'])) $test->test_date = $validated['test_date'];
            if (!empty($validated['test_time'])) $test->test_time = $validated['test_time'];
            $test->save();

            // Send Email (Queued)
            if ($test->student && $test->student->email) {
                Mail::to($test->student->email)->send(new PlacementTestMail($test, $validated['subject'], $validated['message']));
            }
        }

        return response()->json(['success' => true, 'message' => 'Emails sent successfully to selected students.']);
    }
}
