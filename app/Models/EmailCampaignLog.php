<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailCampaignLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'recipient_name',
        'recipient_email',
        'status',
        'error_message'
    ];

    public function campaign()
    {
        return $this->belongsTo(EmailCampaign::class, 'campaign_id');
    }
}
