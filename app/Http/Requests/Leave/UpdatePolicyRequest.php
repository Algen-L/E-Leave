<?php

namespace App\Http\Requests\Leave;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePolicyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'leave_type_id' => 'required|exists:leave_types,id',
            'accrual_rate' => 'required|numeric|min:0',
            'accrual_period' => 'required|in:Monthly,Yearly,None',
            'expiration_rule' => 'required|in:None,Yearly,Monthly,SpecificDate',
            'expiration_date' => 'nullable|required_if:expiration_rule,SpecificDate|date',
            'max_credits' => 'nullable|numeric|min:0',
        ];
    }
}
