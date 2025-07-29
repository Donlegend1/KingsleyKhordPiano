<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Notifications\EarTrainingQuizEmail;

class SendEarTrainingQuizEmails extends Command
{
    protected $signature = 'emails:send-ear-training-quiz';

    protected $description = 'Send ear training quiz email to users registered within the last 14 days';

    public function handle(): int
    {
        $cutoffDate = now()->subDays(14);

        $users = User::where('created_at', '>=', $cutoffDate)->get();

        if ($users->isEmpty()) {
            $this->info('No users found.');
            return Command::SUCCESS;
        }

        foreach ($users as $user) {
            $user->notify(new EarTrainingQuizEmail());
            $this->line("Ear training email sent to: {$user->email}");
        }

        $this->info("Ear Training Quiz emails sent to {$users->count()} user(s).");

        return Command::SUCCESS;
    }
}
