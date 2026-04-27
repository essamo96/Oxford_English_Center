<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailCampaign;
use App\Models\EmailCampaignLog;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class EmailCampaignController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        parent::$data['active_menu'] = 'email_campaigns';
    }

    public function index()
    {
        return view('admin.email_campaigns.index', parent::$data);
    }

    public function getDatatable()
    {
        $data = EmailCampaign::with('admin')->orderBy('created_at', 'desc');
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('status_label', function($row){
                $badgeClass = 'badge-light-primary';
                $statusText = $row->status;
                
                switch($row->status) {
                    case 'pending': $badgeClass = 'badge-light-warning'; $statusText = 'قيد الانتظار'; break;
                    case 'sending': $badgeClass = 'badge-light-info'; $statusText = 'جاري الإرسال'; break;
                    case 'completed': $badgeClass = 'badge-light-success'; $statusText = 'اكتمل بنجاح'; break;
                    case 'completed_with_errors': $badgeClass = 'badge-light-warning'; $statusText = 'اكتمل مع أخطاء'; break;
                    case 'failed': $badgeClass = 'badge-light-danger'; $statusText = 'فشل'; break;
                }
                
                return '<span class="badge ' . $badgeClass . '">' . $statusText . '</span>';
            })
            ->addColumn('progress', function($row){
                $percentage = $row->total_recipients > 0 
                    ? round((($row->sent_count + $row->failed_count) / $row->total_recipients) * 100) 
                    : 0;
                return '<div class="d-flex flex-column w-100 me-2">
                            <div class="d-flex flex-stack mb-2">
                                <span class="text-muted me-2 fs-7 fw-bold">' . $percentage . '%</span>
                            </div>
                            <div class="progress h-6px w-100">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: ' . $percentage . '%" aria-valuenow="' . $percentage . '" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>';
            })
            ->addColumn('action', function($row){
                return '<a href="' . route('admin.email_campaigns.show', $row->id) . '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                            <i class="ki-duotone ki-eye fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                        </a>
                        <button class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm" onclick="deleteCampaign(' . $row->id . ')">
                            <i class="ki-duotone ki-trash fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                        </button>';
            })
            ->rawColumns(['status_label', 'progress', 'action'])
            ->make(true);
    }

    public function show($id)
    {
        $campaign = EmailCampaign::with(['admin', 'logs' => function($q){
            $q->orderBy('status', 'desc');
        }])->findOrFail($id);
        
        parent::$data['campaign'] = $campaign;
        return view('admin.email_campaigns.show', parent::$data);
    }

    public function status($id)
    {
        $campaign = EmailCampaign::findOrFail($id);
        
        $percentage = $campaign->total_recipients > 0 
            ? round((($campaign->sent_count + $campaign->failed_count) / $campaign->total_recipients) * 100) 
            : 0;
            
        return response()->json([
            'status' => $campaign->status,
            'sent' => $campaign->sent_count,
            'failed' => $campaign->failed_count,
            'total' => $campaign->total_recipients,
            'percentage' => $percentage,
            'completed' => in_array($campaign->status, ['completed', 'completed_with_errors', 'failed'])
        ]);
    }

    public function destroy($id)
    {
        EmailCampaign::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'تم حذف الحملة بنجاح']);
    }
}
