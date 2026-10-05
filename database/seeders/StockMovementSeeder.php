<?php

namespace Database\Seeders;

use App\Models\StockMovement;
use Illuminate\Database\Seeder;

class StockMovementSeeder extends Seeder
{
    public function run()
    {
        $stock_movements = json_decode(file_get_contents(__DIR__ . '/data/stockMovement.json'));


        if (!$stock_movements){
            throw new \Exception("No stock movements found");
        }

        foreach ($stock_movements as $stock_movement){
            StockMovement::create([
                'tenant_id' => $stock_movement->tenant_id,
                'product_id' => $stock_movement->product_id,
                'warehouse_id' => $stock_movement->warehouse_id,
                'type' => $stock_movement->type,
                'quantity' => $stock_movement->quantity,
                'reference' => $stock_movement->reference,
                'meta' => $stock_movement->meta,
            ]);
        }
    }
}
