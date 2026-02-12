<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class VideoController extends Controller
{
    // Public: list approved videos
    public function index()
    {
        $videos = Video::with(['category', 'user'])
            ->where('status', 'approved')
            ->latest()
            ->paginate(10);

        $videos->getCollection()->transform(function ($video) {
            $video->playback_url = URL::temporarySignedRoute(
                'video.stream',
                now()->addMinutes(30),
                ['id' => $video->id]
            );
            return $video;
        });

        return response()->json($videos->items());
    }

    // Show single video
    public function show(Request $request, $id)
    {
        $video = Video::with(['category', 'user'])->find($id);
        if (! $video) {
            return response()->json(['message' => 'Video not found'], 404);
        }

        if (!$request->query('preview')) {
            $video->increment('views');
            $video->refresh();
        }

        $video->playback_url = URL::temporarySignedRoute(
            'video.stream',
            now()->addMinutes(30),
            ['id' => $video->id]
        );

        return response()->json($video);
    }

    // Stream video file securely
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

    // Creator: upload video
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:mp4,mov,avi|max:51200',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|url',
            'rating' => 'nullable|numeric|min:0|max:5',
        ]);

        $path = $request->file('file')->store('videos', 'public');

        $video = Video::create([
    'title' => $request->title,
    'description' => $request->description,
    'thumbnail_url' => $request->thumbnail,
    'rating' => $request->rating,
    'file_path' => $path,
    'user_id' => auth()->id(),   // ✅ use this
    'status' => 'pending',
    'views' => 0,
    'likes' => 0,
]);


        $video->playback_url = URL::temporarySignedRoute(
            'video.stream',
            now()->addMinutes(30),
            ['id' => $video->id]
        );

        return response()->json([
            'message' => 'Video uploaded successfully, pending approval.',
            'video' => $video,
        ], 201);
    }

    // Creator: fetch own videos (pending + approved)
    public function mine(Request $request)
    {
        $videos = Video::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $videos->transform(function ($video) {
            $video->playback_url = URL::temporarySignedRoute(
                'video.stream',
                now()->addMinutes(30),
                ['id' => $video->id]
            );
            return $video;
        });

        // Always return an array, even if empty
        return response()->json($videos);
    }

    // Admin: fetch pending videos
    public function pending()
    {
        $videos = Video::where('status', 'pending')->get();
        return response()->json($videos);
    }

    // Admin: approve video
    public function approve($id)
    {
        $video = Video::findOrFail($id);
        $video->status = 'approved';
        $video->save();

        return response()->json($video);
    }

    // Admin: delete video
    public function destroy($id)
    {
        $video = Video::find($id);
        if (! $video) {
            return response()->json(['message' => 'Video not found'], 404);
        }

        $video->delete();
        return response()->json(['message' => 'Video deleted successfully']);
    }

    // User: like video
    public function like($id)
    {
        $video = Video::find($id);
        if (! $video) {
            return response()->json(['message' => 'Video not found'], 404);
        }

        $video->increment('likes');
        return response()->json($video);
    }
}









