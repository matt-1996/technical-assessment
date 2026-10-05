<?php

namespace App\Tenancy;
use App\Models\Tenant;
class TenancyContext
{
    protected ?Tenant $tenant;

    public function set(Tenant $tenant): void
    {
        $this->tenant = $tenant;
    }

    public function get(): ?Tenant
    {
        return $this->tenant;
    }

    public function id(): ?int
    {
        return $this->tenant?->id;
    }

    public function clear():void
    {
        $this->tenant = null;
    }

    public function check():bool
    {
        return $this->tenant !== null;
    }
}
