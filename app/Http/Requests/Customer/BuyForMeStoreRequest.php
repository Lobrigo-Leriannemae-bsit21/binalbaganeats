<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BuyForMeStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // auth / approved / role:customer middleware protects the route
    }

    protected function prepareForValidation(): void
    {
        foreach (['establishment_name', 'delivery_address'] as $field) {
            if (is_string($this->input($field))) {
                $this->merge([$field => trim(preg_replace('/\s+/', ' ', $this->input($field)))]);
            }
        }

        if (is_string($this->input('items_summary'))) {
            $this->merge(['items_summary' => trim(str_replace("\r\n", "\n", $this->input('items_summary')))]);
        }
    }

    public function rules(): array
    {
        return [
            'establishment_name' => ['required', 'string', 'min:2', 'max:255'],
            'items_summary' => ['required', 'string', 'min:5', 'max:2000'],
            'delivery_address' => ['required', 'string', 'min:10', 'max:255'],
            'delivery_zone_id' => [
                'required',
                'integer',
                Rule::exists('delivery_zones', 'id')->where('is_active', true),
            ],
            'form_token' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'establishment_name.required' => 'Tell us where the items should be bought.',
            'items_summary.required' => 'Describe what you would like us to buy.',
            'items_summary.min' => 'Please describe the items in a little more detail.',
            'delivery_address.min' => 'Please enter a complete delivery address (at least 10 characters).',
            'delivery_zone_id.required' => 'Please choose your barangay.',
            'delivery_zone_id.exists' => 'That barangay is not available. Please choose another.',
        ];
    }
}