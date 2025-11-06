<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScheduleRequest;
use App\Models\Schedule;
use Illuminate\Http\JsonResponse;

class ScheduleController extends Controller
{
    public function index(): JsonResponse
    {
        $schedules = Schedule::with(['movie', 'studio'])->get();
        
        return response()->json([
            'success' => true,
            'data' => $schedules
        ]);
    }

    public function store(ScheduleRequest $request): JsonResponse
    {
        // Check for schedule conflicts
        $conflict = Schedule::where('studio_id', $request->studio_id)
            ->where('show_date', $request->show_date)
            ->where('show_time', $request->show_time)
            ->exists();

        if ($conflict) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule conflict: Studio already booked at this time'
            ], 422);
        }

        $schedule = Schedule::create($request->validated());
        $schedule->load(['movie', 'studio']);
        
        return response()->json([
            'success' => true,
            'data' => $schedule
        ], 201);
    }

    public function show(Schedule $schedule): JsonResponse
    {
        $schedule->load(['movie', 'studio']);
        
        return response()->json([
            'success' => true,
            'data' => $schedule
        ]);
    }

    public function update(ScheduleRequest $request, Schedule $schedule): JsonResponse
    {
        // Check for schedule conflicts (excluding current schedule)
        $conflict = Schedule::where('studio_id', $request->studio_id)
            ->where('show_date', $request->show_date)
            ->where('show_time', $request->show_time)
            ->where('id', '!=', $schedule->id)
            ->exists();

        if ($conflict) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule conflict: Studio already booked at this time'
            ], 422);
        }

        $schedule->update($request->validated());
        $schedule->load(['movie', 'studio']);
        
        return response()->json([
            'success' => true,
            'data' => $schedule
        ]);
    }

    public function destroy(Schedule $schedule): JsonResponse
    {
        $schedule->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Schedule deleted successfully'
        ]);
    }

    public function getByMovie($movieId): JsonResponse
    {
        $schedules = Schedule::with('studio')
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