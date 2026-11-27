<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddressUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('address'))) {
            $this->merge(['address' => trim(preg_replace('/\s+/', ' ', $this->input('address')))]);
        }
    }

    public function rules(): array
    {
        return [
            'address' => ['required', 'string', 'min:10', 'max:255'],
            // Must be the name of a real, active delivery zone (barangay), or left empty.
            'preferred_delivery_area' => [
                'nullable',
                'string',
                'max:255',
                Rule::exists('delivery_zones', 'zone_name')->where('is_active', true),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'address.min' => 'Please enter a complete address (at least 10 characters).',
            'preferred_delivery_area.exists' => 'Please choose a barangay from the list.',
        ];
    }
}