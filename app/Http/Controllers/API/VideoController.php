<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class VideoController extends Controller
{
    // ✅ Public: list approved videos
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

        return response()->json($videos->items()); // raw list for Retrofit
    }

    // ✅ Show single video
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

    // ✅ Stream video file securely
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
            'video' => 'required|file|mimes:mp4,mov,avi|max:51200',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $path = $request->file('video')->store('videos', 'public');

        $video = Video::create([
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $path,
            'user_id' => $request->user()->id, // ✅ safer with Sanctum
            'status' => 'pending',
            'views' => 0,
            'likes' => 0,
        ]);

        return response()->json($video, 201);
    }

    // ✅ Creator: fetch own videos
    public function mine(Request $request)
    {
        $videos = Video::where('user_id', $request->user()->id)->get();
        return response()->json($videos);
    }

    // ✅ Admin: fetch pending videos
    public function pending()
    {
        $videos = Video::where('status', 'pending')->get();
        return response()->json($videos);
    }

    // ✅ Admin: approve video
    public function approve($id)
    {
        $video = Video::findOrFail($id);
        $video->status = 'approved';
        $video->save();

        return response()->json($video);
    }

    // ✅ Admin: delete video
    public function destroy($id)
    {
        $video = Video::find($id);
        if (! $video) {
            return response()->json(['message' => 'Video not found'], 404);
        }

        $video->delete();
        return response()->json(['message' => 'Video deleted successfully']);
    }

    // ✅ User: like video
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





