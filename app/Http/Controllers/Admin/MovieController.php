<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MovieRequest;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MovieController extends Controller
{
    public function index()
    {
        $movies = Movie::orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $movies
        ]);
    }

    public function store(MovieRequest $request)
    {
        $data = $request->validated();
        
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('movies', 'public');
        }
        
        if ($request->hasFile('trailer_file')) {
            $data['trailer_file'] = $request->file('trailer_file')->store('trailers', 'public');
        }

        $movie = Movie::create($data);

        return response()->json([
            'success' => true,
            'data' => $movie
        ], 201);
    }

    public function show(Movie $movie)
    {
        return response()->json([
            'success' => true,
            'data' => $movie
        ]);
    }

    public function update(MovieRequest $request, Movie $movie)
    {
        $data = $request->validated();
        
        if ($request->hasFile('image')) {
            if ($movie->image) {
                Storage::disk('public')->delete($movie->image);
            }
            $data['image'] = $request->file('image')->store('movies', 'public');
        }
        
        if ($request->hasFile('trailer_file')) {
            if ($movie->trailer_file) {
                Storage::disk('public')->delete($movie->trailer_file);
            }
            $data['trailer_file'] = $request->file('trailer_file')->store('trailers', 'public');
        }

        $movie->update($data);
        
        return response()->json([
            'success' => true,
            'data' => $movie
        ]);
    }

    public function destroy(Movie $movie)
    {
        if ($movie->image) {
            Storage::disk('public')->delete($movie->image);
        }
        
        if ($movie->trailer_file) {
            Storage::disk('public')->delete($movie->trailer_file);
        }
        
        $movie->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Movie deleted successfully'
        ]);
    }
}