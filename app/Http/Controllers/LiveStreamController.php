<?php

namespace App\Http\Controllers;

use App\Models\LiveStream;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LiveStreamController extends Controller
{
    /**
     * Get active live stream status (for frontend check)
     */
    public function status()
    {
        $activeStream = LiveStream::getActive();

        return response()->json([
            'has_active' => $activeStream !== null,
            'stream' => $activeStream ? [
                'id' => $activeStream->id,
                'title' => $activeStream->title,
                'room_id' => $activeStream->room_id,
                'viewer_count' => $activeStream->viewer_count,
                'started_at' => $activeStream->started_at->toIso8601String(),
            ] : null,
        ]);
    }

    /**
     * View live stream page
     */
    public function view($id = null)
    {
        $stream = $id
            ? LiveStream::findOrFail($id)
            : LiveStream::getActive();

        if (!$stream || $stream->status !== 'live') {
            return redirect()->route('home')->with('error', 'Không có phiên live nào đang diễn ra.');
        }

        // Get featured products for live shopping
        $products = Product::where('status', 'active')
            ->where('is_featured', true)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('frontend.live.view', compact('stream', 'products'));
    }

    /**
     * Admin: Start live stream
     */
    public function start(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $stream = LiveStream::start(
            Auth::id(),
            $request->title,
            $request->description ?? ''
        );

        // Generate Jitsi Meet room name (must be URL-safe)
        $stream->room_id = 'livestream-' . $stream->id . '-' . Str::random(8);
        $stream->save();

        return response()->json([
            'success' => true,
            'stream' => $stream,
            'jitsi_config' => [
                'room_name' => $stream->room_id,
                'domain' => config('services.jitsi.domain', 'meet.jit.si'),
            ],
        ]);
    }

    /**
     * Admin: End live stream
     */
    public function end($id)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $stream = LiveStream::findOrFail($id);

        if ($stream->started_by !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $stream->end();

        return response()->json([
            'success' => true,
            'message' => 'Đã kết thúc phiên live.',
        ]);
    }

    /**
     * Admin: Get all streams
     */
    public function index()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403);
        }

        $streams = LiveStream::with('startedBy')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('backend.livestream.index', compact('streams'));
    }

    /**
     * Admin: Create/Start live stream page
     */
    public function create()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403);
        }

        $activeStream = LiveStream::getActive();

        return view('backend.livestream.create', compact('activeStream'));
    }

    /**
     * Increment viewer count (called when user joins)
     */
    public function join($id)
    {
        $stream = LiveStream::findOrFail($id);

        if ($stream->status === 'live') {
            $stream->incrementViewers();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Decrement viewer count (called when user leaves)
     */
    public function leave($id)
    {
        $stream = LiveStream::findOrFail($id);

        if ($stream->status === 'live') {
            $stream->decrementViewers();
        }

        return response()->json(['success' => true]);
    }
}
