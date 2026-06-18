<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SmsArchive;
use App\Models\Programs;
use App\Models\Groups;
use Yajra\DataTables\Facades\DataTables;

class SmsArchiveController extends AdminController
{
    public function index()
    {
        parent::$data['active_menu'] = 'sms_archive';
        parent::$data['programs'] = Programs::all();
        parent::$data['groups'] = Groups::all();
        return view('admin.sms_archive', parent::$data);
    }

    public function getData(Request $request)
    {
        $query = SmsArchive::with(['student', 'sender', 'program', 'group'])->select('sms_archives.*');

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }
        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return DataTables::of($query)
            ->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->format('Y-m-d h:i A') : '';
            })
            ->addColumn('sender_name', function ($row) {
                return $row->sender ? $row->sender->name : 'نظام';
            })
            ->addColumn('student_name', function ($row) {
                return $row->student ? $row->student->name : $row->receiver_name;
            })
            ->addColumn('program_group', function ($row) {
                $html = '';
                if ($row->program) {
                    $html .= '<span class="badge badge-light-primary fw-bold fs-8 px-2 py-1 me-1"><i class="bi bi-mortarboard-fill me-1"></i>'.$row->program->title.'</span>';
                }
                if ($row->group) {
                    $html .= '<span class="badge badge-light-info fw-bold fs-8 px-2 py-1"><i class="bi bi-people-fill me-1"></i>'.$row->group->name.'</span>';
                }
                return $html;
            })
            ->rawColumns(['program_group'])
            ->make(true);
    }
}
