<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentCompo;
use App\Models\StudentCompoPayment;

class StudentCompoPaymentController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('can:admin.standalone_registrations.payments.view');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            // We want to list students who have payments.
            $query = StudentCompo::has('payments')->with(['payments.admin', 'program']);

            // Filters
            if ($request->filled('program_id')) {
                $query->where('program_id', $request->program_id);
            }
            if ($request->filled('date_from')) {
                $query->whereHas('payments', function($q) use ($request) {
                    $q->whereDate('created_at', '>=', $request->date_from);
                });
            }
            if ($request->filled('date_to')) {
                $query->whereHas('payments', function($q) use ($request) {
                    $q->whereDate('created_at', '<=', $request->date_to);
                });
            }

            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addColumn('details_url', function($row) {
                    return route('standalone_registrations.payments.details', $row->id);
                })
                ->addColumn('total_payments', function($row) {
                    $totalNIS = $row->payments->where('currency', 'NIS')->sum('amount');
                    $totalUSD = $row->payments->where('currency', 'USD')->sum('amount');
                    $totalJOD = $row->payments->where('currency', 'JOD')->sum('amount');
                    $totals = [];
                    if($totalNIS > 0) $totals[] = $totalNIS . ' NIS';
                    if($totalUSD > 0) $totals[] = $totalUSD . ' USD';
                    if($totalJOD > 0) $totals[] = $totalJOD . ' JOD';
                    return implode(' + ', $totals) ?: '0';
                })
                ->addColumn('program_title', function ($row) {
                    return $row->program ? $row->program->title : 'N/A';
                })
                ->addColumn('payments_count', function ($row) {
                    return '<span class="badge badge-light-primary fs-7 fw-bold">' . $row->payments->count() . '</span>';
                })
                ->rawColumns(['payments_count'])
                ->make(true);
        }

        self::$data['title'] = 'Student Payments (Combo)';
        self::$data['breadcrumb'] = 'Payments';
        self::$data['programs'] = \App\Models\Programs::all();
        
        return view('admin.standalone_registrations.payments', self::$data);
    }

    public function details($id)
    {
        $student = StudentCompo::with(['payments.admin'])->findOrFail($id);
        
        $html = '<table class="table table-bordered table-striped table-sm text-center mb-0">';
        $html .= '<thead class="bg-light-primary text-primary"><tr>
                    <th>المبلغ</th>
                    <th>العملة</th>
                    <th>اسم الدافع</th>
                    <th>طريقة الدفع</th>
                    <th>تأكيد الدفعة بواسطة</th>
                    <th>تاريخ الدفع</th>
                    <th>الإشعار</th>
                  </tr></thead><tbody>';
                  
        foreach($student->payments as $payment) {
            $adminName = $payment->admin ? $payment->admin->name : 'System';
            $receipt = $payment->receipt_path ? '<a href="'.url('uploads/'.$payment->receipt_path).'" target="_blank" class="btn btn-sm btn-icon btn-light-success"><i class="ki-duotone ki-file fs-2"><span class="path1"></span><span class="path2"></span></i></a>' : 'N/A';
            
            $html .= '<tr>';
            $html .= '<td class="fw-bold">'.$payment->amount.'</td>';
            $html .= '<td>'.$payment->currency.'</td>';
            $html .= '<td>'.$payment->payer_name.'</td>';
            $html .= '<td>'.$payment->payment_method.'</td>';
            $html .= '<td>'.$adminName.'</td>';
            $html .= '<td>'.$payment->created_at->format('Y-m-d H:i').'</td>';
            $html .= '<td>'.$receipt.'</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';

        return $html;
    }
}
