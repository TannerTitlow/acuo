<?php

namespace App\Http\Controllers;

use App\Models\ScheduleBlock;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScheduleBlockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }

    /**
     * Display the specified resource.
     */
    public function show(ScheduleBlock $scheduleBlock): JsonResponse
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ScheduleBlock $scheduleBlock): JsonResponse
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ScheduleBlock $scheduleBlock): JsonResponse
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function snooze(Request $request, ScheduleBlock $scheduleBlock): JsonResponse
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }
}
