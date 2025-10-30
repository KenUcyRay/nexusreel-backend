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
        
        $foods->transform(function ($food) {
            if ($food->image) {
                $food->image_url = asset('storage/' . $food->image);
            }
            return $food;
        });
        
        return response()->json([
            'success' => true,
            'data' => $foods
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'is_active' => 'sometimes|boolean',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        
        // Ensure is_active is properly handled
        $validated['is_active'] = $request->has('is_active') 
            ? filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN) 
            : true;
        
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('foods', 'public');
        }
        
        $food = Food::create($validated);
        
        if ($food->image) {
            $food->image_url = asset('storage/' . $food->image);
        }
        
        return response()->json([
            'success' => true,
            'data' => $food,
            'message' => 'Food created successfully'
        ]);
    }

    public function show(Food $food)
    {
        if ($food->image) {
            $food->image_url = asset('storage/' . $food->image);
        }
        
        return response()->json([
            'success' => true,
            'data' => $food
        ]);
    }

    public function update(Request $request, Food $food)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'is_active' => 'sometimes|boolean',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        
        // Handle boolean conversion properly
        if ($request->has('is_active')) {
            $validated['is_active'] = filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN);
        }
        
        if ($request->hasFile('image')) {
            if ($food->image) {
                Storage::disk('public')->delete($food->image);
            }
            $validated['image'] = $request->file('image')->store('foods', 'public');
        }
        
        $food->update($validated);
        
        if ($food->image) {
            $food->image_url = asset('storage/' . $food->image);
        }
        
        return response()->json([
            'success' => true,
            'data' => $food,
            'message' => 'Food updated successfully'
        ]);
    }

    public function destroy(Food $food)
    {
        if ($food->image) {
            Storage::disk('public')->delete($food->image);
        }
        
        $food->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Food item deleted successfully'
        ]);
    }

    public function toggleAvailability(Request $request, Food $food)
    {
        $validated = $request->validate([
            'is_active' => 'required|boolean'
        ]);
        
        $food->update([
            'is_active' => filter_var($validated['is_active'], FILTER_VALIDATE_BOOLEAN)
        ]);
        
        return response()->json([
            'success' => true,
            'data' => $food,
            'message' => 'Food availability updated successfully'
        ]);
    }
}