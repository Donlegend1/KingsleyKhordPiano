<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Community;
use App\Models\Payment;
use App\Models\Subscription;
use App\Queries\UserSubscriptionSummary;
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
    public function users(Request $request)
    {
        $this->authorizeUserManagement($request);

        return view('admin.users');
    }

    public function usersList(Request $request)
    {
        $this->authorizeUserManagement($request);

        $query = User::query()->fromSub(UserSubscriptionSummary::query(), 'users')->with([
            'plan',
            'community',
            'subscriptions' => fn ($q) => $q->latest(),
        ]);

        $status = $request->string('status')->toString();
        $planFilter = $request->string('plan')->toString();
        if (in_array($status, ['Active', 'Inactive', 'Canceled'], true)) {
            $query->where('display_subscription_status', $status);
        }
        if (in_array($planFilter, ['standard-monthly', 'standard-quarterly', 'standard-yearly', 'premium-monthly', 'premium-quarterly', 'premium-yearly', 'manual', 'none'], true)) {
            $query->where('display_plan', $planFilter);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT_WS(' ', first_name, last_name) LIKE ?", ["%{$search}%"]);
            });
        }

        // Admins first, then newest accounts. Always ordered so pages don't overlap.
        $query->orderByRaw("CASE WHEN role = 'admin' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc')->orderByDesc('id');

        $perPage = min(max((int) $request->get('perPage', $request->get('per_page', 10)), 1), 100);
        $users = $query->paginate($perPage);
        $countryNames = DB::table('countries')->pluck('country_name', 'country_code');

        $users->getCollection()->transform(function (User $user) use ($countryNames) {
            $latestSubscription = $user->subscriptions->first();
            $metadata = is_array($user->metadata) ? $user->metadata : [];
            $plan = $user->getRelation('plan');

            $duration = $metadata['duration']
                ?? $latestSubscription?->duration
                ?? $user->subscription_type
                ?? $plan?->type;

            $tier = $metadata['tier']
                ?? $plan?->tier
                ?? ($user->premium ? 'premium' : ($duration ? 'standard' : null));

            $paymentMethod = $user->payment_method
                ?? $latestSubscription?->payment_method
                ?? null;

            $endsAt = $user->subscription_expires_at
                ?? $latestSubscription?->ends_at;
            $status = strtolower((string) ($user->subscription_status ?? $latestSubscription?->stripe_status));
            $country = trim((string) ($metadata['signup_country'] ?? $user->country ?? ''));

            $user->setAttribute('subscription_details', [
                'type' => $duration,
                'tier' => $tier,
                'payment_method' => $paymentMethod,
                'status' => $status ?: null,
                'display_status' => $user->display_subscription_status,
                'started_at' => $user->subscription_started_at
                    ?? $latestSubscription?->created_at,
                'ends_at' => $endsAt,
            ]);
            $user->setAttribute('country_name', $countryNames[strtoupper($country)] ?? ($country ?: null));

            return $user;
        });

        return response()->json($users);
    }

    public function editUser(Request $request, User $user)
    {
        $this->authorizeUserManagement($request);

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
        $this->authorizeUserManagement($request);
        abort_if($request->user()->is($user), 403);
        $user->delete();

        return response()->json(['message' => "Record deleted"]);
    }

    public function updateSubscriptionAccess(Request $request, User $user)
    {
        $this->authorizeUserManagement($request);

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

    private function authorizeUserManagement(Request $request): void
    {
        abort_unless(in_array($request->user()?->role, [UserRoles::ADMIN->value, UserRoles::SUPERADMIN->value], true), 403);
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
