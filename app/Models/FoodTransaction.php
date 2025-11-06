<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'customer_name',
        'customer_email',
        'total_amount',
        'amount',
        'status',
        'items',
        'snap_token'
    ];

    protected $casts = [
        'amount' => 'integer',
        'total_amount' => 'integer',
        'items' => 'array'
    ];

    public function items()
    {
        return $this->hasMany(FoodTransactionItem::class, 'transaction_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}