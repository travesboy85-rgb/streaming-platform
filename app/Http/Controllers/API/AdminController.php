<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Video;
use App\Models\WatchHistory;

class AdminController extends Controller
{
    // ✅ Simplified Analytics summary
    public function analytics()
    {
        return response()->json([
            'totalUsers'  => User::count(),
            'totalVideos' => Video::count(),
            'totalViews'  => WatchHistory::count(),
            'totalLikes'  => Video::sum('likes'),
        ]);
    }
}


