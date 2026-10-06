<?php

namespace App\Repository;

use App\Interfaces\ProductRepositoryInterface;
use App\Models\Product;

class ProductRepository implements ProductRepositoryInterface
{
    public function get_product_history_by_sku($sku,$page = 1, $perPage = 10)
    {
        $product = Product::query()
            ->where('sku', $sku)
            ->firstOrFail();

        return $product->stockMovements()
            ->latest('id')
            ->paginate(
                $perPage
            );;
    }
}
