<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadCSVRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            "file" => 'required|mimes:csv,txt',
        ];
    }

    public function messages()
    {
        return [
            'file.required' => 'The file field is required. ',
            'file.mimes'  => 'Please select CSV file type',
        ];
    }
}
