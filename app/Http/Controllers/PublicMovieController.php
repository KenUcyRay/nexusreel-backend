<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;

class PublicMovieController extends Controller
{
    public function index()
    {
        $movies = Movie::orderBy('created_at', 'desc')->get();
        return response()->json($movies);
    }

    public function comingSoon()
    {
        $movies = Movie::where('status', 'coming_soon')
                      ->orderBy('created_at', 'desc')
                      ->get();
        return response()->json($movies);
    }

    public function liveNow()
    {
        $movies = Movie::where('status', 'live_now')
                      ->orderBy('created_at', 'desc')
                      ->get();
        return response()->json($movies);
    }

    public function show(Movie $movie)
    {
        return response()->json($movie);
    }
}