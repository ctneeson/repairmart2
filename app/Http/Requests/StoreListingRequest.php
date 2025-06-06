<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreListingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            // 'user_id' => 'required|integer|exists:users,id',
            // 'status_id' => 'required|integer|exists:listing_statuses,id',
            'manufacturer_id' => 'required|integer|exists:manufacturers,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'currency_id' => 'nullable|integer|exists:currencies,id',
            'budget' => 'nullable|numeric',
            'use_default_location' => 'required|boolean',
            'address_line1' => 'required|nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'postcode' => 'required|string|max:255',
            'country_id' => 'required|integer|exists:countries,id',
            'phone' => ['nullable', 'string', 'max:45', 'regex:/^[0-9\+\-\(\)\s]+$/'],
            'expiry_days' => 'required|integer',
            'product_ids' => 'required|array|min:1|max:3',
            'product_ids.*' => 'integer|distinct|exists:products,id',
            'attachments.*' => 'file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,ogg,qt|max:20000',
        ];

        // For create requests, ensure published_at is today or in the future
        if ($this->isMethod('post')) {
            $rules['published_at'] = 'required|date|after_or_equal:today';
        } else {
            $rules['published_at'] = 'required|date';
        }

        return $rules;
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => 'Please enter a value',
            'manufacturer_id.required' => 'Please select a manufacturer',
            'product_ids.required' => 'Please select at least one product',
            'published_at.required' => 'Please select a date',
            'published_at.after_or_equal' => 'Publication date must be today or in the future',
            'phone.regex' => 'Phone number may only contain numbers, spaces, and the following characters: +, -, (, )',
        ];
    }

    public function attributes(): array
    {
        return [
            'manufacturer_id' => 'manufacturer',
            'product_ids' => 'products',
            'currency_id' => 'currency',
            'country_id' => 'country',
            'published_at' => 'publish date',
        ];
    }

    protected function prepareForValidation()
    {
        $phone = $this->phone;

        if ($phone) {
            // Trim leading/trailing whitespace
            $phone = trim($phone);

            // Replace multiple spaces/tabs with a single space
            $phone = preg_replace('/\s+/', ' ', $phone);
        }

        $this->merge([
            // 'user_id' => auth()->id(),
            'status_id' => 1,
            'phone' => $phone,
        ]);
    }

    protected function passedValidation()
    {
        $this->merge([
            'postcode' => strtoupper($this->postcode),
        ]);
    }
}
