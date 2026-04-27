<?php

namespace App\Jobs;

use App\Models\EmailCampaign;
use App\Models\EmailCampaignLog;
use App\Mail\SendMailToStudents;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Exception;

class SendCampaignEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaign;
    protected $recipient;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(EmailCampaign $campaign, array $recipient)
    {
        $this->campaign = $campaign;
        $this->recipient = $recipient;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $message = !empty($this->recipient['message']) ? $this->recipient['message'] : $this->campaign->message;

            // Send the email
            Mail::to($this->recipient['email'])->send(new SendMailToStudents(
                $this->campaign->subject,
                $message,
                $this->recipient['email'],
                $this->campaign->attachment,
                $this->recipient['name'] ?? null
            ));

            // Update log entry
            EmailCampaignLog::where('campaign_id', $this->campaign->id)
                ->where('recipient_email', $this->recipient['email'])
                ->update(['status' => 'success']);

            $this->campaign->increment('sent_count');
        } catch (Exception $e) {
            \Log::error('Campaign Email Job Failed:', [
                'campaign_id' => $this->campaign->id,
                'email' => $this->recipient['email'],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Update log entry with failure
            EmailCampaignLog::where('campaign_id', $this->campaign->id)
                ->where('recipient_email', $this->recipient['email'])
                ->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage()
                ]);

            $this->campaign->increment('failed_count');
        }

        // Check if finished
        $this->campaign->refresh();
        if (($this->campaign->sent_count + $this->campaign->failed_count) >= $this->campaign->total_recipients) {
            $this->campaign->update([
                'status' => $this->campaign->failed_count > 0 ? 'completed_with_errors' : 'completed',
                'completed_at' => now(),
            ]);
        }
    }
}
