<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCartRequest extends FormRequest
{
    protected $errorBag = 'cart';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quantity' => ['required', 'integer', 'min:1', 'max:'.config('binalbagan.max_item_quantity', 20)],
        ];
    }

    public function messages(): array
    {
        return [
            'quantity.max' => 'You can order at most '.config('binalbagan.max_item_quantity', 20).' of the same item.',
            'quantity.min' => 'Quantity must be at least 1.',
        ];
    }
}