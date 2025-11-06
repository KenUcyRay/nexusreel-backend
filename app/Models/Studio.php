<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Studio extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'status',
        'rows',
        'columns',
        'total_seats'
    ];

    protected $casts = [
        'rows' => 'integer',
        'columns' => 'integer',
        'total_seats' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($studio) {
            $studio->total_seats = $studio->rows * $studio->columns;
        });

        static::deleting(function ($studio) {
            if ($studio->showtimes()->exists()) {
                throw new \Exception('Cannot delete studio that has scheduled showtimes');
            }
        });
    }

    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
    }
}