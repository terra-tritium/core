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
        ];

        if (config('api.app_env') == 'production') {
            $validate['g-recaptcha-response'] = 'required|captcha';
        }

        return $validate;
    }

    public function messages(): array
    {
        $messages = [
            'email.required' => 'E-mail is required.',
            'password.required' => 'Password is required.',
        ];

        if (config('api.app_env') == 'production') {
            $messages['g-recaptcha-response'] = '´Captcha is required.';
        }

        return $messages;
    }
}
