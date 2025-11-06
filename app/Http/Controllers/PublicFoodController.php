<?php

namespace App\Http\Controllers;

use App\Models\Food;
use Illuminate\Http\Request;

class PublicFoodController extends Controller
{
    public function index()
    {
        $foods = Food::where('is_available', true)
                    ->orderBy('category')
                    ->orderBy('name')
                    ->get();
        
        $foods->transform(function ($food) {
            if ($food->image) {
                $food->image_url = asset('storage/' . $food->image);
            }
            return $food;
        });
        
        return response()->json($foods);
    }

    public function byCategory($category)
    {
        $foods = Food::where('is_available', true)
                    ->where('category', $category)
                    ->orderBy('name')
                    ->get();
        
        $foods->transform(function ($food) {
            if ($food->image) {
                $food->image_url = asset('storage/' . $food->image);
            }
            return $food;
        });
        
        return response()->json($foods);
    }

    public function show(Food $food)
    {
        if (!$food->is_available) {
            return response()->json(['message' => 'Food not available'], 404);
        }
        
        if ($food->image) {
            $food->image_url = asset('storage/' . $food->image);
        }
        
        return response()->json($food);
    }
}