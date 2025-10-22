<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'customer_email',
        'total_amount',
        'status',
    ];

    public function items()
    {
        return $this->hasMany(FoodTransactionItem::class, 'transaction_id');
    }
}