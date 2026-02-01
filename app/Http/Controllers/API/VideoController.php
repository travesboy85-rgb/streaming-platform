<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;

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
                now()->addMinutes(30),
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

        if (!$request->query('preview')) {
            $video->increment('views');
            $video->refresh();
        }

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

    // ✅ Creator: upload video
    public function upload(Request $request)
    {
        $request->validate([
            'video' => 'required|file|mimes:mp4,mov,avi|max:20000',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $path = $request->file('video')->store('videos', 'public');

        $video = Video::create([
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $path,
            'user_id' => auth()->id(),
            'status' => 'pending', // default until admin approves
        ]);

        return response()->json([
            'message' => 'Video uploaded successfully',
            'data' => $video
        ], 201);
    }

    // ✅ Creator: fetch own videos
    public function mine()
    {
        $videos = Video::where('user_id', auth()->id())->get();
        return response()->json([
            'message' => 'Your videos retrieved successfully',
            'data' => $videos
        ]);
    }

    // ✅ Admin: fetch pending videos
    public function pending()
    {
        $videos = Video::where('status', 'pending')->get();
        return response()->json([
            'message' => 'Pending videos retrieved successfully',
            'data' => $videos
        ]);
    }

    // ✅ Admin: approve video
    public function approve($id)
    {
        $video = Video::findOrFail($id);
        $video->status = 'approved';
        $video->save();

        return response()->json([
            'message' => 'Video approved successfully',
            'data' => $video
        ]);
    }
}


