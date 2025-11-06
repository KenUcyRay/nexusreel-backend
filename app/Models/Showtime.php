<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Showtime extends Model
{
    use HasFactory;

    protected $fillable = [
        'movie_id',
        'studio_id',
        'cinema_hall',
        'show_date',
        'show_time',
        'available_seats',
    ];

    protected $casts = [
        'show_date' => 'date',
        'show_time' => 'datetime:H:i',
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function seats()
    {
        return $this->hasMany(Seat::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function studio()
    {
        return $this->belongsTo(Studio::class);
    }
}