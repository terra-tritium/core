<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        $validate = [
            'email' => 'required',
            'password' => 'required',
            'g-recaptcha-response' => 'required|recaptcha',
        ];
        return $validate;
    }

    public function messages(): array
    {
        $messages = [
            'email.required' => 'E-mail is required.',
            'password.required' => 'Password is required.',
            'g-recaptcha-response' => 'Captcha is required.'
        ];

        return $messages;
    }
}
