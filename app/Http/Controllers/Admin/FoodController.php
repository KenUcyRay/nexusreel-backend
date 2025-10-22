<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Food;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FoodController extends Controller
{
    public function index()
    {
        $foods = Food::orderBy('created_at', 'desc')->get();
        return response()->json($foods);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'category' => 'nullable|in:snack,drink,combo'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('foods', 'public');
        }

        $food = Food::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $imagePath,
            'category' => $request->category ?? 'snack',
            'is_available' => $request->boolean('is_available', true)
        ]);

        return response()->json($food, 201);
    }

    public function show(Food $food)
    {
        return response()->json($food);
    }

    public function update(Request $request, Food $food)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'category' => 'nullable|in:snack,drink,combo'
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category' => $request->category ?? $food->category,
            'is_available' => $request->boolean('is_available', $food->is_available)
        ];

        if ($request->hasFile('image')) {
            // Delete old image
            if ($food->image && Storage::exists('public/' . $food->image)) {
                Storage::delete('public/' . $food->image);
            }
            
            $data['image'] = $request->file('image')->store('foods', 'public');
        }

        $food->update($data);
        return response()->json($food);
    }

    public function destroy(Food $food)
    {
        // Delete image file
        if ($food->image && Storage::exists('public/' . $food->image)) {
            Storage::delete('public/' . $food->image);
        }

        $food->delete();
        return response()->json(['message' => 'Food deleted successfully']);
    }
}