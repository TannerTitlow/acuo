<?php

namespace App\Http\Controllers;

use App\Models\UserAchievement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AchievementController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function markSeen(Request $request, UserAchievement $userAchievement): JsonResponse
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }
}
