<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
        $rules = [
            'name' => 'required',
            'email' => 'required|unique:users,email,' . $this->get('id'),
        ];

        // Check if password field is not empty
        if ($this->has('password')) {
            // If password field has a value, add required and min:8 rules
            $rules['password'] = 'required|min:8';
        } else {
            // If password field is empty, no need to make it required or min:8
            $rules['password'] = '';
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
