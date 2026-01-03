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
        return DB::transaction(function () use ($request) {

            $campaign = EmailCampaign::create([
                'subject' => $request->subject,
                'body' => $request->body,
                'targets' => $request->targets,
                'status' => $request->status,
                'sent_at' => $request->status === 'sent' ? Carbon::now() : null,
            ]);

            if ($request->status === 'sent') {
                $this->dispatchEmails($campaign);
            }

            return $campaign;
        });
    }

    public function dispatchEmails(EmailCampaign $campaign): void
    {
     
     $emails = collect([
         'shedrackogwuche5@gmail.com',
     ]);
        // $emails = collect();

        // if (in_array('premium', $campaign->targets)) {
        //     $emails = $emails->merge(
        //         User::where('premium', true)->pluck('email')
        //     );
        // }

        // if (in_array('standard', $campaign->targets)) {
        //     $emails = $emails->merge(
        //         User::where('premium', false)->pluck('email')
        //     );
        // }

        // if (in_array('visitor', $campaign->targets)) {
        //     $emails = $emails->merge(
        //      Visitor::pluck('email')
        //     );
        // }

        $emails
            ->unique()
            ->chunk(50)
            ->each(function ($chunk) use ($campaign) {
                foreach ($chunk as $email) {
                    Mail::to($email)->queue(
                        new AdminBroadcastMail(
                            $campaign->subject,
                            $campaign->body
                        )
                    );
                }
            });
    }
}
