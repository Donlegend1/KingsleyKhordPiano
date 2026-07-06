<?php

namespace App\Services;

use App\Models\User;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ManualPaymentService
{
    /**
     * Process a manual subscription payment.
     */
    public function process($request)
    {
        $user = User::findOrFail($request->user_id);
        $startsAt = $request->starts_at ?? now();
        $endsAt = $request->ends_at ?? now()->addMonth();
        $reference = Str::uuid()->toString();

        DB::beginTransaction();
        try {

            Subscription::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'stripe_id' => 'manual_' . $reference,
                    'stripe_status' => 'active',
                    'status' => 'active',
                    'ends_at' => $endsAt,
                    'type' => 'default',
                    'payment_method' => 'Manual',
                ]
            );

            // 3) Update user profile fields
            $user->update([
                'metadata' => $request->all(),
                'premium' => $request->premium === 'premium' || $request->premium === true || $request->premium === '1',
                'payment_method' => 'Manual',
                'last_payment_reference' => $reference,
                'last_payment_amount' => $request->amount,
                'payment_status' => 'successful',
                'last_payment_at' => now(),
            ]);

            DB::commit();
            return true;

        } catch (\Throwable $e) {
            DB::rollBack();
            logger()->error('Manual Payment Service Failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
