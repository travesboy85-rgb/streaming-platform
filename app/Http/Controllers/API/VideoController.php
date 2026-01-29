<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class VideoController extends Controller
{
    public function index()
    {
        $videos = Video::with(['category', 'user'])
            ->latest()
            ->paginate(10);

        // Add signed playback_url to each video
        $videos->getCollection()->transform(function ($video) {
            $video->playback_url = URL::temporarySignedRoute(
                'video.stream',
                now()->addMinutes(30),   // ✅ valid for 30 minutes
                ['id' => $video->id]
            );
            return $video;
        });

        return response()->json([
            'message' => 'Videos retrieved successfully',
            'data' => $videos->items(),
            'pagination' => [
                'current_page' => $videos->currentPage(),
                'last_page' => $videos->lastPage(),
                'per_page' => $videos->perPage(),
                'total' => $videos->total(),
            ]
        ]);
    }

    public function show(Request $request, $id)
    {
        $video = Video::with(['category', 'user'])->find($id);

        if (! $video) {
            return response()->json(['message' => 'Video not found'], 404);
        }

        // ✅ Only increment views if not preview
        if (!$request->query('preview')) {
            $video->increment('views');
            $video->refresh();
        }

        // ✅ Generate signed playback URL
        $video->playback_url = URL::temporarySignedRoute(
            'video.stream',
            now()->addMinutes(30),
            ['id' => $video->id]
        );

        return response()->json([
            'message' => 'Video retrieved successfully',
            'data' => $video
        ]);
    }

    // ✅ Streaming method with signature validation
    public function stream(Request $request, $id)
    {
        if (! $request->hasValidSignature()) {
            return response()->json(['message' => 'Invalid or expired link'], 403);
        }

        $video = Video::find($id);
        if (! $video) {
            return response()->json(['message' => 'Video not found'], 404);
        }

        $path = storage_path("app/public/" . $video->file_path);
        if (!file_exists($path)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        return response()->file($path, [
            'Content-Type' => 'video/mp4',
            'Accept-Ranges' => 'bytes'
        ]);
    }

    // store(), update(), destroy() remain unchanged
}

