<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\ReEngagementEmail;
use Illuminate\Console\Command;

class SendReengagementEmails extends Command
{
    protected $signature = 'email:reengagement';

    protected $description = 'Send re-engagement emails to users inactive for 7+ days';

    public function handle()
    {
        $users = User::where('last_login_at', '<=', now()->subDays(7))
                     ->orWhereNull('last_login_at')
                     ->get();

        if ($users->isEmpty()) {
            $this->info('No inactive users found.');
            return;
        }

        foreach ($users as $user) {
            $user->notify(new ReEngagementEmail());
        }

        $this->info("Sent re-engagement emails to {$users->count()} user(s).");
    }
}
