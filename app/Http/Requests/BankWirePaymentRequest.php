<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BankWirePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:100'], // Minimum $100 for wire transfers
            'currency' => ['required', 'string', 'size:3'],
            
            // Bank Details
            'bank_name' => ['required', 'string', 'max:255'],
            'account_holder_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:50'],
            
            // International vs Domestic formatting
            'routing_number' => ['nullable', 'required_without:swift_bic', 'string', 'max:20', function ($attribute, $value, $fail) {
                if ($value && !\App\Services\BankValidator::isValidRoutingNumber($value)) {
                    $fail('The routing number is invalid.');
                }
            }],
            'swift_bic' => ['nullable', 'required_without:routing_number', 'string', 'max:11', function ($attribute, $value, $fail) {
                if ($value && !\App\Services\BankValidator::isValidSwiftBic($value)) {
                    $fail('The SWIFT/BIC code is invalid.');
                }
            }],
            'iban' => ['nullable', 'string', 'max:34', function ($attribute, $value, $fail) {
                if ($value && !\App\Services\BankValidator::isValidIban($value)) {
                    $fail('The IBAN is invalid.');
                }
            }],
            
            // Location
            'bank_country' => ['required', 'string', 'size:2'], // ISO-3166-1 alpha-2
            'bank_city' => ['nullable', 'string', 'max:100'],
            'bank_state' => ['nullable', 'string', 'max:100'],
            'bank_zip' => ['nullable', 'string', 'max:20'],
            'bank_address' => ['nullable', 'string', 'max:500'],
            
            // Additional Info
            'user_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
    
    public function messages(): array
    {
        return [
            'amount.min' => 'Wire transfers require a minimum amount of :min.',
            'routing_number.required_without' => 'A Routing Number is required if no SWIFT/BIC is provided.',
            'swift_bic.required_without' => 'A SWIFT/BIC code is required if no Routing Number is provided.',
            'bank_country.size' => 'The bank country must be a valid 2-letter ISO code.',
        ];
    }
}
