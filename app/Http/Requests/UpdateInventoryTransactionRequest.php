<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\InventoryTransactionType;
use App\Models\InventoryTransaction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateInventoryTransactionRequest extends FormRequest
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
        /** @var InventoryTransaction|null $transaction */
        $transaction = $this->route('inventoryTransaction');

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
                'required',
                'numeric',
                'gt:0',
                'decimal:0,3',
            ],

            'unit' => [
                'required',
                'string',
                'max:20',
            ],

            'reference_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'notes' => [
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
            'unit' => 'unit',
            'reference_number' => 'reference number',
            'notes' => 'notes',
        ];
    }
}