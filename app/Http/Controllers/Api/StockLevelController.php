<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StockLevelIndexRequest;
use App\Http\Resources\StockLevelResource;
use App\Models\StockLevel;

class StockLevelController extends Controller
{
    public function index(StockLevelIndexRequest $request)
    {
        $query = StockLevel::query()
            ->orderBy('id');

        $query->when(
            $request->integer('product_id'),
            fn ($query, $productId) =>
            $query->where('product_id', $productId)
        );

        $query->when(
            $request->integer('warehouse_id'),
            fn ($query, $warehouseId) =>
            $query->where('warehouse_id', $warehouseId)
        );

        $stockLevels = $query->paginate(
            $request->integer('per_page', 20)
        );

        return StockLevelResource::collection($stockLevels);
    }
}
