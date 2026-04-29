<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Test
 */
class TestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'is_published' => (bool) $this->is_published,
            'user_id' => $this->user_id,
            'questions_count' => $this->whenCounted('questions'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'author' => new UserResource($this->whenLoaded('user')),
            'questions' => QuestionResource::collection($this->whenLoaded('questions')),
        ];
    }
}
