<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Movie;
use App\Models\Studio;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::with(['movie', 'studio'])
            ->orderBy('show_date', 'desc')
            ->orderBy('show_time', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $schedules
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'studio_id' => 'required|exists:studios,id',
            'show_date' => 'required|date|after_or_equal:today',
            'show_time' => 'required|date_format:H:i',
            'price' => 'required|numeric|min:0'
        ]);

        // Check for schedule conflicts
        $conflict = Schedule::where('studio_id', '=', $request->studio_id)
            ->where('show_date', '=', $request->show_date)
            ->where('show_time', '=', $request->show_time)
            ->exists();

        if ($conflict) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule conflict: Studio already booked at this time'
            ], 422);
        }

        $schedule = Schedule::create($request->all());
        $schedule->load(['movie', 'studio']);

        return response()->json([
            'success' => true,
            'data' => $schedule
        ], 201);
    }

    public function show(Schedule $schedule)
    {
        $schedule->load(['movie', 'studio']);
        
        return response()->json([
            'success' => true,
            'data' => $schedule
        ]);
    }

    public function update(Request $request, Schedule $schedule)
    {
        $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'studio_id' => 'required|exists:studios,id',
            'show_date' => 'required|date|after_or_equal:today',
            'show_time' => 'required|date_format:H:i',
            'price' => 'required|numeric|min:0'
        ]);

        // Check for schedule conflicts (excluding current schedule)
        $conflict = Schedule::where('studio_id', '=', $request->studio_id)
            ->where('show_date', '=', $request->show_date)
            ->where('show_time', '=', $request->show_time)
            ->where('id', '!=', $schedule->id)
            ->exists();

        if ($conflict) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule conflict: Studio already booked at this time'
            ], 422);
        }

        $schedule->update($request->all());
        $schedule->load(['movie', 'studio']);
        
        return response()->json([
            'success' => true,
            'data' => $schedule
        ]);
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Schedule deleted successfully'
        ]);
    }

    public function getByMovie($movieId)
    {
        $schedules = Schedule::with(['studio'])
            ->where('movie_id', $movieId)
            ->where('show_date', '>=', now()->toDateString())
            ->orderBy('show_date')
            ->orderBy('show_time')
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $schedules
        ]);
    }
}