<?php

namespace App\Models;

use App\Enums\StockMovementsEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockMovement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'product_id',
        'warehouse_id',
        'type',
        'quantity',
        'reference',
    ];

    protected function casts(): array
    {
        return [
            'type' => StockMovementsEnum::class,
            'meta' => 'array',
        ];
    }
}
