<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $rules = [
            'branch_id'    => 'required|exists:branches,id',
            'program_type' => 'required|in:adult,kids',
            'name' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'email' => 'required|email|max:100',
            'dob' => 'required|date',
            'current_level' => 'required_if:take_test,no',
            'take_test' => 'required|in:yes,no',
            'address' => 'required|string',
            'health_status' => 'required|in:yes,no',
            'health_notes' => 'required_if:health_status,yes',
        ];

        if ($this->input('program_type') === 'adult') {
            $rules['major'] = 'required|string|max:255';
        }

        if ($this->input('program_type') === 'kids') {
            $rules['parent_name'] = 'required|string|max:255';
            $rules['parent_phone'] = 'required|string|max:20';
            $rules['parent_email'] = 'nullable|email|max:100';
            $rules['parent_relationship'] = 'required|string|max:50';
        }

        if ($this->input('take_test') === 'yes') {
            $rules['test_date'] = 'required|date|after_or_equal:today';
            $rules['preferred_days'] = 'required|string';
            $rules['preferred_time'] = 'required|string';
            $rules['payment_method_id'] = 'required|exists:payment_methods,id';
            $rules['payment_receipt'] = 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:5120';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => 'الاسم الرباعي بالعربي مطلوب',
            'name_en.required' => 'الاسم الرباعي بالإنجليزي مطلوب',
            'branch_id.required'    => 'يرجى اختيار الفرع',
            'branch_id.exists'      => 'الفرع المختار غير صالح',
            'program_type.required' => 'يرجى اختيار البرنامج',
            'payment_receipt.mimes' => 'يجب أن يكون الملف صورة (jpg, png, webp) أو ملف PDF',
            'payment_receipt.max' => 'حجم الملف يجب أن لا يتجاوز 5 ميجابايت',
        ];
    }
}
