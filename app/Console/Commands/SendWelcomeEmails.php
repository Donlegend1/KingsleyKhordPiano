<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Notifications\WelcomeEmailNotification;

class SendWelcomeEmails extends Command
{
    protected $signature = 'email:welcome';
    protected $description = 'Send welcome email on the first day';

    public function handle()
    {
        $this->info('Fetching users who registered today...');

        $users = User::whereDate('created_at', now()->subDay()->toDateString())->get();

        if ($users->isEmpty()) {
            $this->info('No new users found today.');
            return;
        }

        foreach ($users as $user) {
            $user->notify(new WelcomeEmailNotification($user));
            $this->line("Welcome email sent to: {$user->email}");
        }

        $this->info('All done.');
    }
}
