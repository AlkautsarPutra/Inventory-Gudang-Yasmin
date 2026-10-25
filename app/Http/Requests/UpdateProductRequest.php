<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends StoreProductRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = parent::rules();

        $product = $this->route('product');

        $productId = $product instanceof Product
            ? $product->getKey()
            : $product;

        $rules['product_code'] = [
            'required',
            'string',
            'max:50',
            Rule::unique('products', 'product_code')
                ->ignore($productId),
        ];

        return $rules;
    }
}