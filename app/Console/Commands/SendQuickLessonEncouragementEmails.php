<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Notifications\QuickLessonEncouragementEmail;
use Carbon\Carbon;

class SendQuickLessonEncouragementEmails extends Command
{
    protected $signature = 'emails:send-quick-lessons';

    protected $description = 'Send quick lesson encouragement email to users registered in the last 14 days';

    public function handle(): int
    {
        $cutoffDate = now()->subDays(14);

        $users = User::where('created_at', '>=', $cutoffDate)->get();

        if ($users->isEmpty()) {
            $this->info('No users found.');
            return Command::SUCCESS;
        }

        foreach ($users as $user) {
            $user->notify(new QuickLessonEncouragementEmail());
            $this->line("Email sent to: {$user->email}");
        }

        $this->info("Quick Lesson Encouragement emails sent to {$users->count()} user(s).");

        return Command::SUCCESS;
    }
}
