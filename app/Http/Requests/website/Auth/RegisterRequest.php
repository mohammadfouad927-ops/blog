<?php

namespace App\Http\Requests\website\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;


class RegisterRequest extends FormRequest
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
            "name" => 'required|min:4|max:255|unique:users,name',
            "first_name" => 'required|string|max:20',
            "last_name" => 'required|string|max:20',
            "email" => 'required|email|unique:users,email',
            "password" => ['required','confirmed', Rules\Password::defaults()],
        ];
    }
}
