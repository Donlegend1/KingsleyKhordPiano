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
        'country',
        'passport',
        'metadata',
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
        'last_payment_amount' => 'decimal:2',
        'metadata' => 'array',
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


    public function hasActiveSubscription()
    {
        // Stripe
        if ($this->subscribed('default')) {
            return true;
        }

        // Manual
        return $this->payments()
            ->where('status', 'successful')
            ->where('ends_at', '>', now())
            ->exists();
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    /**
     * The [start, end) bounds of the user's current subscription-anniversary
     * cycle (used e.g. to reset the monthly free live coaching session).
     *
     * Each boundary is computed directly from the anchor day-of-month via
     * addMonthsNoOverflow(), not chained from the previous boundary, so a
     * short month doesn't permanently shift the anchor: Jan 31 -> Feb 28/29
     * -> Mar 31, not Feb 28 -> Mar 28.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    public function currentCoachingCycleBounds(?Carbon $now = null): array
    {
        $now = $now ?? now();
        $anchor = ($this->subscription_started_at ?? $this->created_at)->copy()->startOfDay();

        $monthsElapsed = $anchor->diffInMonths($now);

        while ($anchor->copy()->addMonthsNoOverflow($monthsElapsed)->gt($now)) {
            $monthsElapsed--;
        }
        while ($anchor->copy()->addMonthsNoOverflow($monthsElapsed + 1)->lte($now)) {
            $monthsElapsed++;
        }

        $cycleStart = $anchor->copy()->addMonthsNoOverflow($monthsElapsed);
        $cycleEnd = $anchor->copy()->addMonthsNoOverflow($monthsElapsed + 1);

        return [$cycleStart, $cycleEnd];
    }
}
