<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    use HasFactory;

    protected $fillable = [
        'showtime_id',
        'seat_number',
        'row_letter',
        'is_booked',
    ];

    protected $casts = [
        'is_booked' => 'boolean',
    ];

    public function showtime()
    {
        return $this->belongsTo(Showtime::class);
    }

    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_seats');
    }
}