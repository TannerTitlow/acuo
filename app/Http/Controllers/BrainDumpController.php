<?php

namespace App\Http\Controllers;

use App\Models\BrainDump;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrainDumpController extends Controller
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
    public function show(BrainDump $brainDump): JsonResponse
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BrainDump $brainDump): JsonResponse
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BrainDump $brainDump): JsonResponse
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function process(Request $request, BrainDump $brainDump): JsonResponse
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }
}
