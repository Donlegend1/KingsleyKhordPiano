<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Community;

class SyncUsersToCommunity extends Command
{
    protected $signature = 'community:sync-users';
    protected $description = 'Move all users not in the community table into the community table';

    public function handle()
    {
        $this->info('Syncing users to community...');

        $existingUserIds = Community::pluck('user_id')->toArray();

        $users = User::whereNotIn('id', $existingUserIds)->get();

        if ($users->isEmpty()) {
            $this->info('No new users to add.');
            return Command::SUCCESS;
        }

        foreach ($users as $user) {
            Community::create([
                'user_id' => $user->id,
                'user_name' => '@' . str_replace(' ', '', trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''))),
                'verified_status' => 0,
                'bio' => null,
                'status' => 'active',
                'social' =>[
                    'instagram' => null,
                    'x' => null,
                    'youtube' => null,
                    'facebook' => null,
                ],
            ]);
        }

        $this->info(count($users) . ' users added to community.');
        return Command::SUCCESS;
    }
}
