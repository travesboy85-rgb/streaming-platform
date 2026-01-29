<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'url',        // external video URL
        'file_path',  // ✅ local upload path must be fillable
        'thumbnail',
        'duration',
        'category_id',
        'user_id',
        'is_premium',
        'views',
        'metadata'
    ];

    protected $casts = [
        'is_premium' => 'boolean',
        'metadata'   => 'array',
        'views'      => 'integer',
        'duration'   => 'integer'
    ];

    // Relationships
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
}
