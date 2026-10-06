<?php

namespace App\Interfaces;

interface StockLevelRepositoryInterface
{
    public function get($product_id,$warehouse_id,$page = 1, $perPage = 10);
}
