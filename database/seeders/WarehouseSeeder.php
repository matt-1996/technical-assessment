<?php

namespace Database\Seeders;

use App\Models\warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    public function run()
    {
        $warehouses = json_decode(file_get_contents(__DIR__ . "/data/warehouses.json"));

        if(!$warehouses) {
            throw new \Exception("No Warehouses found");
        }

        foreach ($warehouses as $warehouse) {
            Warehouse::create([
                'tenant_id' => $warehouse->tenant_id,
                'name' => $warehouse->name,
                'location' => $warehouse->location,
            ]);
        }
    }
}
