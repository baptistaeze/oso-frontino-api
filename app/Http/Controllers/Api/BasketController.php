<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BasketItemRequest;
use App\Http\Requests\Api\BasketUpdateRequest;
use App\Models\Basket;
use App\Services\BasketService;
use Illuminate\Http\JsonResponse;

class BasketController extends Controller
{
    public function __construct(
        private BasketService $service
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->service->list());
    }

    public function store(): JsonResponse
    {
        $basket = $this->service->create();

        return response()->json($basket->load('items'), 201);
    }

    public function show(Basket $basket): JsonResponse
    {
        return response()->json($basket->load('items'));
    }

    public function update(BasketUpdateRequest $request, Basket $basket): JsonResponse
    {
        $basket = $this->service->update($basket, $request->validated());

        return response()->json($basket);
    }

    public function destroy(Basket $basket): JsonResponse
    {
        $this->service->delete($basket);

        return response()->json(null, 204);
    }

    public function addItem(BasketItemRequest $request, Basket $basket): JsonResponse
    {
        $item = $this->service->addItem($basket, $request->validated());

        return response()->json($item, 201);
    }

    public function charge(Basket $basket): JsonResponse
    {
        $result = $this->service->charge($basket);

        return response()->json($result);
    }
}
