<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStoreImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // "image" checks the real file content, and SVG is not allowed.
            'store_image' => [
                'required',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
                'dimensions:max_width=6000,max_height=6000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'store_image.required' => 'Choose an image to upload.',
            'store_image.image' => 'The file must be an image.',
            'store_image.mimes' => 'The image must be a JPG, PNG or WEBP file.',
            'store_image.max' => 'The image must not be larger than 4 MB.',
            'store_image.dimensions' => 'The image is too large. Please use one under 6000 × 6000 pixels.',
            'store_image.uploaded' => 'The upload failed. The file may be too large for the server.',
        ];
    }
}