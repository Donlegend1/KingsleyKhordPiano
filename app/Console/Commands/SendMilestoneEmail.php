<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\MilestoneEmail;
use Illuminate\Console\Command;

class SendMilestoneEmail extends Command
{
    protected $signature = 'email:milestone';

    protected $description = 'Send milestone email to users who signed up 14 days ago';

    public function handle()
    {
        $users = User::whereDate('created_at', now()->subDays(14))->get();

        if ($users->isEmpty()) {
            $this->info("No users found who registered 14 days ago.");
            return;
        }

        foreach ($users as $user) {
            $user->notify(new MilestoneEmail());
        }

        $this->info("Milestone emails sent to {$users->count()} user(s) who joined 14 days ago.");
    }
}
