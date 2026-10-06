<?php

namespace App\Repository;

use App\Interfaces\StockLevelRepositoryInterface;
use App\Models\StockLevel;

class StockLevelRepository implements StockLevelRepositoryInterface
{

    public function get($product_id, $warehouse_id, $page = 1, $perPage = 10)
    {
        $query = StockLevel::query()
            ->orderBy('id');

        $query->when(
            $product_id,
            fn ($query, $productId) =>
            $query->where('product_id', $productId)
        );

        $query->when(
            $warehouse_id,
            fn ($query, $warehouseId) =>
            $query->where('warehouse_id', $warehouseId)
        );

        return $query->paginate(
            $perPage
        );
    }
}
