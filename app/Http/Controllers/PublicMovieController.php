<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;

class PublicMovieController extends Controller
{
    public function index()
    {
        $movies = Movie::where('status', 'live_now')
            ->select('id', 'name', 'genre', 'duration', 'image', 'status')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $movies
        ]);
    }
    
    public function show($id)
    {
        $movie = Movie::with(['schedules.studio'])
            ->where('id', $id)
            ->firstOrFail();
            
        return response()->json([
            'success' => true,
            'data' => $movie
        ]);
    }
}