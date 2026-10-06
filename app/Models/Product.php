<?php

namespace App\Models;

use App\Traits\BelongsToTenantTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes, BelongsToTenantTrait;

    protected $fillable = [
        'tenant_id',
        'sku',
        'name',
        'unit_price',
    ];

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function stockLevels()
    {
        return $this->hasMany(StockLevel::class);
    }
}
