<?php

namespace App\Traits;
use App\Models\Scopes\TenantScope;
use App\Tenancy\TenancyContext;

trait BelongsToTenantTrait
{
    static function BelongsToTenant():void {
        static::addGlobalScope(new TenantScope());

        static::creating(function ($model) {
            $tenant_id = app(TenancyContext::class)->id();
            if (!$tenant_id){
                throw new \Exception("Tenant ID is invalid");
            }

            $model->tenant_id = $tenant_id;
        });
    }
}
