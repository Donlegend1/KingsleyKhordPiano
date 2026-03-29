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
        DB::transaction(function () use ($emailCampaign) {

            $emailCampaign->update([
                'status' => 'sent',
                'sent_at' => Carbon::now(),
            ]);
        });

        // Dispatch emails AFTER transaction commits
        $this->emailService->dispatchEmails($emailCampaign);

        return $emailCampaign->fresh();
    }
}