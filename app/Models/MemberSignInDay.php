<?php

namespace App\Models;

use App\Enums\Roles\UserRoles;
use Illuminate\Support\Facades\DB;

class MemberSignInDay
{
    public static function record(User $user): void
    {
        if ($user->role !== UserRoles::MEMBER->value) {
            return;
        }

        rescue(fn () => DB::table('member_sign_in_days')->insertOrIgnore([
            'date' => now(config('app.timezone'))->toDateString(),
            'user_id' => $user->getKey(),
        ]));
    }
}
