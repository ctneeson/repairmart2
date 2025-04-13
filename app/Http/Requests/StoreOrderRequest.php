<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Order;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $quote = $this->route('quote');
        return Gate::allows('create', [Order::class, $quote]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'quote_id' => 'required|integer|exists:quotes,id',
            'customer_id' => 'required|integer|exists:users,id',
            'status_id' => 'required|integer|exists:order_statuses,id',
            'override_quote' => 'required|boolean',
            'amount' => [
                'nullable',
                'numeric',
                'min:0.01',
                Rule::requiredIf(function () {
                    return $this->override_quote == true;
                }),
            ],
            'customer_feedback_id' => 'nullable|integer|exists:feedback_types,id',
            'customer_feedback' => 'nullable|string',
            'specialist_feedback_id' => 'nullable|integer|exists:feedback_types,id',
            'specialist_feedback' => 'nullable|string',
            'attachments.*' => 'nullable|file|max:10240|mimes:jpeg,png,jpg,gif,svg,mp4,mov,ogg,qt,pdf,doc,docx,txt',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'amount.required_if' => 'The amount field is required when overriding the quote amount.',
        ];
    }
}