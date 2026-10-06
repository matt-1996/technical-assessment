<?php

namespace App\Http\Controllers\Api;

use App\Enums\StockMovementsEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStockMovementRequest;
use App\Http\Resources\StockMovementResource;
use App\Services\InventoryService;
use Illuminate\Http\JsonResponse;
class StockMovementController extends Controller
{
    public function __construct(
        private readonly InventoryService $inventoryService
    ) {
    }

    public function store(
        StoreStockMovementRequest $request
    ): JsonResponse {
        $movement = $this->inventoryService->recordMovement(
            $request->product_id,
            StockMovementsEnum::from($request->type),
            $request->quantity,
            $request->warehouse_id,
            $request->meta ?? []
        );

        return (new StockMovementResource($movement))
            ->response()
            ->setStatusCode(201);
    }
}
