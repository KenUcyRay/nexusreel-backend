<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;

    protected $table = 'foods';

    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        'stock',
        'is_active',
        'is_available',
        'category'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_available' => 'boolean',
        'price' => 'decimal:2'
    ];
}