<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{
    /** Validation errors are shown by <x-flash> from this named bag. */
    protected $errorBag = 'cart';

    public function authorize(): bool
    {
        return true; // Access is enforced by the auth / approved / role:customer route middleware.
    }

    public function rules(): array
    {
        return [
            'menu_item_id' => ['required', 'integer', 'exists:menu_items,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:'.config('binalbagan.max_item_quantity', 20)],
            'replace' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'menu_item_id.exists' => 'That item could not be found.',
            'quantity.max' => 'You can order at most '.config('binalbagan.max_item_quantity', 20).' of the same item.',
            'quantity.min' => 'Quantity must be at least 1.',
        ];
    }
}