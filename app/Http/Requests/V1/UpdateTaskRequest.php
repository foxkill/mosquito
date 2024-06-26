<?php

namespace App\Http\Requests\V1;

use App\Enums\StateEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
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
            'title' => 'sometimes|required|max:255',
            'description' => 'sometimes|required',
            'state' => [
                'sometimes',
                'required',
                Rule::in(StateEnum::InProgess->value, StateEnum::Done->value),
            ],
            'project_id' => 'sometimes|exists:projects,id',
            'deadline' => 'sometimes|required|date|after:today',
        ];
    }
}
