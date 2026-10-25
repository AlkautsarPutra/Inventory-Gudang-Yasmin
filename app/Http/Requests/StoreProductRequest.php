<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\ProductStatus;
use App\Enums\WarehouseType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine whether the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'product_code' => [
                'required',
                'string',
                'max:50',
                'unique:products,product_code',
            ],

            'product_name' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'category' => [
                'required',
                'string',
                'max:100',
            ],

            'brand' => [
                'required',
                'string',
                'max:100',
            ],

            'unit' => [
                'required',
                'string',
                'max:30',
            ],

            'warehouse_type' => [
                'required',
                Rule::enum(WarehouseType::class),
            ],

            'minimum_stock' => [
                'required',
                'integer',
                'min:0',
            ],

            'maximum_stock' => [
                'nullable',
                'integer',
                'gte:minimum_stock',
            ],

            'reorder_point' => [
                'required',
                'integer',
                'gte:minimum_stock',
                Rule::when(
                    $this->filled('maximum_stock'),
                    'lte:maximum_stock',
                ),
            ],

            'lead_time_days' => [
                'required',
                'integer',
                'min:1',
                'max:365',
            ],

            'status' => [
                'required',
                new Enum(ProductStatus::class),
            ],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'product_code' => strtoupper(trim((string) $this->product_code)),
            'product_name' => trim((string) $this->product_name),
            'description' => trim((string) $this->description),
            'category' => trim((string) $this->category),
            'brand' => trim((string) $this->brand),
            'unit' => strtoupper(trim((string) $this->unit)),
        ]);
    }

    /**
     * Get custom attribute names.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'product_code' => 'product code',
            'product_name' => 'product name',
            'description' => 'description',
            'category' => 'category',
            'brand' => 'brand',
            'unit' => 'unit',
            'warehouse_type' => 'warehouse type',
            'minimum_stock' => 'minimum stock',
            'maximum_stock' => 'maximum stock',
            'reorder_point' => 'reorder point',
            'status' => 'status',
        ];
    }
}
