<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image',
        'duration',
        'genre',
        'rating',
        'status',
        'director',
        'production_team',
        'trailer_type',
        'trailer_url',
        'trailer_file',
        'price'
    ];

    protected $casts = [
        'duration' => 'integer'
    ];

    protected $appends = ['title'];

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    // Accessor untuk konsistensi dengan frontend
    public function getTitleAttribute()
    {
        return $this->name;
    }
}