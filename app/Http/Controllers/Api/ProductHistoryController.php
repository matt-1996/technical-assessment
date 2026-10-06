<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductHistoryRequest;
use App\Http\Resources\StockMovementResource;
use App\Models\Product;

class ProductHistoryController extends Controller
{
    public function index(ProductHistoryRequest $request,string $sku)
    {
        $product = Product::query()
            ->where('sku', $sku)
            ->firstOrFail();

        $movements = $product->stockMovements()
            ->latest('id')
            ->paginate(
                $request->integer('per_page', 20)
            );

        return StockMovementResource::collection($movements);
    }
}
