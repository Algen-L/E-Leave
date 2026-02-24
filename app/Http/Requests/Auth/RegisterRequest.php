<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'gmail' => 'required|email|unique:users,gmail',
            'office_station' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'employee_number' => 'required|string|regex:/^[0-9]{7}$/|unique:users,employee_number',
        ];
    }
}
