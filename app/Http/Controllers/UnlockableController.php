<?php

namespace App\Http\Controllers;

use App\Models\Unlockable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UnlockableController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function activate(Request $request, Unlockable $unlockable): JsonResponse
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }
}
