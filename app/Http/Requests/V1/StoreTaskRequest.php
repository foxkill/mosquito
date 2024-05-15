<?php

namespace App\Http\Requests\V1;

use App\Enums\StateEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|max:255',
            'description' => ['required'],
            'state' => ['required', Rule::in(StateEnum::Todo->value)],
            'deadline' => 'sometimes|required|date|after:today',
            'project_id' => 'nullable|exists:projects,id',
        ];
    }
}
