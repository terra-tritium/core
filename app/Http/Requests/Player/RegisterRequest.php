<?php

namespace App\Http\Requests\Player;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        $validate = [
            'email' => 'required|unique:users,email',
            'password' => 'required|min:8|alpha_num_with_letter',
            'country' => 'required',
            'name' => 'required|unique:players,name|max:15|min:3|regex:/(^([a-zA-Z0-9]+)?$)/u',
        ];

        if (config('api.app_env') == 'production') {
            $validate['g-recaptcha-response'] = 'required|recaptcha';
        }

        return $validate;
    }

    public function messages(): array
    {
        $messages = [
            'email.required' => 'E-mail is required.',
            'password.required' => 'Password is required.',
            'country.required' => 'Country is required.',
            'name.required' => 'Name is required.',
        ];

        if (config('api.app_env') == 'production') {
            $messages['g-recaptcha-response'] = 'Captcha is required.';
        }

        return $messages;
    }
}
