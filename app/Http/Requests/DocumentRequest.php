<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Allow all users to make the request (you can add authorization logic here)
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|string|max:255',
            'claimer_id' => 'required|string|max:255',
            'student_information_id' => 'required|string|max:255',
            'document_id' => 'required|string|max:255',
            'request_schl_entity' => 'required|string|max:255',
            'requested_sf10' => 'required|string|max:255',
            'release_mode' => 'required|string|max:255',
            'remarks' => 'required|string|max:255',
            'status' => 'required|string|max:255',

        ];
    }
}
