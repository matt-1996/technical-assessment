<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductHistoryRequest;
use App\Http\Resources\StockMovementResource;
use App\Interfaces\ProductRepositoryInterface;
use App\Models\Product;

class ProductHistoryController extends Controller
{
    private ProductRepositoryInterface $productRepository;
    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }
    public function index(ProductHistoryRequest $request,string $sku)
    {
        return StockMovementResource::collection($this->productRepository->get_product_history_by_sku($sku,$request->page ?? 1, $request->per_page));
    }
}
