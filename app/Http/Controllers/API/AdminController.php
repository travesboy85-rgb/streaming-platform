<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Video;
use App\Models\WatchHistory;

class AdminController extends Controller
{
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
            'status'         => 'success',
            'message'        => 'Analytics data retrieved successfully',
            'total_users'    => $totalUsers,
            'total_admins'   => $totalAdmins,
            'total_creators' => $totalCreators,
            'total_regulars' => $totalRegulars,
            'total_videos'   => $totalVideos,
            'premium_videos' => $premiumVideos,
            'total_views'    => $totalViews,
            'avg_duration'   => round($avgDuration, 2),
        ]);
    }
}
