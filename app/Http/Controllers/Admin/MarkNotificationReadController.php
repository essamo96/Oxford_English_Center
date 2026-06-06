<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Students;
use App\Models\Contacts;
use App\Models\Students_Admin_Messages;
use App\Models\Teachers_Admin_Messages;
use App\Models\Closed_Classes;
use App\Events\CountersUpdated;

class MarkNotificationReadController extends Controller
{
    public function markAsReadAndRedirect(Request $request)
    {
        $type = $request->get('type');
        $id = $request->get('id');

        // Mark specific entity as read based on type
        switch ($type) {
            case 'student_message':
                if ($id) {
                    Students_Admin_Messages::where('student_id', $id)->where('seen', 0)->update(['seen' => 1]);
                }
                $redirectUrl = route('students.messages'); // Maybe append ?open=id if frontend supports it
                break;

            case 'teacher_message':
                if ($id) {
                    Teachers_Admin_Messages::where('teacher_id', $id)->where('seen', 0)->update(['seen' => 1]);
                }
                $redirectUrl = route('teachers.messages');
                break;

            case 'contact':
                // For contact, we might not want to force status=1 just by clicking, 
                // because it means "Replied". However, the user specifically requested 
                // that clicking it marks it as read. Since there's no 'seen' column for contacts, 
                // we have to update status=1 (which means contacted/handled) OR we don't change the status 
                // but just redirect. Wait, let's update status=1 if they explicitly asked for it.
                // Let's NOT update status=1 automatically unless we are sure. The user said: 
                // "قراءة Contact Message تقلل العداد مباشرة." This implies changing status to 1.
                if ($id) {
                    Contacts::where('id', $id)->where('status', 0)->update(['status' => 1]);
                }
                $redirectUrl = route('contacts.view');
                break;

            case 'booking':
                if ($id) {
                    Students::where('id', $id)->where('seen', 0)->update(['seen' => 1]);
                } else {
                    Students::where('seen', 0)->update(['seen' => 1]);
                }
                $redirectUrl = route('dashboard.view.membership');
                break;

            case 'closed_class':
                if ($id) {
                    Closed_Classes::where('id', $id)->where('seen', 0)->update(['seen' => 1]);
                } else {
                    Closed_Classes::where('seen', 0)->update(['seen' => 1]);
                }
                $redirectUrl = route('closed_classes.view');
                break;
                
            default:
                $redirectUrl = url('/admin');
                break;
        }

        // Broadcast the update so all tabs sync
        broadcast(new CountersUpdated());

        return redirect($redirectUrl);
    }
}
