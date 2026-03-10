<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'order' => ['sometimes', 'integer'],
            'schedule_block_id' => ['sometimes', 'nullable', 'uuid'],
            'estimated_minutes' => ['sometimes', 'nullable', 'integer'],
        ];
    }
}
