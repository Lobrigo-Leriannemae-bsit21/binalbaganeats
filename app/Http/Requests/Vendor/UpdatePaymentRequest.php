<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        foreach (['gcash_number', 'gcash_name'] as $field) {
            if (is_string($this->input($field))) {
                $this->merge([$field => trim(preg_replace('/\s+/', ' ', $this->input($field)))]);
            }
        }
    }

    /** Leave both fields empty to stop accepting GCash. */
    public function rules(): array
    {
        return [
            'gcash_number' => ['nullable', 'required_with:gcash_name', 'string', 'regex:/^(09|\+639)\d{9}$/'],
            'gcash_name' => ['nullable', 'required_with:gcash_number', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'gcash_number.regex' => 'Enter a valid Philippine mobile number, for example 09171234567.',
            'gcash_number.required_with' => 'Enter the GCash number, or clear both fields.',
            'gcash_name.required_with' => 'Enter the name on the GCash account, or clear both fields.',
        ];
    }
}