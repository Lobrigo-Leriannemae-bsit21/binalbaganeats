<?php

namespace App\Http\Requests\Customer;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SubmitPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $order = $this->route('order');

        return $order instanceof Order && (bool) $this->user()?->can('submitPayment', $order);
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('transaction_reference'))) {
            $this->merge([
                'transaction_reference' => Str::upper(preg_replace('/\s+/', '', $this->input('transaction_reference'))),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'transaction_reference' => [
                'required',
                'string',
                'regex:/^[A-Z0-9]{8,30}$/',
                Rule::unique('payments', 'transaction_reference'),
            ],
            'payment_proof' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }

    public function messages(): array
    {
        return [
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