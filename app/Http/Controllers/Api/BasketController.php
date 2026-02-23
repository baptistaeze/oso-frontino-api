<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BasketItemRequest;
use App\Http\Requests\Api\BasketUpdateRequest;
use App\Http\Resources\Api\BasketResource;
use App\Http\Resources\Api\BasketItemResource;
use App\Http\Resources\Api\ChargeBasketResource;
use App\Models\Basket;
use App\Services\BasketService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class BasketController extends Controller
{
    public function __construct(
        private BasketService $service
    ) {}

    #[OA\Get(path: '/baskets', tags: ['Baskets'], summary: 'List all baskets', operationId: 'baskets.index', responses: [new OA\Response(response: 200, description: 'OK')])]
    public function index(): JsonResponse
    {
        return BasketResource::collection($this->service->list())->response();
    }

    #[OA\Post(path: '/baskets', tags: ['Baskets'], summary: 'Create basket', operationId: 'baskets.store', security: [['bearerAuth' => []]], responses: [new OA\Response(response: 201, description: 'Created'), new OA\Response(response: 401, description: 'Unauthenticated')])]
    public function store(): JsonResponse
    {
        $basket = $this->service->create();

        return (new BasketResource($basket->load('items')))->response()->setStatusCode(201);
    }

    #[OA\Get(path: '/baskets/{id}', tags: ['Baskets'], summary: 'Get basket by ID', operationId: 'baskets.show', parameters: [new OA\PathParameter(name: 'id', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'OK'), new OA\Response(response: 404, description: 'Not found')])]
    public function show(Basket $basket): JsonResponse
    {
        return (new BasketResource($basket->load('items')))->response();
    }

    #[OA\Put(path: '/baskets/{id}', tags: ['Baskets'], summary: 'Update basket', operationId: 'baskets.update', security: [['bearerAuth' => []]], parameters: [new OA\PathParameter(name: 'id', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'OK'), new OA\Response(response: 401, description: 'Unauthenticated'), new OA\Response(response: 404, description: 'Not found')])]
    public function update(BasketUpdateRequest $request, Basket $basket): JsonResponse
    {
        $basket = $this->service->update($basket, $request->validated());

        return (new BasketResource($basket))->response();
    }

    #[OA\Delete(path: '/baskets/{id}', tags: ['Baskets'], summary: 'Delete basket', operationId: 'baskets.destroy', security: [['bearerAuth' => []]], parameters: [new OA\PathParameter(name: 'id', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 204, description: 'Deleted'), new OA\Response(response: 401, description: 'Unauthenticated'), new OA\Response(response: 404, description: 'Not found')])]
    public function destroy(Basket $basket): JsonResponse
    {
        $this->service->delete($basket);

        return response()->json(null, 204);
    }

    #[OA\Post(path: '/baskets/{id}/items', tags: ['Baskets'], summary: 'Add item to basket', operationId: 'baskets.addItem', security: [['bearerAuth' => []]], parameters: [new OA\PathParameter(name: 'id', required: true, schema: new OA\Schema(type: 'integer'))], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(required: ['product_type', 'product_id', 'price'], example: ['product_type' => 'iphone', 'product_id' => 1, 'price' => 999.99, 'quantity' => 2])), responses: [new OA\Response(response: 201, description: 'Created'), new OA\Response(response: 401, description: 'Unauthenticated'), new OA\Response(response: 404, description: 'Not found')])]
    public function addItem(BasketItemRequest $request, Basket $basket): JsonResponse
    {
        $item = $this->service->addItem($basket, $request->validated());

        return (new BasketItemResource($item))->response()->setStatusCode(201);
    }

    #[OA\Post(path: '/baskets/{id}/charge', tags: ['Baskets'], summary: 'Charge basket (complete purchase)', operationId: 'baskets.charge', security: [['bearerAuth' => []]], parameters: [new OA\PathParameter(name: 'id', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'OK'), new OA\Response(response: 401, description: 'Unauthenticated'), new OA\Response(response: 404, description: 'Not found'), new OA\Response(response: 422, description: 'Basket empty or already charged')])]
    public function charge(Basket $basket): JsonResponse
    {
        $result = $this->service->charge($basket);

        return (new ChargeBasketResource($result))->response();
    }
}
