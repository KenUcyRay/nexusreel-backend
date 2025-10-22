<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MovieController extends Controller
{
    public function index()
    {
        $movies = Movie::orderBy('created_at', 'desc')->get();
        return response()->json($movies);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'genre' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:coming_soon,live_now'
        ]);

        $imagePath = $request->file('image')->store('movies', 'public');

        $movie = Movie::create([
            'name' => $request->name,
            'genre' => $request->genre,
            'duration' => $request->duration,
            'image' => $imagePath,
            'status' => $request->status
        ]);

        return response()->json($movie, 201);
    }

    public function show(Movie $movie)
    {
        return response()->json($movie);
    }

    public function update(Request $request, Movie $movie)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'genre' => 'required|string|max:255', 
            'duration' => 'required|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:coming_soon,live_now'
        ]);

        $data = [
            'name' => $request->name,
            'genre' => $request->genre,
            'duration' => $request->duration,
            'status' => $request->status
        ];

        if ($request->hasFile('image')) {
            if ($movie->image) {
                Storage::disk('public')->delete($movie->image);
            }
            $data['image'] = $request->file('image')->store('movies', 'public');
        }

        $movie->update($data);
        return response()->json($movie);
    }

    public function destroy(Movie $movie)
    {
        if ($movie->image) {
            Storage::disk('public')->delete($movie->image);
        }
        
        $movie->delete();
        return response()->json(['message' => 'Movie deleted successfully']);
    }
}