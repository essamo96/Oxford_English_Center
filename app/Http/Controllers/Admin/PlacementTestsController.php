<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlacementTests;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PlacementTestsController extends Controller
{
    public function getIndex()
    {
        return view('admin.placement_tests.index');
    }

    public function getList()
    {
        $tests = PlacementTests::with(['student', 'paymentMethod'])->select('placement_tests.*');

        return DataTables::of($tests)
            ->addColumn('action', function ($test) {
                $btns = '<div class="btn-group">';
                
                // Confirm Payment Button
                if ($test->status === 'pending') {
                    $btns .= '<button class="btn btn-sm btn-info confirm-payment-btn" data-id="' . $test->id . '" title="Confirm Payment"><i class="fa fa-check-double"></i></button>';
                }

                // Score Button (Only if payment confirmed)
                if (in_array($test->status, ['payment_confirmed', 'waiting_for_test'])) {
                    $btns .= '<button class="btn btn-sm btn-success score-btn" data-id="' . $test->id . '" data-name="' . $test->student->name . '" title="Record Score"><i class="fa fa-graduation-cap"></i></button>';
                }

                $btns .= '<a href="' . route('placement_tests.edit', $test->id) . '" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></a>';
                $btns .= '<button class="btn btn-sm btn-danger delete-btn" data-id="' . $test->id . '"><i class="fa fa-trash"></i></button>';
                $btns .= '</div>';
                return $btns;
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
            ->addColumn('record_score', function ($test) {
                if (in_array($test->status, ['payment_confirmed', 'waiting_for_test'])) {
                    return '<button class="btn btn-sm btn-success score-btn w-100" data-id="' . $test->id . '" data-name="' . $test->student->name . '"><i class="fa fa-graduation-cap me-1"></i> رصد</button>';
                }
                return '<span class="text-muted small">بانتظار الدفع</span>';
            })
            ->rawColumns(['action', 'status', 'payment_receipt', 'record_score'])
            ->make(true);
    }

    public function confirmPayment($id)
    {
        $test = PlacementTests::findOrFail($id);
        $test->status = 'payment_confirmed';
        $test->save();
        return response()->json(['success' => true, 'message' => 'Payment confirmed successfully.']);
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
}
