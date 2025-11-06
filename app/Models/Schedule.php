<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'movie_id',
        'studio_id',
        'show_date',
        'show_time',
        'price'
    ];

    protected $casts = [
        'show_date' => 'date',
        'show_time' => 'datetime:H:i',
        'price' => 'integer'
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function studio()
    {
        return $this->belongsTo(Studio::class);
    }
}