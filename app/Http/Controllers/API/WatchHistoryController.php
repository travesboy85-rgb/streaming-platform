<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\WatchHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WatchHistoryController extends Controller
{
    /**
     * Display the authenticated user's watch history.
     */
    public function index()
    {
        $user = Auth::user();

        $history = WatchHistory::where('user_id', $user->id)
            ->with('video')
            ->latest()
            ->get();

        return response()->json([
            'watch_history' => $history
        ]);
    }

    /**
     * Store a new watch history entry.
     */
    public function store(Request $request, $videoId)
    {
        $user = Auth::user();

        $history = WatchHistory::create([
            'user_id' => $user->id,
            'video_id' => $videoId,
            'watched_at' => now(),
        ]);

        return response()->json([
            'message' => 'Video added to watch history',
            'entry' => $history
        ]);
    }
}
