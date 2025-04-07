<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // The authorization is handled in the controller
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'listing_id' => 'required|integer|exists:listings,id',
            'user_id' => 'required|integer|exists:users,id',
            'status_id' => 'required|integer|exists:quote_statuses,id',
            'currency_id' => 'required|integer|exists:currencies,id',
            'deliverymethod_id' => 'required|integer|exists:deliverymethods,id',
            'amount' => 'required|numeric|min:0.01',
            'turnaround' => 'required|integer|min:1',
            'details' => 'nullable|string',
            'use_default_location' => 'required|boolean',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'postcode' => 'required|string|max:255',
            'country_id' => 'required|integer|exists:countries,id',
            'phone' => 'nullable|string|max:45',
        ];
    }
}