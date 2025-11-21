<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Notifications\EncouragementEmail;

class SendEncouragementEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Run with: php artisan email:encouragement
     */
    protected $signature = 'email:encouragement';

    /**
     * The console command description.
     */
    protected $description = 'Send the encouragement email to users created within the last 7 days.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $users = User::whereNotNull('email')
            ->whereBetween('created_at', [now()->subDays(7), now()])
            ->get();

        if ($users->isEmpty()) {
            $this->info('No users found for encouragement email.');
            return Command::SUCCESS;
        }

        foreach ($users as $user) {
            $user->notify(new EncouragementEmail());
            $this->info("Sent encouragement email to: {$user->email}");
        }

        $this->info('🎉 All recent users have been notified successfully.');

        return Command::SUCCESS;
    }
}
