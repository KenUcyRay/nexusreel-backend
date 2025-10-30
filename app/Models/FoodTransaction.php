<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_email',
        'total_amount',
        'status',
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