<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Quote;

class StoreQuoteRequest extends FormRequest
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
        return [
            'user_id' => 'required|integer|exists:users,id',
            'listing_id' => [
                'required',
                'exists:listings,id',
                function ($attribute, $value, $fail) {
                    $openStatusId = \DB::table('quote_statuses')->where('name', 'Open')->value('id');

                    $existingQuote = Quote::where('user_id', auth()->id())
                        ->where('listing_id', $value)
                        ->where('status_id', $openStatusId)
                        ->where('currency_id', $this->currency_id)
                        ->where('deliverymethod_id', $this->deliverymethod_id)
                        ->where('address_line1', $this->address_line1)
                        ->where(function ($query) {
                            if (!empty($this->address_line2)) {
                                $query->where('address_line2', $this->address_line2);
                            } else {
                                $query->whereNull('address_line2');
                            }
                        })
                        ->where('city', $this->city)
                        ->where('postcode', $this->postcode)
                        ->where('country_id', $this->country_id)
                        ->first();

                    if ($existingQuote) {
                        // Store the existing quote ID in the validator's data
                        $this->merge(['existing_quote_id' => $existingQuote->id]);

                        $fail('You already have an open quote with the same details for this listing.');
                    }
                },
            ],
            'status_id' => 'required|integer|exists:quote_statuses,id',
            'currency_id' => 'required|integer|exists:currencies,id',
            'deliverymethod_id' => 'required|integer|exists:deliverymethods,id',
            'amount' => 'required|numeric|min:0.01',
            'turnaround' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'use_default_location' => 'required|boolean',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'postcode' => 'required|string|max:255',
            'country_id' => 'required|integer|exists:countries,id',
            'phone' => 'nullable|string|max:45',
            'attachments.*' => 'nullable|file|max:10240|mimes:jpeg,png,jpg,gif,svg,mp4,mov,ogg,qt,pdf,doc,docx,txt',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'amount.required' => 'Please enter a quote amount',
            'amount.min' => 'Amount must be greater than zero',
            'turnaround.required' => 'Please specify the turnaround time in days',
            'turnaround.min' => 'Turnaround time must be at least 1 day',
            'deliverymethod_id.required' => 'Please select a delivery method',
            'phone.regex' => 'Phone number may only contain numbers, spaces, and the following characters: +, -, (, )',
        ];
    }
}