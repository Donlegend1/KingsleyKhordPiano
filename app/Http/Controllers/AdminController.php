<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Community;
use App\Models\Payment;
use App\Models\Subscription;
use App\Enums\Roles\UserRoles;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    //   public function __construct()
    // {
    //     $this->middleware('auth');
    // }
    /**
     * Show the courses page.
     *
     * @return \Illuminate\View\View
     */
    public function users() 
    {
        $users = User::with('community')->paginate(10);
        return view('admin.users', compact('users'));
    }

    public function usersList(Request $request)
    {
        $query = User::with([
            'plan',
            'community',
            'subscriptions' => fn ($q) => $q->latest(),
        ]);

        // ==== SORTING ====
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        } else {
            // Default sorting: admins first, then by created_at desc
            $query->orderByRaw("CASE WHEN role = 'admin' THEN 0 ELSE 1 END")
                ->orderBy('created_at', 'desc');
        }

        $perPage = $request->get('perPage', $request->get('per_page', 10));
        $users = $query->paginate($perPage);

        $users->getCollection()->transform(function (User $user) {
            $latestSubscription = $user->subscriptions->first();
            $metadata = is_array($user->metadata) ? $user->metadata : [];

            $duration = $metadata['duration']
                ?? $latestSubscription?->duration
                ?? $user->subscription_type
                ?? null;

            $tier = $metadata['tier']
                ?? ($user->premium ? 'premium' : null);

            $paymentMethod = $user->payment_method
                ?? $latestSubscription?->payment_method
                ?? null;

            $endsAt = $user->subscription_expires_at
                ?? $latestSubscription?->ends_at;

            $user->setAttribute('subscription_details', [
                'type' => $duration,
                'tier' => $tier,
                'payment_method' => $paymentMethod,
                'status' => $user->subscription_status
                    ?? $latestSubscription?->stripe_status
                    ?? null,
                'started_at' => $user->subscription_started_at
                    ?? $latestSubscription?->created_at,
                'ends_at' => $endsAt,
            ]);

            return $user;
        });

        return response()->json($users);
    }

    public function editUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'payment_status' => 'nullable|in:successful,pending',
            'premium' => 'nullable|boolean',
            'verified' => 'nullable|boolean',
        ]);

        $user->update($validated);

        if (array_key_exists('premium', $validated) && $validated['premium']) {
            $user->forceFill(['can_access_coaching' => true])->save();
        }

        return response()->json($user);
    }

    public function destroy(Request $request, User $user)
    {
        $user->delete();

        return response()->json(['message' => "Record deleted"]);
    }

    public function updateSubscriptionAccess(Request $request, User $user)
    {
        $validated = $request->validate([
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date'],
            'paused' => ['required', 'boolean'],
        ]);

        if ($request->filled('starts_at') && $request->filled('ends_at')) {
            $start = Carbon::parse($validated['starts_at'])->startOfDay();
            $end = Carbon::parse($validated['ends_at'])->endOfDay();
            if ($end->lt($start)) {
                return response()->json([
                    'message' => 'The expiry date must be on or after the start date.',
                ], 422);
            }
        }

        $paused = $request->boolean('paused');
        $startsAt = $request->filled('starts_at')
            ? Carbon::parse($validated['starts_at'])->startOfDay()
            : $user->subscription_started_at;
        $endsAt = $request->filled('ends_at')
            ? Carbon::parse($validated['ends_at'])->endOfDay()
            : $user->subscription_expires_at;

        $expired = $endsAt && $endsAt->isPast();
        $status = $paused ? 'paused' : ($expired ? 'expired' : 'active');

        DB::transaction(function () use ($user, $startsAt, $endsAt, $status) {
            $user->forceFill([
                'subscription_started_at' => $startsAt,
                'subscription_expires_at' => $endsAt,
                'subscription_status' => $status,
            ])->save();

            if ($endsAt) {
                Subscription::query()
                    ->where('user_id', $user->id)
                    ->update(['ends_at' => $endsAt]);

                $paymentUpdates = [
                    'ends_at' => $endsAt->toDateString(),
                ];
                if ($startsAt) {
                    $paymentUpdates['starts_at'] = $startsAt->toDateString();
                }

                // user_id is a string and includes guest checkout rows.
                // Comparing it to an integer makes MySQL coerce 'guest' and abort.
                Payment::query()
                    ->where('user_id', (string) $user->id)
                    ->where('status', 'successful')
                    ->update($paymentUpdates);
            }
        });

        $message = $paused
            ? 'Subscription paused.'
            : 'Subscription dates updated.';

        return response()->json([
            'message' => $message,
            'subscription_status' => $status,
            'subscription_started_at' => $startsAt,
            'subscription_expires_at' => $endsAt,
        ]);
    }

    public function updateUserStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|string|in:active,pending,blocked'
        ]);

        Community::where('user_id', $user->id)->update([
            'status' => $request->input('status')
        ]);

        return response()->json([
            'message' => 'Community status updated successfully'
        ]);
    }
}
