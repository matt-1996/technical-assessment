<?php

namespace App\Http\Requests;

use App\Enums\StockMovementsEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
class StoreStockMovementRequest extends FormRequest
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
            'product_id' => [
                'required',
                'integer',
                'exists:products,id',
            ],

            'warehouse_id' => [
                'required',
                'integer',
                'exists:warehouses,id',
            ],

            'type' => [
                'required',
                new Enum(StockMovementsEnum::class),
            ],

            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'reference' => [
                'nullable',
                'string',
                'max:255',
            ],

            'meta' => [
                'required_if:type,transfer',
                'array',
            ],

            'meta.from_warehouse_id' => [
                'required_if:type,transfer',
                'nullable',
                'integer',
                'exists:warehouses,id',
            ],

            'meta.to_warehouse_id' => [
                'required_if:type,transfer',
                'nullable',
                'integer',
                'exists:warehouses,id',
                'different:meta.from_warehouse_id',
            ],
        ];
    }
}
