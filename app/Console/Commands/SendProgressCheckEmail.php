<?php
namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ProgressCheckEmail;

class SendProgressCheckEmail extends Command
{
    protected $signature = 'email:send-progress-check';
    protected $description = 'Send progress check email to users who signed up 7 days ago';

    public function handle(): void
    {
        $users = User::whereDate('created_at', now()->subDays(7)->toDateString())->get();

        foreach ($users as $user) {
            Notification::route('mail', $user->email)
                ->notify(new ProgressCheckEmail($user));
        }

        $this->info("Sent progress check email to {$users->count()} user(s).");
    }
}
