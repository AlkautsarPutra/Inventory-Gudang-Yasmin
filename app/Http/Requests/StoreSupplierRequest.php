<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\SupplierStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreSupplierRequest extends FormRequest
{
    /**
     * Determine whether the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'supplier_code' => [
                'required',
                'string',
                'max:50',
                'unique:suppliers,supplier_code',
            ],

            'supplier_name' => [
                'required',
                'string',
                'max:255',
            ],

            'company_name' => [
                'required',
                'string',
                'max:255',
            ],

            'tax_identification_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'contact_person' => [
                'required',
                'string',
                'max:255',
            ],

            'phone_number' => [
                'nullable',
                'string',
                'max:50',
            ],

            'mobile_number' => [
                'nullable',
                'string',
                'max:50',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'website' => [
                'nullable',
                'url',
                'max:255',
            ],

            'street_address' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'city' => [
                'nullable',
                'string',
                'max:100',
            ],

            'province' => [
                'nullable',
                'string',
                'max:100',
            ],

            'postal_code' => [
                'nullable',
                'string',
                'max:20',
            ],

            'country' => [
                'required',
                'string',
                'max:100',
            ],

            'supplier_category' => [
                'required',
                'string',
                'max:100',
            ],

            'business_type' => [
                'required',
                'string',
                'max:100',
            ],

            'status' => [
                'required',
                new Enum(SupplierStatus::class),
            ],
        ];
    }

    /**
     * Prepare data before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'supplier_code' => strtoupper(trim((string) $this->supplier_code)),
            'supplier_name' => trim((string) $this->supplier_name),
            'company_name' => trim((string) $this->company_name),
            'tax_identification_number' => trim((string) $this->tax_identification_number),
            'contact_person' => trim((string) $this->contact_person),
            'phone_number' => trim((string) $this->phone_number),
            'mobile_number' => trim((string) $this->mobile_number),
            'email' => $this->email
                ? strtolower(trim((string) $this->email))
                : null,
            'website' => $this->website
                ? trim((string) $this->website)
                : null,
            'street_address' => trim((string) $this->street_address),
            'city' => trim((string) $this->city),
            'province' => trim((string) $this->province),
            'postal_code' => trim((string) $this->postal_code),
            'country' => trim((string) $this->country),
            'supplier_category' => trim((string) $this->supplier_category),
            'business_type' => trim((string) $this->business_type),
        ]);
    }

    /**
     * Human readable attribute names.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'supplier_code' => 'supplier code',
            'supplier_name' => 'supplier name',
            'company_name' => 'company name',
            'tax_identification_number' => 'tax identification number',
            'contact_person' => 'contact person',
            'phone_number' => 'phone number',
            'mobile_number' => 'mobile number',
            'email' => 'email',
            'website' => 'website',
            'street_address' => 'street address',
            'city' => 'city',
            'province' => 'province',
            'postal_code' => 'postal code',
            'country' => 'country',
            'supplier_category' => 'supplier category',
            'business_type' => 'business type',
            'status' => 'status',
        ];
    }
}
