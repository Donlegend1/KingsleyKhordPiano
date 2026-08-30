<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PianoCoachingMembersController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('q', ''));

        $members = $this->coachingMembersQuery($search)
            ->withCount('liveCoachingBookings')
            ->paginate(20)
            ->withQueryString();

        return view('admin.piano_coaching.index', [
            'members' => $members,
            'search' => $search,
        ]);
    }

    public function eligible(Request $request)
    {
        $search = trim((string) $request->input('q', ''));

        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $users = $this->eligibleSubscribersQuery($search)
            ->limit(20)
            ->get()
            ->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->full_name,
                'email' => $user->email,
                'plan' => $this->planLabel($user),
            ]);

        return response()->json($users);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);

        if ($user->can_access_coaching) {
            return back()->with('error', 'This member already has piano coaching access.');
        }

        if (! $user->premium && ! $user->hasActiveSubscription()) {
            return back()->with('error', 'Only subscribed members can be added to piano coaching.');
        }

        $user->forceFill(['can_access_coaching' => true])->save();

        return back()->with('success', "{$user->full_name} can now book piano coaching.");
    }

    public function destroy(User $user)
    {
        $user->forceFill(['can_access_coaching' => false])->save();

        return back()->with('success', "{$user->full_name} was removed from piano coaching.");
    }

    protected function coachingMembersQuery(string $search)
    {
        return User::query()
            ->where('can_access_coaching', true)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('first_name')
            ->orderBy('last_name');
    }

    protected function eligibleSubscribersQuery(string $search)
    {
        return User::query()
            ->where(function ($query) {
                $query->whereNull('can_access_coaching')
                    ->orWhere('can_access_coaching', false);
            })
            ->where(function ($query) {
                $query->where('premium', true)
                    ->orWhereIn('subscription_status', ['active', 'trialing'])
                    ->orWhere('payment_status', 'successful');
            })
            ->where(function ($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('first_name');
    }

    protected function planLabel(User $user): string
    {
        $tier = strtolower((string) (
            data_get($user->metadata, 'tier')
            ?? ($user->premium ? 'premium' : 'standard')
        ));
        $duration = strtolower((string) (
            data_get($user->metadata, 'duration')
            ?? $user->subscription_type
            ?? ''
        ));

        $tierLabel = str_contains($tier, 'premium') ? 'Premium' : 'Standard';
        $durationLabel = match ($duration) {
            'month', 'monthly' => 'Monthly',
            'quarter', 'quarterly' => 'Quarterly',
            'year', 'yearly' => 'Yearly',
            default => null,
        };

        return trim($tierLabel . ($durationLabel ? " · {$durationLabel}" : ''));
    }
}
