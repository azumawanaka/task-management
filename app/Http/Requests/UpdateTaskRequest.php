<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
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
            'title' => 'required|max:100|unique:tasks,title,' . $this->get('id'),
            'content' => 'required',
            'status' => 'required',
        ];

        if ($this->has('files')) {
            $rules['files.*'] = 'file|max:4096'; // 4MB limit (4096 KB)
        } else {
            $rules['files.*'] = 'nullable'; 
        }

        return $rules;
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [];
    }
}
