<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Video;
use App\Models\WatchHistory;

class AdminController extends Controller
{
    // ✅ Analytics summary for Admin Dashboard
    public function analytics()
    {
        $totalUsers    = User::count();
        $totalAdmins   = User::role('admin')->count();
        $totalCreators = User::role('creator')->count();
        $totalRegulars = User::role('user')->count();

        $totalVideos   = Video::count();
        $premiumVideos = Video::where('is_premium', true)->count();

        $totalViews    = WatchHistory::count();
        $avgDuration   = Video::avg('duration') ?? 0;

        return response()->json([
            'totalUsers'    => $totalUsers,
            'totalAdmins'   => $totalAdmins,
            'totalCreators' => $totalCreators,
            'totalRegulars' => $totalRegulars,
            'totalVideos'   => $totalVideos,
            'premiumVideos' => $premiumVideos,
            'totalViews'    => $totalViews,
            'avgDuration'   => round($avgDuration, 2),
        ]);
    }
}

