<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StockLevelIndexRequest;
use App\Http\Resources\StockLevelResource;
use App\Interfaces\StockLevelRepositoryInterface;
use App\Models\StockLevel;

class StockLevelController extends Controller
{
    private StockLevelRepositoryInterface $stockLevelRepository;
    public function __construct(StockLevelRepositoryInterface $stockLevelRepository)
    {
        $this->stockLevelRepository = $stockLevelRepository;
    }
    public function index(StockLevelIndexRequest $request)
    {
        return StockLevelResource::collection($this->stockLevelRepository->get($request->product_id,$request->warehouse_id, $request->page, $request->per_page));
    }
}
