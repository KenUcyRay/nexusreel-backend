<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovieTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'customer_name',
        'customer_email',
        'amount',
        'status',
        'seats',
        'ticket_count',
        'schedule_id',
        'snap_token'
    ];

    protected $casts = [
        'seats' => 'array',
        'amount' => 'integer'
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id', 'id');
    }
}
