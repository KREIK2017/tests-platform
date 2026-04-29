<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Attempt
 */
class AttemptResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'test_id' => $this->test_id,
            'score' => $this->score,
            'total_questions' => $this->total_questions,
            'percent' => $this->total_questions > 0
                ? (int) round(100 * $this->score / $this->total_questions)
                : 0,
            'completed_at' => $this->completed_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'student' => new UserResource($this->whenLoaded('user')),
            'test' => new TestResource($this->whenLoaded('test')),
            'attempt_answers' => $this->whenLoaded('attemptAnswers', function () {
                return $this->attemptAnswers->map(fn ($aa) => [
                    'question_id' => $aa->question_id,
                    'answer_id' => $aa->answer_id,
                ])->all();
            }),
        ];
    }
}
