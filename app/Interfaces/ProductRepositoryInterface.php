<?php

namespace App\Interfaces;

interface ProductRepositoryInterface
{
    public function get_product_history_by_sku($sku,$page = 1, $perPage = 10);
}
