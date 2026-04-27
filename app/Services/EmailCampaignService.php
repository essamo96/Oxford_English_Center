<?php

namespace App\Services;

use App\Models\EmailCampaign;
use App\Models\EmailCampaignLog;
use App\Jobs\SendCampaignEmailJob;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EmailCampaignService
{
    /**
     * Create and launch a new email campaign.
     *
     * @param array $data ['subject', 'message', 'sender_name', 'attachment', 'recipients']
     * @return EmailCampaign
     */
    public function launchCampaign(array $data)
    {
        $campaign = EmailCampaign::create([
            'subject' => $data['subject'],
            'message' => $data['message'],
            'sender_name' => $data['sender_name'] ?? 'Oxford English Centre',
            'attachment' => $data['attachment'] ?? null,
            'total_recipients' => count($data['recipients']),
            'status' => 'pending',
            'admin_id' => Auth::guard('admin')->check() ? Auth::guard('admin')->id() : null,
            'started_at' => Carbon::now(),
        ]);

        $campaign->update(['status' => 'sending']);

        foreach ($data['recipients'] as $recipient) {
            $formattedRecipient = is_array($recipient) ? $recipient : ['email' => $recipient, 'name' => null];
            
            // Create log entry immediately so it shows up in history
            EmailCampaignLog::create([
                'campaign_id' => $campaign->id,
                'recipient_name' => $formattedRecipient['name'] ?? null,
                'recipient_email' => $formattedRecipient['email'],
                'status' => 'pending', // Initially pending
            ]);

            SendCampaignEmailJob::dispatch($campaign, $formattedRecipient);
        }

        return $campaign;
    }


    /**
     * Calculate estimated time remaining for a campaign.
     *
     * @param EmailCampaign $campaign
     * @param int $emailsPerMinute
     * @return string
     */
    public function getEstimatedTime($campaign, $emailsPerMinute = 60)
    {
        $remaining = $campaign->total_recipients - ($campaign->sent_count + $campaign->failed_count);
        if ($remaining <= 0) return "0 seconds";

        $minutes = floor($remaining / $emailsPerMinute);
        $seconds = ceil(($remaining % $emailsPerMinute) * (60 / $emailsPerMinute));

        $output = "";
        if ($minutes > 0) $output .= $minutes . " min ";
        if ($seconds > 0) $output .= $seconds . " sec";

        return trim($output);
    }
}
