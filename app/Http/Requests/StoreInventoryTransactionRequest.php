<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\InventoryTransactionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreInventoryTransactionRequest extends FormRequest
{
    /**
     * Determine whether the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'unit' => is_string($this->unit)
                ? trim(strtoupper($this->unit))
                : $this->unit,

            'reference_type' => is_string($this->reference_type)
                ? trim(strtoupper($this->reference_type))
                : $this->reference_type,

            'reference_number' => is_string($this->reference_number)
                ? trim($this->reference_number)
                : $this->reference_number,

            'notes' => is_string($this->notes)
                ? trim($this->notes)
                : $this->notes,
        ]);
    }

    /**
     * Get the validation rules.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [

            'transaction_date' => [
                'required',
                'date',
                'before_or_equal:today',
            ],

            'product_id' => [
                'required',
                'integer',
                'exists:products,id',
            ],

            'supplier_id' => [
                'nullable',
                'integer',
                'exists:suppliers,id',
            ],

            'transaction_type' => [
                'required',
                new Enum(InventoryTransactionType::class),
            ],

            'quantity' => [
                'required_unless:transaction_type,stock_adjustment',
                'nullable',
                'numeric',
                'gt:0',
                'decimal:0,3',
            ],

            'physical_quantity' => [
                'required_if:transaction_type,stock_adjustment',
                'nullable',
                'numeric',
                'min:0',
                'decimal:0,3',
            ],

            'unit' => [
                'required',
                'string',
                'max:20',
            ],

            'reference_type' => [
                Rule::requiredIf(
                    fn (): bool => $this->input('transaction_type') === InventoryTransactionType::STOCK_ADJUSTMENT->value
                ),
                'nullable',
                'string',
                'max:30',
            ],

            'reference_number' => [
                Rule::requiredIf(
                    fn (): bool => $this->input('transaction_type') === InventoryTransactionType::STOCK_ADJUSTMENT->value
                ),
                'nullable',
                'string',
                'max:100',
            ],

            'notes' => [
                Rule::requiredIf(
                    fn (): bool => $this->input('transaction_type') === InventoryTransactionType::STOCK_ADJUSTMENT->value
                ),
                'nullable',
                'string',
                'max:5000',
            ],
        ];
    }

    /**
     * Get custom attribute names.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'transaction_date' => 'transaction date',
            'product_id' => 'product',
            'supplier_id' => 'supplier',
            'transaction_type' => 'transaction type',
            'quantity' => 'quantity',
            'physical_quantity' => 'physical quantity',
            'unit' => 'unit',
            'reference_type' => 'reference type',
            'reference_number' => 'reference number',
            'notes' => 'notes',
        ];
    }
}
