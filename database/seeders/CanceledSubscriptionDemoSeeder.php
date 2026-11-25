<?php

namespace Database\Seeders;

use App\Enums\Roles\UserRoles;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CanceledSubscriptionDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            return;
        }

        foreach ([
            ['Amina', 'Lawal', 'NG', 'standard', 'monthly'],
            ['Jordan', 'Miles', 'US', 'standard', 'yearly'],
            ['Ama', 'Owusu', 'GH', 'premium', 'quarterly'],
        ] as [$firstName, $lastName, $country, $tier, $duration]) {
            $email = strtolower("$firstName.$lastName") . '@canceled-demo.test';
            if (User::withTrashed()->where('email', $email)->exists()) {
                continue;
            }

            $user = new User();
            $user->forceFill([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'password' => Hash::make(Str::random(48)),
                'role' => UserRoles::MEMBER->value,
                'country' => $country,
                'email_verified_at' => now(),
                'subscription_status' => 'canceled',
                'subscription_type' => $duration,
                'subscription_started_at' => now()->subYear(),
                'subscription_expires_at' => now()->subDay(),
                'metadata' => compact('tier', 'duration'),
            ])->saveQuietly();
        }
    }
}
