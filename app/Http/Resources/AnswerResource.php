<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Answer
 */
class AnswerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $request->user();
        $isAdmin = $user?->isAdmin() ?? false;

        return [
            'id' => $this->id,
            'question_id' => $this->question_id,
            'text' => $this->text,
            'is_correct' => $this->when($isAdmin, fn () => (bool) $this->is_correct),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
