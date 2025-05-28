<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PartialUserRequest extends FormRequest
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
            "name" => "sometimes|required|string|min:5|max:25",
            "email" => "sometimes|required|email|max:50",
            "password" => "sometimes|required|min:8|max:50"
        ];
    }
    protected function failedValidation(Validator $validator): void
    {
        // Build whatever JSON structure you like:
        $response = response([
            'errors'  => $validator->errors(),
        ], 400);

        throw new HttpResponseException($response);
    }
}
