<?php

namespace App\Services\EmailCampaign;

use App\Http\Requests\StoreEmailCampaignRequest;
use App\Models\EmailCampaign;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminBroadcastMail;
use App\Models\User;
use App\Models\Visitor;
use Carbon\Carbon;

class CreateEmailCampaignService
{
    public function create(StoreEmailCampaignRequest $request): EmailCampaign
    {
        $campaign = DB::transaction(function () use ($request) {

            return EmailCampaign::create([
                'subject' => $request->subject,
                'body' => $request->body,
                'targets' => $request->targets,
                'status' => $request->status,
                'sent_at' => $request->status === 'sent' ? Carbon::now() : null,
            ]);
        });

        // Dispatch emails AFTER transaction commits
        if ($request->status === 'sent') {
            $this->dispatchEmails($campaign);
        }

        return $campaign;
    }

    public function dispatchEmails(EmailCampaign $campaign): void
    {
        $emails = collect();

        if (in_array('premium', $campaign->targets)) {
            $emails = $emails->merge(
                User::where('premium', true)->pluck('email')
            );
        }

        if (in_array('standard', $campaign->targets)) {
            $emails = $emails->merge(
                User::where('premium', false)->pluck('email')
            );
        }

        if (in_array('visitor', $campaign->targets)) {
            $emails = $emails->merge(
                Visitor::pluck('email')
            );
        }

        $emails = $emails->unique()->values();

        $emails->each(function ($email, $index) use ($campaign) {

            Mail::to($email)->later(
                now()->addSeconds($index * 2),
                new AdminBroadcastMail(
                    $campaign->subject,
                    $campaign->body
                )
            );

        });
    }
}