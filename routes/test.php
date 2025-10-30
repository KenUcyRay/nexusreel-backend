<?php

use Illuminate\Support\Facades\Route;
use App\Models\Food;

Route::get('/test-images', function () {
    $foods = Food::whereNotNull('image')->take(3)->get();
    
    $result = [];
    foreach ($foods as $food) {
        $result[] = [
            'id' => $food->id,
            'name' => $food->name,
            'image_path' => $food->image,
            'image_url' => asset('storage/' . $food->image),
            'file_exists' => file_exists(storage_path('app/public/' . $food->image))
        ];
    }
    
    return response()->json([
        'success' => true,
        'data' => $result,
        'storage_path' => storage_path('app/public/'),
        'public_path' => public_path('storage/')
    ]);
});