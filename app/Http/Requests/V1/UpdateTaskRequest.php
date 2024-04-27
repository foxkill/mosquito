<?php

namespace App\Http\Requests\V1;

use App\Enums\StateEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return request()->isMethod('PATCH');
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
                Rule::in(StateEnum::InProgess, StateEnum::Done)
            ],
            // exam - additional
            'deadline' => 'required|date|after:today'
        ];
    }
}
