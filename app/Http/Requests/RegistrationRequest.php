<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class RegistrationRequest extends FormRequest
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
            'name' => 'required|string|min:6|max:255|unique:users',
            'email' =>'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'password_confirm' => 'required|same:password',
        ];
    }


    public function messages()
    {
        return [
            'name.required' => 'Le nom est requis',
            'email.required' => 'Email is required',
            'password.required' => 'Le mot de passe est requis',
            'password_confirm.required' => 'Les deux mots de passe ne correspondent pas',
        ];
    }


    public function failedValidation(validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Echec de validation.',
            'data'      => $validator->errors()
        ]));
    }


}
