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
     * You can run this with: php artisan email:encouragement
     */
    protected $signature = 'email:encouragement';

    /**
     * The console command description.
     */
    protected $description = 'Send the unexpected encouragement email to eligible users';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $users = User::whereNotNull('email') 
                     ->where('created_at', '<=', now()->subDays(7)) 
                     ->get();

        if ($users->isEmpty()) {
            $this->info('No users found for encouragement email.');
            return Command::SUCCESS;
        }

        foreach ($users as $user) {
            $user->notify(new EncouragementEmail());
            $this->info("Sent encouragement email to: {$user->email}");
        }

        return Command::SUCCESS;
    }
}
