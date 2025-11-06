<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodTransactionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'food_id',
        'food_name',
        'quantity',
        'price',
        'subtotal',
    ];

    public function transaction()
    {
        return $this->belongsTo(FoodTransaction::class, 'transaction_id');
    }

    public function food()
    {
        return $this->belongsTo(Food::class);
    }
}