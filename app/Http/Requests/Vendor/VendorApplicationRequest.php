<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class VendorApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('email'))) {
            $this->merge(['email' => Str::lower(trim($this->input('email')))]);
        }

        foreach (['name', 'business_name', 'business_address'] as $field) {
            if (is_string($this->input($field))) {
                $this->merge([$field => trim(preg_replace('/\s+/', ' ', $this->input($field)))]);
            }
        }
    }

    /**
     * There is deliberately NO "role" or "status" rule. The controller fixes them in code,
     * so nothing the browser sends can change them.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'regex:/^(09|\+639)\d{9}$/'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'business_name' => ['required', 'string', 'min:2', 'max:255'],
            'business_address' => ['required', 'string', 'min:5', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please enter the owner’s full name.',
            'phone.regex' => 'Enter a valid Philippine mobile number, for example 09171234567.',
            'business_name.required' => 'Please enter your store name.',
            'business_address.required' => 'Please enter your store address.',
            'email.unique' => 'An account with this email already exists.',
        ];
    }
}