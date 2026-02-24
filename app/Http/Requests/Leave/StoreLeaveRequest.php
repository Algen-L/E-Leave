<?php

namespace App\Http\Requests\Leave;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeaveRequest extends FormRequest
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
            'leave_type_id' => 'required|exists:leave_types,id',
            'selected_dates' => 'required|string',
            'days_applied' => 'required|numeric|min:0.5',
            'others_type' => 'nullable|string',
            'other_purpose' => 'nullable|string',
            'vacation_loc_type' => 'nullable|string',
            'vacation_loc_details' => 'nullable|string',
            'sick_loc_type' => 'nullable|string',
            'sick_illness' => 'nullable|string',
            'women_illness' => 'nullable|string',
            'study_type' => 'nullable|string',
            'study_details' => 'nullable|string',
        ];
    }
}
