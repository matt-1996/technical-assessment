<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = json_decode(file_get_contents(__DIR__ . '/data/products.json'));

        if (!$products) {
            throw new \Exception("No products found");
        }

        foreach ($products as $product) {
            Product::create(
                [
                    'tenant_id' => $product->tenant_id,
                    'sku' => $product->sku,
                    'name' => $product->name,
                    'unit_price' => $product->unit_price,
                ]
            );
        }
    }
}
