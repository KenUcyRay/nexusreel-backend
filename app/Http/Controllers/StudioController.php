<?php

namespace App\Http\Controllers;

use App\Models\Studio;
use Illuminate\Http\Request;

class StudioController extends Controller
{
    public function index()
    {
        $studios = Studio::all();
        return response()->json([
            'success' => true,
            'data' => $studios
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'rows' => 'required|integer|min:1',
            'columns' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive'
        ]);

        $validated['total_seats'] = $validated['rows'] * $validated['columns'];
        
        $studio = Studio::create($validated);
        
        return response()->json([
            'success' => true,
            'data' => $studio
        ], 201);
    }

    public function show($id)
    {
        $studio = Studio::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $studio
        ]);
    }

    public function update(Request $request, $id)
    {
        $studio = Studio::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'rows' => 'required|integer|min:1',
            'columns' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive'
        ]);

        $validated['total_seats'] = $validated['rows'] * $validated['columns'];
        
        $studio->update($validated);
        
        return response()->json([
            'success' => true,
            'data' => $studio
        ]);
    }

    public function destroy($id)
    {
        $studio = Studio::findOrFail($id);
        $studio->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Studio deleted successfully'
        ]);
    }
}