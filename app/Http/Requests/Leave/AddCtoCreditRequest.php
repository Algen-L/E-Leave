<?php

namespace App\Http\Requests\Leave;

use Illuminate\Foundation\Http\FormRequest;

class AddCtoCreditRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'credit_amount' => 'required|numeric|min:0.1',
            'expiration_date' => 'required|date|after:today',
            'remarks' => 'nullable|string|max:255',
        ];
    }
}
