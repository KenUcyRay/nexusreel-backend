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
        return response()->json($foods);
    }

    public function byCategory($category)
    {
        $foods = Food::where('is_available', true)
                    ->where('category', $category)
                    ->orderBy('name')
                    ->get();
        return response()->json($foods);
    }

    public function show(Food $food)
    {
        if (!$food->is_available) {
            return response()->json(['message' => 'Food not available'], 404);
        }
        return response()->json($food);
    }
}