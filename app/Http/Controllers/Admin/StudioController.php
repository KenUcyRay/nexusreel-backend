<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudioRequest;
use App\Models\Studio;
use Illuminate\Http\JsonResponse;

class StudioController extends Controller
{
    public function index(): JsonResponse
    {
        $studios = Studio::all();
        
        return response()->json([
            'success' => true,
            'data' => $studios
        ]);
    }

    public function store(StudioRequest $request): JsonResponse
    {
        $studio = Studio::create($request->validated());
        
        return response()->json([
            'success' => true,
            'data' => $studio
        ], 201);
    }

    public function show(Studio $studio): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $studio
        ]);
    }

    public function update(StudioRequest $request, Studio $studio): JsonResponse
    {
        $studio->update($request->validated());
        
        return response()->json([
            'success' => true,
            'data' => $studio->fresh()
        ]);
    }

    public function destroy(Studio $studio): JsonResponse
    {
        try {
            $studio->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Studio deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}