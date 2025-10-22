<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Showtime;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function index()
    {
        $movies = Movie::with('showtimes')->get();
        return response()->json($movies);
    }

    public function show($id)
    {
        $movie = Movie::with('showtimes')->findOrFail($id);
        return response()->json($movie);
    }

    public function showtimes($id)
    {
        $showtimes = Showtime::where('movie_id', $id)
            ->with('seats')
            ->get();
        return response()->json($showtimes);
    }
}