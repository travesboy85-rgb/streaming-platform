<?php

namespace App\Http\Controllers\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'url'         => $this->url ?? $this->file_path,
            'thumbnail'   => $this->thumbnail,
            'duration'    => $this->duration,
            'is_premium'  => $this->is_premium,
            'views'       => $this->views,
            'metadata'    => $this->metadata,

            // Relationships
            'category'    => $this->category?->name,
            'user'        => [
                'id'    => $this->user?->id,
                'name'  => $this->user?->name,
                'email' => $this->user?->email,
            ],

            // Timestamps
            'created_at'  => $this->created_at?->toDateTimeString(),
            'updated_at'  => $this->updated_at?->toDateTimeString(),
        ];
    }
}
