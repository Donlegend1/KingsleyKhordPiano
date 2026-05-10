<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Notifications\SongBreakdownEmail;
use Carbon\Carbon;

class SendSongBreakdownToNewUsers extends Command
{
    protected $signature = 'email:send-song-breakdowns';
    protected $description = 'Send song breakdown email to newly registered users';

    public function handle()
    {
        $this->info('Fetching newly registered users...');

        $users = User::whereDate('created_at', now()->subWeeks(2)->toDateString()) 
                     ->get();

        if ($users->isEmpty()) {
            $this->info('No new users found.');
            return;
        }

        foreach ($users as $user) {
            $user->notify(new SongBreakdownEmail());
            $this->line("Email sent to: {$user->email}");
        }

        $this->info('All done.');
    }
}
