<?php

namespace Database\Seeders;

use App\Models\StockLevel;
use Illuminate\Database\Seeder;

class StockLevelSeeder extends Seeder
{
    public function run()
    {
        $stockLevels = json_decode(file_get_contents(__DIR__ . '/data/stockLevel.json'));

        if (!$stockLevels) {
            throw new \Exception('Stock levels json is empty');
        }

        foreach ($stockLevels as $stockLevel) {
            StockLevel::create([
                'tenant_id' => $stockLevel->tenant_id,
                'product_id' => $stockLevel->product_id,
                'warehouse_id' => $stockLevel->warehouse_id,
                'quantity' => $stockLevel->quantity,
            ]);
        }
    }
}
