<?php

namespace App\Http\Requests\Leave;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCreditsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'credits' => 'required|array',
            'credits.*' => 'nullable|numeric|min:0',
        ];
    }
}
