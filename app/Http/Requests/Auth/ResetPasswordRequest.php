<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'email' => 'required|email',
            'token' => 'required|string|size:6',
            'verify_only' => 'sometimes|boolean',
        ];

        if (!$this->boolean('verify_only', false)) {
            $rules['password'] = 'required|string|min:6|confirmed';
        }

        return $rules;
    }
}
