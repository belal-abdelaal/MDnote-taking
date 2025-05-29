<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PartialNoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    // public function authorize(): bool {return false;}

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "title" => "string|max:255",
            "note" => "string"
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        // Build whatever JSON structure you like:
        $response = response([
            'errors'  => $validator->errors(),
        ], 400);

        throw new HttpResponseException($response);
    }
}
