<?php

namespace App\Http\Requests\Customer;

use App\Models\BuyForMeRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BuyForMeAdvanceRequest extends FormRequest
{
    /** Only the owner of the request may submit its advance payment. */
    public function authorize(): bool
    {
        $buyForMe = $this->route('buyForMe');

        return $buyForMe instanceof BuyForMeRequest && (bool) $this->user()?->can('view', $buyForMe);
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('gcash_reference_number'))) {
            $this->merge([
                'gcash_reference_number' => Str::upper(preg_replace('/\s+/', '', $this->input('gcash_reference_number'))),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'gcash_reference_number' => [
                'required',
                'string',
                'regex:/^[A-Z0-9]{8,30}$/',
                // A reference may be used only once, across food orders AND Buy-for-Me.
                Rule::unique('buy_for_me_requests', 'gcash_reference_number'),
                Rule::unique('payments', 'transaction_reference'),
            ],
            'advance_proof' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }

    public function messages(): array
    {
        return [
            'gcash_reference_number.required' => 'Enter the reference number from your GCash receipt.',
            'gcash_reference_number.regex' => 'The reference number should be 8 to 30 letters or numbers.',
            'gcash_reference_number.unique' => 'This reference number has already been used.',
            'advance_proof.required' => 'Upload a screenshot of your GCash payment.',
            'advance_proof.image' => 'The payment proof must be an image.',
            'advance_proof.mimes' => 'The payment proof must be a JPG, PNG or WEBP image.',
            'advance_proof.max' => 'The payment proof must not be larger than 4 MB.',
        ];
    }
}