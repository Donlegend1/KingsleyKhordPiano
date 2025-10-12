<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Notifications\CommunityInviteEmail;

class SendCommunityInviteEmail extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'email:community-invite';

    /**
     * The console command description.
     */
    protected $description = 'Send the community invite email to eligible users';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $link = config('app.community_link') ?? 'kingsleykhordpiano.com/community';

        $users = User::whereNotNull('email')
                     ->where('created_at', '<=', now()->subDays(3)) 
                     ->get();

        if ($users->isEmpty()) {
            $this->info('No users found to invite.');
            return Command::SUCCESS;
        }

        foreach ($users as $user) {
            $user->notify(new CommunityInviteEmail($link));
            $this->info("Invited: {$user->email}");
        }

        return Command::SUCCESS;
    }
}
