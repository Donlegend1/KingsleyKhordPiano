<?php
namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\DownloadRoadmapNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendRoadmapToNewUsers extends Command
{
    protected $signature = 'email:send-roadmap';
    protected $description = 'Send roadmap email to users who registered 24 hours ago';

    public function handle(): void
    {
        $users = User::whereDate('created_at', now()->subDay()->toDateString())->get();

        foreach ($users as $user) {
            Notification::route('mail', $user->email)
                ->notify(new DownloadRoadmapNotification($user));
        }

        $this->info("Sent roadmap email to {$users->count()} user(s).");
    }
}
