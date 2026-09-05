<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuestBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'timezone',
        'date',
        'time',
        'focus',
        'skill_level',
        'payment_status',
        'payment_method',
        'stripe_session_id',
        'paystack_reference',
        'paypal_order_id',
        'zoom_meeting_id',
        'zoom_join_url',
        'google_meet_link',
        'google_calendar_event_id',
    ];
}
