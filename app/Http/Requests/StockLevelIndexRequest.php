<?php

namespace App\Http\Requests;

use App\Tenancy\TenancyContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class StockLevelIndexRequest extends FormRequest
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
        $tenantId = app(TenancyContext::class)->id();
        return [
            'product_id' => [
                'nullable',
                'integer',
                Rule::exists('products', 'id')
                    ->where('tenant_id', $tenantId),
            ],

            'warehouse_id' => [
                'nullable',
                'integer',
                Rule::exists('warehouses', 'id')
                    ->where('tenant_id', $tenantId),
            ],

            'per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],
        ];
    }
}
