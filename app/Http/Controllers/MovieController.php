<?php

namespace App\Http\Controllers;

use App\Models\Movie;

class MovieController extends Controller
{
    public function index()
    {
        $movies = Movie::all();
        return response()->json([
            'success' => true,
            'data' => $movies
        ]);
    }

    public function show($id)
    {
        $movie = Movie::with(['schedules.studio'])->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $movie
        ]);
    }
}