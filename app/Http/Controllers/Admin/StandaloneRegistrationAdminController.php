<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentCompo;

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
                ->setRowClass(function ($row) {
                    return $row->is_contacted ? 'bg-light-success' : '';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button type="button" class="btn btn-sm btn-primary view-details me-1" data-id="' . $row->id . '">View</button>';
                    $btn .= '<button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '">Delete</button>';
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
        
        return view('admin.standalone_registrations.index', self::$data);
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
                    $btn = '<button type="button" class="btn btn-sm btn-primary view-details me-1" data-id="' . $row->id . '">View</button>';
                    $btn .= '<button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '">Delete</button>';
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
    public function toggleContact(Request $request)
    {
        $registration = StudentCompo::findOrFail($request->id);
        $registration->is_contacted = $request->is_contacted;
        $registration->save();

        return response()->json(['success' => true]);
    }
}
