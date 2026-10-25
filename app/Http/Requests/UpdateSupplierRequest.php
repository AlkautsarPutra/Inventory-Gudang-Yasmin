<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Supplier;
use Illuminate\Validation\Rule;

class UpdateSupplierRequest extends StoreSupplierRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = parent::rules();

        $supplier = $this->route('supplier');

        $supplierId = $supplier instanceof Supplier
            ? $supplier->getKey()
            : $supplier;

        $rules['supplier_code'] = [
            'required',
            'string',
            'max:50',
            Rule::unique('suppliers', 'supplier_code')
                ->ignore($supplierId),
        ];

        return $rules;
    }
}