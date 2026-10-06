<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockMovementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'tenant_id' => $this->tenant_id,

            'product_id' => $this->product_id,

            'warehouse_id' => $this->warehouse_id,

            'type' => $this->type->value,

            'quantity' => $this->quantity,

            'reference' => $this->reference,

            'meta' => $this->meta,

            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
