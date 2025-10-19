<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBulkRequestFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Change this based on your authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'school_name' => 'required|string|max:255',
            'email' => 'required|email',
            'students' => 'required|array|min:1',
            'students.*' => 'required|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'school_name.required' => 'The school name is required.',
            'email.required' => 'The email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'students.required' => 'At least one student is required.',
            'students.min' => 'You must add at least one student.',
            'students.*.required' => 'Student name cannot be empty.',
        ];
    }
}