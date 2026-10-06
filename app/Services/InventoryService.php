<?php

namespace App\Services;

use App\Enums\StockMovementsEnum;
use App\Exceptions\InsufficientStockException;
use App\Models\StockLevel;
use App\Models\StockMovement;
use App\Tenancy\TenancyContext;
use Illuminate\Support\Facades\DB;
class InventoryService
{
    public function recordMovement(
        int $productId,
        StockMovementsEnum $type,
        int $quantity,
        int $warehouseId,
        array $meta = [],
        string|null $reference = null,
    ): StockMovement {
        return DB::transaction(function () use (
            $productId,
            $type,
            $quantity,
            $warehouseId,
            $meta,
            $reference
        ) {
            return match ($type) {
                StockMovementsEnum::IN => $this->stockIn(
                    $productId,
                    $warehouseId,
                    $quantity,
                    $meta,
                    $reference
                ),

                StockMovementsEnum::OUT => $this->stockOut(
                    $productId,
                    $warehouseId,
                    $quantity,
                    $meta,
                    $reference
                ),

                StockMovementsEnum::TRANSFER => $this->transfer(
                    $productId,
                    $quantity,
                    $meta,
                    $reference
                ),
            };
        });
    }


    private function stockIn(
        int $productId,
        int $warehouseId,
        int $quantity,
        array $meta = [],
        string $reference = null
    ): StockMovement {
        $stock = StockLevel::query()
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->lockForUpdate()
            ->firstOrCreate([
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
            ], [
                'quantity' => 0,
                'tenant_id' => app(TenancyContext::class)->id(),
            ]);

        $stock->increment('quantity', $quantity);

        return StockMovement::create([
            'product_id' => $productId,
            'warehouse_id' => $warehouseId,
            'type' => StockMovementsEnum::IN,
            'quantity' => $quantity,
            'meta' => $meta,
            'tenant_id' => app(TenancyContext::class)->id(),
            'reference' => $reference ?? 'none'
        ]);
    }

    private function stockOut(
        int $productId,
        int $warehouseId,
        int $quantity,
        array $meta = [],
        string $reference = null
    ): StockMovement {
        $stock = StockLevel::query()
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->lockForUpdate()
            ->firstOrFail();

        if ($stock->quantity < $quantity) {
            throw new InsufficientStockException();
        }

        $stock->decrement('quantity', $quantity);

        return StockMovement::create([
            'product_id' => $productId,
            'warehouse_id' => $warehouseId,
            'type' => StockMovementsEnum::OUT,
            'quantity' => $quantity,
            'meta' => $meta,
            'tenant_id' => app(TenancyContext::class)->id(),
            'reference' => $reference ?? 'none'
        ]);
    }

    private function transfer(
        int $productId,
        int $quantity,
        array $meta,
        string $reference = null
    ): StockMovement {
        $fromWarehouseId = $meta['from_warehouse_id'];
        $toWarehouseId = $meta['to_warehouse_id'];

        if ($fromWarehouseId === $toWarehouseId) {
            throw new \InvalidArgumentException(
                'Source and destination warehouses must be different.'
            );
        }

        $warehouseIds = [
            $fromWarehouseId,
            $toWarehouseId,
        ];

        sort($warehouseIds);

        $stocks = StockLevel::query()
            ->where('product_id', $productId)
            ->whereIn('warehouse_id', $warehouseIds)
            ->orderBy('warehouse_id')
            ->lockForUpdate()
            ->get()
            ->keyBy('warehouse_id');

        $sourceStock = $stocks->get($fromWarehouseId);

        if (!$sourceStock || $sourceStock->quantity < $quantity) {
            throw new InsufficientStockException();
        }

        $destinationStock = $stocks->get($toWarehouseId);

        if (!$destinationStock) {
            $destinationStock = StockLevel::create([
                'product_id' => $productId,
                'warehouse_id' => $toWarehouseId,
                'quantity' => 0,
                'tenant_id' => app(TenancyContext::class)->id()
            ]);
        }

        $sourceStock->decrement('quantity', $quantity);
        $destinationStock->increment('quantity', $quantity);

        return StockMovement::create([
            'product_id' => $productId,
            'warehouse_id' => $fromWarehouseId,
            'type' => StockMovementsEnum::TRANSFER,
            'quantity' => $quantity,
            'meta' => [
                'from_warehouse_id' => $fromWarehouseId,
                'to_warehouse_id' => $toWarehouseId,
            ],
            'tenant_id' => app(TenancyContext::class)->id(),
            'reference' => $reference ?? 'none'
        ]);
    }
}
