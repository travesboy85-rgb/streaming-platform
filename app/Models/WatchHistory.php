<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WatchHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'video_id',
        'watched_seconds',
        'completion_percentage',
        'completed',
        'last_watched_at'
    ];

    protected $casts = [
        'watched_seconds' => 'integer',
        'completed' => 'boolean',
        'last_watched_at' => 'datetime'
    ];

    // Custom setter for completion_percentage
    public function setCompletionPercentageAttribute($value)
    {
        $this->attributes['completion_percentage'] = round((float) $value, 2);
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function video()
    {
        return $this->belongsTo(Video::class);
    }

    // Helper method to update watch progress
    public function updateProgress(int $seconds, int $totalDuration): void
    {
        $this->watched_seconds = $seconds;
        
        // Calculate percentage
        if ($totalDuration > 0) {
            $percentage = ($seconds / $totalDuration) * 100;
            $this->completion_percentage = $percentage;
        } else {
            $this->completion_percentage = 0.00;
        }
        
        $this->completed = $this->completion_percentage >= 90.00;
        $this->last_watched_at = now();
        $this->save();
    }
}