<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'time',
        'is_booked',
    ];
}
