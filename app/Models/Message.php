<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Message extends Model {

    protected $table = "messages";

    /** user_type column values */
    const TYPE_STUDENT = 0;
    const TYPE_TEACHER = 1;
    const TYPE_ADMIN   = 2;

    public function chatGroup() {
        return $this->belongsTo('App\Models\Groups', 'group_id');
    }

    /**
     * The sender's name/avatar lives in one of three tables depending on user_type,
     * so it is resolved with correlated subqueries rather than a relation.
     */
    private function senderColumns() {
        return "*,(
                        CASE
                            WHEN user_type = 0 THEN (select name FROM students where id=messages.from_user)
                            WHEN user_type = 2 THEN (select name FROM users where id=messages.from_user)
                            ELSE  (select name FROM teachers where id=messages.from_user)
                        END
                    ) as name,(
                        CASE
                            WHEN user_type = 0 THEN (select image FROM students where id=messages.from_user)
                            WHEN user_type = 2 THEN (select image FROM users where id=messages.from_user)
                            ELSE  (select image FROM teachers where id=messages.from_user)
                        END
                    ) as image,(
                        CASE
                            WHEN user_type = 0 THEN (select gender FROM students where id=messages.from_user)
                            ELSE NULL
                        END
                    ) as gender ";
    }

    public function getLastMessages($group_id, $type) {
        return $this->selectRaw($this->senderColumns())
                ->where('group_id', $group_id)
                ->orderBy('messages.created_at', 'desc')
                ->limit(10)
                ->get();
    }

    /**
     * Full group history for the admin monitor, oldest first.
     *
     * $afterId lets the live poller ask only for what it has not rendered yet — the
     * Pusher push is the primary delivery path, this is the catch-up/fallback path.
     */
    public function getGroupMessages($group_id, $afterId = null, $limit = 200) {
        $query = $this->selectRaw($this->senderColumns())->where('group_id', $group_id);

        if ($afterId) {
            $query->where('messages.id', '>', $afterId);
            return $query->orderBy('messages.id', 'asc')->limit($limit)->get();
        }

        // Newest N, then flipped, so a long-running group still opens on recent traffic.
        return $query->orderBy('messages.id', 'desc')->limit($limit)->get()->reverse()->values();
    }

    /**
     * Full-text-ish search inside one group's messages.
     *
     * Matches the message body and the attachment's original filename, so
     * "التقرير.pdf" is findable even though it was sent without a caption.
     */
    public function searchInGroup($group_id, $term, $limit = 100) {
        $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $term) . '%';

        return $this->selectRaw($this->senderColumns())
            ->where('group_id', $group_id)
            ->where(function ($q) use ($like) {
                $q->where('content', 'LIKE', $like)
                  ->orWhere('attachment_name', 'LIKE', $like);
            })
            ->orderBy('messages.id', 'desc')
            ->limit($limit)
            ->get();
    }

    /** Single message re-selected with the sender columns (used after an insert). */
    public function getOneWithSender($id) {
        return $this->selectRaw($this->senderColumns())->where('messages.id', $id)->first();
    }

    /**
     * Per-group activity summary for the monitor index: total messages, the last
     * message and when it arrived — in one query instead of N.
     */
    public static function groupActivitySummary() {
        return DB::table('messages')
            ->select('group_id', DB::raw('COUNT(*) as total'), DB::raw('MAX(id) as last_id'), DB::raw('MAX(created_at) as last_at'))
            ->groupBy('group_id')
            ->get()
            ->keyBy('group_id');
    }
}
