<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'text' => ['required', 'string', 'min:5', 'max:1000'],
            'order' => ['nullable', 'integer', 'min:0'],
            'answers' => ['required', 'array', 'size:4'],
            'answers.*.text' => ['required', 'string', 'max:255'],
            'correct_answer' => ['required', 'integer', 'min:0', 'max:3'],
        ];
    }

    public function attributes(): array
    {
        return [
            'answers.0.text' => __('tests.answers.singular') . ' #1',
            'answers.1.text' => __('tests.answers.singular') . ' #2',
            'answers.2.text' => __('tests.answers.singular') . ' #3',
            'answers.3.text' => __('tests.answers.singular') . ' #4',
            'correct_answer' => __('tests.answers.fields.is_correct'),
        ];
    }
}
