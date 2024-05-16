<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => 'required|max:100|unique:tasks',
            'content' => 'required',
            'status' => 'required',
            'files.*' => 'nullable|file|max:4096', // 4MB limit (4096 KB)
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [];
    }
}
