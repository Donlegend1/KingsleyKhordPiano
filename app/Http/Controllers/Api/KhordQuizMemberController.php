<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Lets Khord Quiz confirm that someone is a subscribed member of
 * Kingsley Khord Piano and pull the details it needs to create (or
 * update) their Khord Quiz account and subscription.
 */
class KhordQuizMemberController extends Controller
{
    /** @var array<int, Plan|null> */
    protected array $planCache = [];

    /**
     * GET /api/khord-quiz/members
     *
     * Bulk sync: every member (role = member) so Khord Quiz can create the
     * same accounts and subscribe the ones that are subscribed here.
     *
     * Query:
     *   since     ISO-8601 date; only members created/updated at or after it
     *             (use the previous response's `synced_at` for incremental sync)
     *   page      page number (default 1)
     *   per_page  1–500 (default 100)
     */
    public function index(Request $request): JsonResponse
    {
        $data = $request->validate([
            'since' => ['nullable', 'date'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:500'],
        ]);

        $syncedAt = now();

        $members = User::query()
            ->where('role', 'member')
            ->when(! empty($data['since']), fn ($q) => $q->where('updated_at', '>=', Carbon::parse($data['since'])))
            ->orderBy('id')
            ->paginate($data['per_page'] ?? 100);

        $this->planCache = Plan::query()
            ->whereIn('id', $members->getCollection()->pluck('plan')->filter(fn ($id) => is_numeric($id))->unique())
            ->get()
            ->keyBy('id')
            ->all();

        return response()->json([
            'synced_at' => $syncedAt->toIso8601String(),
            'data' => $members->getCollection()->map(fn (User $user) => $this->syncPayload($user))->values(),
            'meta' => [
                'current_page' => $members->currentPage(),
                'last_page' => $members->lastPage(),
                'per_page' => $members->perPage(),
                'total' => $members->total(),
                'has_more' => $members->hasMorePages(),
            ],
        ]);
    }

    /**
     * GET /api/khord-quiz/members/{email}
     *
     * Single member (subscribed or not) in the same shape as the bulk sync,
     * for when Khord Quiz needs to create one user on demand.
     */
    public function show(string $email): JsonResponse
    {
        $user = User::query()
            ->where('role', 'member')
            ->whereRaw('LOWER(email) = ?', [strtolower(trim($email))])
            ->first();

        if (! $user) {
            return response()->json(['message' => 'No Kingsley Khord Piano member found with this email.'], 404);
        }

        return response()->json(['data' => $this->syncPayload($user)]);
    }

    /**
     * Everything Khord Quiz needs to create an identical account: profile,
     * the bcrypt password hash (so the member logs in with the same
     * password), and subscription state.
     */
    protected function syncPayload(User $user): array
    {
        $subscribed = $user->has_premium_access;

        return [
            'member' => $this->memberPayload($user) + [
                'password_hash' => $user->password,
                'email_verified_at' => optional($user->email_verified_at)->toIso8601String(),
                'created_at' => optional($user->created_at)->toIso8601String(),
                'updated_at' => optional($user->updated_at)->toIso8601String(),
            ],
            'subscribed' => $subscribed,
            'subscription' => $subscribed ? $this->subscriptionPayload($user) : null,
        ];
    }

    /**
     * POST /api/khord-quiz/members/verify
     *
     * Body: { "email": "...", "password": "..." (optional) }
     *
     * 200 – member exists and has an active subscription (details returned)
     * 401 – password supplied but wrong
     * 403 – member exists but has no active subscription
     * 404 – no member with that email
     */
    public function verify(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [strtolower(trim($data['email']))])
            ->first();

        if (! $user) {
            return response()->json([
                'subscribed' => false,
                'message' => 'No Kingsley Khord Piano member found with this email.',
            ], 404);
        }

        if (! empty($data['password']) && ! Hash::check($data['password'], (string) $user->password)) {
            return response()->json([
                'subscribed' => false,
                'message' => 'Invalid credentials.',
            ], 401);
        }

        if (! $user->has_premium_access) {
            return response()->json([
                'subscribed' => false,
                'message' => 'This member does not have an active subscription.',
            ], 403);
        }

        return response()->json([
            'subscribed' => true,
            'member' => $this->memberPayload($user),
            'subscription' => $this->subscriptionPayload($user),
        ]);
    }

    protected function memberPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'display_name' => $user->display_name,
            'email' => $user->email,
            'email_verified' => $user->email_verified_at !== null,
            'phone_number' => $user->phone_number,
            'country' => $user->country,
            'timezone' => $user->timezone,
            'skill_level' => $user->skill_level,
        ];
    }

    protected function subscriptionPayload(User $user): array
    {
        $local = $user->latestLocalSubscription();
        // `plan` is both a column (plan id) and a relation name on User,
        // so resolve the plan explicitly from the stored id.
        $planId = $user->getAttribute('plan');
        $plan = null;
        if (is_numeric($planId)) {
            $plan = array_key_exists((int) $planId, $this->planCache)
                ? $this->planCache[(int) $planId]
                : ($this->planCache[(int) $planId] = Plan::query()->find($planId));
        }

        $expiresAt = $user->subscription_expires_at ?? $local?->ends_at;

        return [
            'status' => $user->subscription_status ?: ($local?->stripe_status ?? ($user->premium ? 'active' : null)),
            'type' => $user->subscription_type,
            'provider' => $user->subscriptionProvider($local),
            'is_lifetime' => (bool) $user->premium && ! $expiresAt,
            'started_at' => optional($user->subscription_started_at ?? $local?->created_at)->toIso8601String(),
            'expires_at' => optional($expiresAt)->toIso8601String(),
            'plan' => $plan ? [
                'id' => $plan->id,
                'tier' => $plan->tier,
                'type' => $plan->type,
                'slug' => $plan->slug,
                'is_premium' => $plan->isPremium(),
            ] : null,
        ];
    }
}
