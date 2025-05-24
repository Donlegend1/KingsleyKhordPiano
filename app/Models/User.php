<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',

        // Custom subscription/payment fields
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


    function plan() {
    return $this->hasOne(Subscription::class, 'id', 'plan');
        
    }

    public function completedVideos()
    {
        return $this->belongsToMany(Course::class, 'user_video_completions')->withTimestamps();
    }
}
