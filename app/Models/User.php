<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Cashier\Billable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
// use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, Billable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'display_name',
        'email',
        'password',
        'plan',
        'amount',
        'payment_status',
        'subscription_type',
        'subscription_payload',
        'subscription_status',
        'subscription_started_at',
        'subscription_expires_at',
        'payment_method',
        'last_payment_reference',
        'last_payment_amount',
        'last_payment_at',
        'premium',
        'can_access_coaching',
        'country',
        'passport',
        'metadata',
        'notification_preference',
        'last_login_at',
        'timezone',
        'phone_number',
        'skill_level',
        'biography',
        'instagram',
        'youtube',
        'facebook',
        'tiktok',
        'verified'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'subscription_payload' => 'array',
        'subscription_started_at' => 'datetime',
        'subscription_expires_at' => 'datetime',
        'last_payment_at' => 'datetime',
        'last_login_at' => 'datetime',
        'last_payment_amount' => 'decimal:2',
        'metadata' => 'array',
        'premium' => 'boolean',
        'can_access_coaching' => 'boolean',
    ];

    protected $appends = [
        'can_access_piano_coaching',
    ];


    public function plan()
    {
       return $this->hasOne(Plan::class, 'id', 'plan');
    }

    public function completedVideos()
    {
        return $this->belongsToMany(Course::class, 'user_video_completions')->withTimestamps();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * The name shown across the community (posts, replies, profile) —
     * a custom nickname if the user has set one, otherwise their real name.
     */
    public function getDisplayNameAttribute($value)
    {
        return $value ?: $this->full_name;
    }

    public function likedPosts()
    {
        return $this->belongsToMany(Post::class, 'post_likes')->withTimestamps();
    }

    public function community()
    {
        return $this->hasOne(Community::class);
    }

    public function messages() { return $this->hasMany(ChatMessage::class); }
    public function likes() { return $this->hasMany(Like::class); }


    public function latestLocalSubscription(): ?Subscription
    {
        return Subscription::query()
            ->where('user_id', $this->id)
            ->latest()
            ->first();
    }

    /**
     * Latest Stripe or PayPal subscription that can still be cancelled
     * (stops future renewals, access continues until period end).
     */
    public function cancellableSubscription(): ?Subscription
    {
        $subscription = $this->latestLocalSubscription();
        if (! $subscription) {
            return null;
        }

        $status = strtolower((string) $subscription->stripe_status);
        if (! in_array($status, ['active', 'trialing'], true)) {
            return null;
        }

        $userStatus = strtolower((string) ($this->subscription_status ?? ''));
        if (in_array($userStatus, ['canceled', 'cancelled'], true)) {
            return null;
        }

        $provider = $this->subscriptionProvider($subscription);
        if (! in_array($provider, ['stripe', 'paypal'], true)) {
            return null;
        }

        return $subscription;
    }

    public function subscriptionOnGracePeriod(): bool
    {
        $subscription = $this->latestLocalSubscription();
        $endsAt = $this->subscription_expires_at ?? $subscription?->ends_at;
        if (! $endsAt || $endsAt->isPast()) {
            return false;
        }

        $status = strtolower((string) (
            $this->subscription_status
            ?? $subscription?->stripe_status
            ?? ''
        ));

        return in_array($status, ['canceled', 'cancelled'], true);
    }

    public function subscriptionProvider(?Subscription $subscription = null): string
    {
        $subscription ??= $this->latestLocalSubscription();
        $method = strtolower((string) (
            $subscription?->payment_method
            ?: $this->payment_method
            ?: ''
        ));
        $gatewayId = (string) ($subscription?->stripe_id ?? '');

        if ($method === 'paypal' || str_starts_with($gatewayId, 'I-')) {
            return 'paypal';
        }

        if (in_array($method, ['paystack', 'manual'], true)) {
            return $method;
        }

        if ($method === 'stripe' || str_starts_with($gatewayId, 'sub_')) {
            return 'stripe';
        }

        return $method ?: 'stripe';
    }

    public function hasActiveSubscription(): bool
    {
        if ($this->hasActiveLocalSubscription()) {
            return true;
        }

        if ($this->hasActiveEntitlementWindow()) {
            return true;
        }

        return $this->payments()
            ->where('status', 'successful')
            ->whereNotNull('ends_at')
            ->where('ends_at', '>', now())
            ->exists();
    }

    /**
     * Stripe, PayPal, Paystack, and manual rows all live on `subscriptions`
     * and share stripe_status / ends_at.
     */
    protected function hasActiveLocalSubscription(): bool
    {
        return Subscription::query()
            ->where('user_id', $this->id)
            ->where(function ($query) {
                $query->where(function ($active) {
                    $active->whereIn('stripe_status', ['active', 'trialing'])
                        ->where(function ($period) {
                            $period->whereNull('ends_at')
                                ->orWhere('ends_at', '>', now());
                        });
                })->orWhere(function ($grace) {
                    $grace->whereIn('stripe_status', ['canceled', 'cancelled'])
                        ->where('ends_at', '>', now());
                });
            })
            ->exists();
    }

    /**
     * PayPal (and some Stripe webhooks) stamp period end on the user.
     * Canceled-at-period-end still has access until subscription_expires_at.
     */
    protected function hasActiveEntitlementWindow(): bool
    {
        if (! $this->subscription_expires_at || $this->subscription_expires_at->isPast()) {
            return false;
        }

        $status = strtolower((string) ($this->subscription_status ?? ''));

        if (in_array($status, ['past_due', 'expired', 'failed', 'incomplete', 'pending'], true)) {
            return false;
        }

        return in_array($status, ['active', 'trialing', 'canceled', 'cancelled'], true)
            || $this->payment_status === 'successful';
    }

    public function hasPendingStripeCheckout(): bool
    {
        return Subscription::query()
            ->where('user_id', $this->id)
            ->whereIn('stripe_status', ['pending', 'incomplete'])
            ->where(function ($query) {
                $query->whereNull('payment_method')
                    ->orWhereRaw('LOWER(payment_method) = ?', ['stripe']);
            })
            ->exists();
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    public function liveCoachingBookings()
    {
        return $this->hasMany(LiveCoachingBooking::class);
    }

    /**
     * Piano coaching is limited to legacy Premium members.
     * New Premium ($45 / €39 / ₦78,000) does not include it.
     */
    public function canAccessPianoCoaching(): bool
    {
        if (! (bool) ($this->attributes['can_access_coaching'] ?? false)) {
            return false;
        }

        return (bool) $this->premium || $this->hasActiveSubscription();
    }

    public function getCanAccessPianoCoachingAttribute(): bool
    {
        return $this->canAccessPianoCoaching();
    }
}
