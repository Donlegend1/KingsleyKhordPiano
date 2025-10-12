<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Notifications\DiscoveryCallInvitationNotification;
use Carbon\Carbon;

class SendDiscoveryCallInvitations extends Command
{
    protected $signature = 'notifications:send-discovery-calls';
    protected $description = 'Send discovery call invitations to premium members who registered within the last 3 hours';

    public function handle()
    {
        $now = Carbon::now();
        $threeHoursAgo = $now->clone()->subHours(3);

        $users = User::where('premium', true) 
            ->whereBetween('created_at', [$threeHoursAgo, $now])
            ->get();

        if ($users->isEmpty()) {
            $this->info('No premium users found in the last 3 hours.');
            return Command::SUCCESS;
        }

        foreach ($users as $user) {
            $user->notify(new DiscoveryCallInvitationNotification(
                $user->first_name ?? 'there',
                'https://calendly.com/kingsleykhord/30min'
            ));
        }

        $this->info("Discovery Call Invitations sent to {$users->count()} premium members registered in the last 3 hours.");
        return Command::SUCCESS;
    }
}
