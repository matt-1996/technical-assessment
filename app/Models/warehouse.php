<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class warehouse extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'location',
    ];
}
