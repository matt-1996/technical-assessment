<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Enums\StockMovementsEnum;
use App\Models\StockLevel;
use App\Models\StockMovement;
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;
class InventoryReconcile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:reconcile {tenant : Tenant ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = (int) $this->argument('tenant');

        $tenant = Tenant::find($tenantId);

        if (! $tenant) {
            $this->error("Tenant {$tenantId} not found.");

            return self::FAILURE;
        }

        $this->info("Reconciling tenant #{$tenant->id}...");

        $discrepancies = 0;

        $expected = [];

        StockMovement::query()
            ->where('tenant_id', $tenant->id)
            ->orderBy('id')
            ->cursor()
            ->each(function (StockMovement $movement) use (&$expected) {

                if ($movement->type === StockMovementsEnum::IN) {
                    $this->addQuantity(
                        $expected,
                        $movement->product_id,
                        $movement->warehouse_id,
                        $movement->quantity
                    );

                    return;
                }

                if ($movement->type === StockMovementsEnum::OUT) {
                    $this->addQuantity(
                        $expected,
                        $movement->product_id,
                        $movement->warehouse_id,
                        -$movement->quantity
                    );

                    return;
                }

                if ($movement->type === StockMovementsEnum::TRANSFER) {
                    $meta = $movement->meta ?? [];

                    $fromWarehouseId = $meta['from_warehouse_id'] ?? null;
                    $toWarehouseId = $meta['to_warehouse_id'] ?? null;

                    if (! $fromWarehouseId || ! $toWarehouseId) {
                        Log::error('Invalid transfer movement during reconciliation.', [
                            'tenant_id' => $movement->tenant_id,
                            'movement_id' => $movement->id,
                            'product_id' => $movement->product_id,
                            'warehouse_id' => $movement->warehouse_id,
                            'meta' => $meta,
                        ]);

                        return;
                    }

                    $this->addQuantity(
                        $expected,
                        $movement->product_id,
                        (int) $fromWarehouseId,
                        -$movement->quantity
                    );

                    $this->addQuantity(
                        $expected,
                        $movement->product_id,
                        (int) $toWarehouseId,
                        $movement->quantity
                    );
                }
            });

        StockLevel::query()
            ->where('tenant_id', $tenant->id)
            ->orderBy('id')
            ->cursor()
            ->each(function (StockLevel $stock) use (
                &$expected,
                &$discrepancies
            ) {
                $key = $this->key(
                    $stock->product_id,
                    $stock->warehouse_id
                );

                $expectedQuantity = $expected[$key] ?? 0;
                $actualQuantity = (int) $stock->quantity;

                if ($expectedQuantity !== $actualQuantity) {
                    $discrepancies++;

                    $this->logDiscrepancy(
                        tenantId: $stock->tenant_id,
                        productId: $stock->product_id,
                        warehouseId: $stock->warehouse_id,
                        expected: $expectedQuantity,
                        actual: $actualQuantity
                    );
                }

                unset($expected[$key]);
            });

        foreach ($expected as $key => $expectedQuantity) {
            if ($expectedQuantity === 0) {
                continue;
            }

            [$productId, $warehouseId] = explode(':', $key);

            $discrepancies++;

            $this->logDiscrepancy(
                tenantId: $tenant->id,
                productId: (int) $productId,
                warehouseId: (int) $warehouseId,
                expected: $expectedQuantity,
                actual: 0
            );
        }

        if ($discrepancies === 0) {
            $this->info('Reconciliation completed. No discrepancies found.');

            return self::SUCCESS;
        }

        $this->warn(
            "Reconciliation completed. {$discrepancies} discrepancy(ies) found."
        );

        return self::FAILURE;
    }

    private function addQuantity(
        array &$expected,
        int $productId,
        int $warehouseId,
        int $quantity
    ): void {
        $key = $this->key($productId, $warehouseId);

        $expected[$key] = ($expected[$key] ?? 0) + $quantity;
    }

    private function key(int $productId, int $warehouseId): string
    {
        return "{$productId}:{$warehouseId}";
    }

    private function logDiscrepancy(
        int $tenantId,
        int $productId,
        int $warehouseId,
        int $expected,
        int $actual
    ): void {
        Log::warning('Inventory reconciliation discrepancy', [
            'tenant_id' => $tenantId,
            'product_id' => $productId,
            'warehouse_id' => $warehouseId,
            'expected' => $expected,
            'actual' => $actual,
            'difference' => $actual - $expected,
        ]);

        $this->line(sprintf(
            'Product: %d | Warehouse: %d | Expected: %d | Actual: %d',
            $productId,
            $warehouseId,
            $expected,
            $actual
        ));
    }
}
