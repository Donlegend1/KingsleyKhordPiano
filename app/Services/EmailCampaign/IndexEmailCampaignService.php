<?php

namespace App\Services\EmailCampaign;

use App\Models\EmailCampaign;
use Illuminate\Database\Eloquent\Collection;

class IndexEmailCampaignService
{
    public function all(): array
    {
        $campaigns = EmailCampaign::latest()->get();

        // Initialize counts
        $counts = [
            'premium' => 0,
            'standard' => 0,
            'visitor' => 0,
        ];

        // Count targets
        foreach ($campaigns as $campaign) {
            if (in_array('premium', $campaign->targets ?? [])) {
                $counts['premium']++;
            }
            if (in_array('standard', $campaign->targets ?? [])) {
                $counts['standard']++;
            }
            if (in_array('visitor', $campaign->targets ?? [])) {
                $counts['visitor']++;
            }
        }

        return [
            'campaigns' => $campaigns,
            'counts' => $counts,
        ];
    }
}
