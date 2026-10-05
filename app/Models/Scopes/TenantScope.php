<?php

namespace App\Models\Scopes;

use App\Tenancy\TenancyContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $tenant_id = app(TenancyContext::class)->id();

        if ($tenant_id !== null) {
            $builder->where($model->getTable() . 'tenant_id', $tenant_id);
        }
    }
}
