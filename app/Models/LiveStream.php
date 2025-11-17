<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LiveStream extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'stream_id',
        'room_id',
        'zego_app_id',
        'zego_server_secret',
        'status', // pending, live, ended
        'started_by',
        'started_at',
        'ended_at',
        'viewer_count',
        'is_active',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'is_active' => 'boolean',
        'viewer_count' => 'integer',
    ];

    /**
     * Get the user who started the stream
     */
    public function startedBy()
    {
        return $this->belongsTo(User::class, 'started_by');
    }

    /**
     * Get active live stream
     */
    public static function getActive()
    {
        return self::where('status', 'live')
            ->where('is_active', true)
            ->orderBy('started_at', 'desc')
            ->first();
    }

    /**
     * Check if there's an active live stream
     */
    public static function hasActive()
    {
        return self::where('status', 'live')
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Start a new live stream
     */
    public static function start($userId, $title = 'Live Shopping', $description = '')
    {
        // End any existing active streams
        self::where('status', 'live')
            ->where('is_active', true)
            ->update([
                'status' => 'ended',
                'is_active' => false,
                'ended_at' => now(),
            ]);

        // Create new stream
        $stream = self::create([
            'title' => $title,
            'description' => $description,
            'status' => 'live',
            'started_by' => $userId,
            'started_at' => now(),
            'is_active' => true,
            'viewer_count' => 0,
        ]);

        return $stream;
    }

    /**
     * End the live stream
     */
    public function end()
    {
        $this->update([
            'status' => 'ended',
            'is_active' => false,
            'ended_at' => now(),
        ]);

        return $this;
    }

    /**
     * Increment viewer count
     */
    public function incrementViewers()
    {
        $this->increment('viewer_count');
    }

    /**
     * Decrement viewer count
     */
    public function decrementViewers()
    {
        if ($this->viewer_count > 0) {
            $this->decrement('viewer_count');
        }
    }
}
