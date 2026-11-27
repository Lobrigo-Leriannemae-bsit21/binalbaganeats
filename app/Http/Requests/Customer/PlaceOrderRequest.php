<?php

namespace App\Http\Requests\Customer;

use App\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PlaceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // auth / approved / role:customer middleware already protects the route
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('transaction_reference'))) {
            $this->merge([
                'transaction_reference' => Str::upper(preg_replace('/\s+/', '', $this->input('transaction_reference'))),
            ]);
        }

        if (is_string($this->input('delivery_address'))) {
            $this->merge([
                'delivery_address' => trim(preg_replace('/\s+/', ' ', $this->input('delivery_address'))),
            ]);
        }
    }

    public function rules(): array
    {
        $isGcash = $this->input('payment_method') === PaymentMethod::Gcash->value;

        return [
            'delivery_address' => ['required', 'string', 'min:10', 'max:255'],
            'delivery_zone_id' => [
                'required',
                'integer',
                Rule::exists('delivery_zones', 'id')->where('is_active', true),
            ],
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],

            // GCash-only fields are required for GCash and ignored completely for COD.
            'transaction_reference' => $isGcash
                ? ['required', 'string', 'regex:/^[A-Z0-9]{8,30}$/', Rule::unique('payments', 'transaction_reference')]
                : ['exclude'],
            'payment_proof' => $isGcash
                ? ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096']
                : ['exclude'],

            'save_address' => ['nullable', 'boolean'],
            'checkout_token' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'delivery_address.min' => 'Please enter a complete delivery address (at least 10 characters).',
            'delivery_zone_id.required' => 'Please choose your delivery zone.',
            'delivery_zone_id.exists' => 'That delivery zone is not available. Please choose another.',
            'payment_method.required' => 'Please choose a payment method.',
            'transaction_reference.required' => 'Enter the reference number from your GCash receipt.',
            'transaction_reference.regex' => 'The reference number should be 8 to 30 letters or numbers.',
            'transaction_reference.unique' => 'This reference number has already been used on another order.',
            'payment_proof.required' => 'Upload a screenshot of your GCash payment.',
            'payment_proof.image' => 'The payment proof must be an image.',
            'payment_proof.mimes' => 'The payment proof must be a JPG, PNG or WEBP image.',
            'payment_proof.max' => 'The payment proof must not be larger than 4 MB.',
        ];
    }
}