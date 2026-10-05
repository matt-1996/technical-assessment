<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function run()
    {
        $tenants = json_decode(file_get_contents(__DIR__ . '/data/tenants.json'));

        if (!$tenants){
            throw new \Exception('No Tenants found');
        }

        foreach ($tenants as $tenant){
            Tenant::create([
                'name' => $tenant->name,
                'subdomain' => $tenant->subdomain,
            ]);
        }
    }
}
