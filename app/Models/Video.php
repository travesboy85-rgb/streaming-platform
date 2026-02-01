<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'title',
        'description',
        'url',        // external video URL
        'file_path',  // local upload path
        'thumbnail',
        'duration',
        'category_id',
        'user_id',
        'is_premium',
        'views',
        'metadata'
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'is_premium' => 'boolean',
        'metadata'   => 'array',
        'views'      => 'integer',
        'duration'   => 'integer',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function watchHistories()
    {
        return $this->hasMany(WatchHistory::class);
    }

    /**
     * Example: scope for approved videos
     */
    public function scopeApproved($query)
    {
        return $query->where('approved', true);
    }

    /**
     * Example: scope for premium videos
     */
    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }
}

