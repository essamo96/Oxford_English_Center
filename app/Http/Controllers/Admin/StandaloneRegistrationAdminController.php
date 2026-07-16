<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentCompo;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StandaloneRegistrationAdminController extends AdminController
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = StudentCompo::with('parents', 'program')->select('student_compos.*');
            
            if ($request->filled('program_id')) {
                $query->where('program_id', $request->program_id);
            }
            if ($request->filled('program_type')) {
                $query->where('program_type', $request->program_type);
            }
            if ($request->filled('gender')) {
                $query->where('gender', $request->gender);
            }
            if ($request->filled('branch')) {
                $query->where('branch', $request->branch);
            }
            if ($request->filled('is_invoiced')) {
                $query->where('is_invoiced', $request->is_invoiced);
            }
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
            if ($request->filled('is_contacted')) {
                $query->where('is_contacted', $request->is_contacted);
            }

            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addIndexColumn()
                ->setRowClass(function ($row) {
                    return $row->is_contacted ? 'bg-light-success' : '';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button type="button" class="btn btn-icon btn-sm btn-primary view-details me-1" data-id="' . $row->id . '" title="عرض"><i class="ki-duotone ki-eye fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i></button>';
                    $btn .= '<button type="button" class="btn btn-icon btn-sm btn-success record-payment-btn me-1" data-id="' . $row->id . '" data-name="' . $row->full_name_ar . '" title="تسجيل دفعة"><i class="ki-duotone ki-wallet fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i></button>';
                    $btn .= '<button type="button" class="btn btn-icon btn-sm btn-danger delete-btn" data-id="' . $row->id . '" title="حذف"><i class="ki-duotone ki-trash fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i></button>';
                    return $btn;
                })
                ->addColumn('program_title', function ($row) {
                    return $row->program ? $row->program->title : 'N/A';
                })
                ->editColumn('is_read', function ($row) {
                    return $row->is_read ? '<span class="badge bg-success">مقروء</span>' : '<span class="badge bg-warning">جديد</span>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('Y-m-d H:i');
                })
                ->editColumn('is_contacted', function ($row) {
                    $checked = $row->is_contacted ? 'checked' : '';
                    return '<div class="form-check form-switch form-check-custom form-check-solid d-flex justify-content-center">
                                <input class="form-check-input contact-toggle" type="checkbox" data-id="' . $row->id . '" ' . $checked . ' />
                            </div>';
                })
                ->rawColumns(['action', 'is_read', 'is_contacted'])
                ->make(true);
        }

        self::$data['title'] = 'Oxford Registrations';
        self::$data['breadcrumb'] = 'Registrations';
        self::$data['programs'] = \App\Models\Programs::all();
        self::$data['branches'] = \App\Models\Branch::where('status', 1)->get();
        self::$data['payment_methods'] = \App\Models\PaymentMethods::where('is_active', 1)->get();
        
        return view('admin.standalone_registrations.index', self::$data);
    }

    public function exportExcel(Request $request)
    {
        $query = StudentCompo::query();

        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }
        if ($request->filled('program_type')) {
            $query->where('program_type', $request->program_type);
        }
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }
        if ($request->filled('branch')) {
            $query->where('branch', $request->branch);
        }
        if ($request->filled('is_invoiced')) {
            $query->where('is_invoiced', $request->is_invoiced);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('is_contacted')) {
            $query->where('is_contacted', $request->is_contacted);
        }

        $registrations = $query->orderBy('full_name_ar')
            ->get(['full_name_ar', 'phone'])
            ->unique('phone')
            ->values();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Registrations');
        $sheet->setRightToLeft(true);

        // Column headers
        $sheet->setCellValue('A1', 'اسم الطالب');
        $sheet->setCellValue('B1', 'رقم الجوال');
        $sheet->getRowDimension(1)->setRowHeight(28);
        $sheet->getStyle('A1:B1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '36336D']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
        ]);

        $row = 2;
        foreach ($registrations as $registration) {
            $sheet->setCellValue('A' . $row, $registration->full_name_ar);
            $sheet->setCellValueExplicit('B' . $row, $registration->phone, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->getRowDimension($row)->setRowHeight(22);

            $fill = $row % 2 === 0 ? 'F1F5F9' : 'FFFFFF';
            $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
                'font' => ['size' => 11],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $fill]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
            ]);
            $row++;
        }

        $sheet->getColumnDimension('A')->setWidth(40);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->freezePane('A2');

        $fileName = 'new-registrations-' . now()->format('Y-m-d') . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function smsStudents(Request $request)
    {
        $query = StudentCompo::query();

        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }
        if ($request->filled('program_type')) {
            $query->where('program_type', $request->program_type);
        }
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }
        if ($request->filled('branch')) {
            $query->where('branch', $request->branch);
        }
        if ($request->filled('is_invoiced')) {
            $query->where('is_invoiced', $request->is_invoiced);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('is_contacted')) {
            $query->where('is_contacted', $request->is_contacted);
        }

        $students = $query->with('program')
            ->whereNotNull('phone')->where('phone', '!=', '')
            ->orderBy('full_name_ar')
            ->get(['id', 'full_name_ar', 'phone', 'program_id', 'program_type', 'gender', 'branch', 'is_read', 'created_at'])
            ->map(function ($r) {
                return [
                    'id' => $r->id,
                    'name' => $r->full_name_ar,
                    'mobile' => $r->phone,
                    'program_type' => $r->program_type,
                    'gender' => $r->gender,
                    'branch' => $r->branch,
                    'is_read' => (bool) $r->is_read,
                    'registered_at' => optional($r->created_at)->format('Y-m-d'),
                ];
            });

        return response()->json(['students' => $students]);
    }

    public function show($id)
    {
        $registration = StudentCompo::with('parents', 'program')->findOrFail($id);
        
        if (!$registration->is_read) {
            $registration->is_read = true;
            $registration->save();
        }

        return response()->json([
            'success' => true,
            'data' => $registration,
            'parents' => $registration->parents
        ]);
    }

    public function destroy($id)
    {
        $registration = StudentCompo::findOrFail($id);
        $registration->delete();

        return response()->json([
            'success' => true,
            'message' => 'Registration deleted successfully.'
        ]);
    }

    public function markAsRead($id)
    {
        $registration = StudentCompo::findOrFail($id);
        $registration->is_read = true;
        $registration->save();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        StudentCompo::where('is_read', false)->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    public function comboParents(Request $request)
    {
        if ($request->ajax()) {
            $query = StudentCompo::with('parents', 'program')
                ->whereRaw('TIMESTAMPDIFF(YEAR, dob, CURDATE()) <= 15')
                ->select('student_compos.*');
            
            if ($request->filled('program_id')) {
                $query->where('program_id', $request->program_id);
            }
            if ($request->filled('gender')) {
                $query->where('gender', $request->gender);
            }
            if ($request->filled('branch')) {
                $query->where('branch', $request->branch);
            }
            if ($request->filled('is_invoiced')) {
                $query->where('is_invoiced', $request->is_invoiced);
            }
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addColumn('action', function ($row) {
                    $btn = '<button type="button" class="btn btn-icon btn-sm btn-primary view-details me-1" data-id="' . $row->id . '" title="عرض"><i class="ki-duotone ki-eye fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i></button>';
                    $btn .= '<button type="button" class="btn btn-icon btn-sm btn-danger delete-btn" data-id="' . $row->id . '" title="حذف"><i class="ki-duotone ki-trash fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i></button>';
                    return $btn;
                })
                ->addColumn('program_title', function ($row) {
                    return $row->program ? $row->program->title : 'N/A';
                })
                ->addColumn('parents_list', function ($row) {
                    if($row->parents->count() > 0) {
                        return $row->parents->map(function($p) {
                            return $p->parent_name . ' (' . $p->parent_phone . ')';
                        })->implode('<br>');
                    }
                    return 'N/A';
                })
                ->editColumn('is_read', function ($row) {
                    return $row->is_read ? '<span class="badge bg-success">مقروء</span>' : '<span class="badge bg-warning">جديد</span>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('Y-m-d H:i');
                })
                ->rawColumns(['action', 'is_read', 'parents_list'])
                ->make(true);
        }

        self::$data['title'] = 'أولياء الأمور';
        self::$data['breadcrumb'] = 'Parents';
        self::$data['programs'] = \App\Models\Programs::all();
        self::$data['branches'] = \App\Models\Branch::where('status', 1)->get();
        
        return view('admin.standalone_registrations.parents', self::$data);
    }

    public function toggleContact(Request $request)
    {
        $registration = StudentCompo::findOrFail($request->id);
        $registration->is_contacted = $request->is_contacted;
        $registration->save();

        return response()->json(['success' => true]);
    }

    public function storePayment(Request $request, $id)
    {
        $request->validate([
            'payer_name' => 'required|string|max:191',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string',
            'payment_method' => 'required|string',
            'receipt' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $registration = StudentCompo::findOrFail($id);

        $payment = new \App\Models\StudentCompoPayment();
        $payment->student_compo_id = $registration->id;
        $payment->admin_id = auth()->guard('admin')->id() ?? auth()->id();
        $payment->payer_name = $request->payer_name;
        $payment->amount = $request->amount;
        $payment->currency = $request->currency;
        $payment->payment_method = $request->payment_method;

        if ($request->hasFile('receipt')) {
            $file = $request->file('receipt');
            $fileName = time() . '_' . $registration->id . '_receipt.' . $file->getClientOriginalExtension();
            $payment->receipt_path = $file->storeAs('compo_receipts', $fileName, 'uploads');
        }

        $payment->save();

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الدفعة بنجاح'
        ]);
    }
}
