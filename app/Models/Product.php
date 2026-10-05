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
}
