<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // auth / approved / role:vendor / vendor.has-store middleware protects the route
    }

    protected function prepareForValidation(): void
    {
        foreach (['business_name', 'business_address'] as $field) {
            if (is_string($this->input($field))) {
                $this->merge([$field => trim(preg_replace('/\s+/', ' ', $this->input($field)))]);
            }
        }
    }

    public function rules(): array
    {
        return [
            'business_name' => ['required', 'string', 'min:2', 'max:255'],
            'business_address' => ['required', 'string', 'min:5', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'estimated_prep_time' => ['required', 'integer', 'min:5', 'max:180'],
        ];
    }

    public function messages(): array
    {
        return [
            'business_name.required' => 'Please enter your store name.',
            'business_address.required' => 'Please enter your store address.',
            'estimated_prep_time.min' => 'Preparation time must be at least 5 minutes.',
            'estimated_prep_time.max' => 'Preparation time cannot be more than 180 minutes.',
        ];
    }
}