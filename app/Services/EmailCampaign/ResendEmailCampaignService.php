<?php

namespace App\Services\EmailCampaign;

use App\Models\EmailCampaign;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ResendEmailCampaignService
{
    protected CreateEmailCampaignService $emailService;

    public function __construct(CreateEmailCampaignService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function resend(EmailCampaign $emailCampaign): EmailCampaign
    {
        return DB::transaction(function () use ($emailCampaign) {

            $emailCampaign->status = 'sent';
            $emailCampaign->sent_at = Carbon::now();
            $emailCampaign->save();

            $this->emailService->dispatchEmails($emailCampaign);

            return $emailCampaign;
        });
    }
}
