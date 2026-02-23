<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SmartWatchStoreRequest;
use App\Http\Requests\Api\SmartWatchUpdateRequest;
use App\Http\Resources\Api\SmartWatchResource;
use App\Models\SmartWatch;
use App\Services\SmartWatchService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class SmartWatchController extends Controller
{
    public function __construct(
        private SmartWatchService $service
    ) {}

    #[OA\Get(path: '/smart-watches', tags: ['Smart Watches'], summary: 'List all smart watches', operationId: 'smartWatches.index', responses: [new OA\Response(response: 200, description: 'OK')])]
    public function index(): JsonResponse
    {
        return SmartWatchResource::collection($this->service->list())->response();
    }

    #[OA\Post(path: '/smart-watches', tags: ['Smart Watches'], summary: 'Create smart watch', operationId: 'smartWatches.store', security: [['bearerAuth' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(required: ['name', 'price'], example: ['name' => 'Apple Watch', 'price' => 399.99])), responses: [new OA\Response(response: 201, description: 'Created'), new OA\Response(response: 401, description: 'Unauthenticated')])]
    public function store(SmartWatchStoreRequest $request): JsonResponse
    {
        $smartWatch = $this->service->create($request->validated());

        return (new SmartWatchResource($smartWatch))->response()->setStatusCode(201);
    }

    #[OA\Get(path: '/smart-watches/{id}', tags: ['Smart Watches'], summary: 'Get smart watch by ID', operationId: 'smartWatches.show', parameters: [new OA\PathParameter(name: 'id', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'OK'), new OA\Response(response: 404, description: 'Not found')])]
    public function show(SmartWatch $smartWatch): JsonResponse
    {
        return (new SmartWatchResource($smartWatch))->response();
    }

    #[OA\Put(path: '/smart-watches/{id}', tags: ['Smart Watches'], summary: 'Update smart watch', operationId: 'smartWatches.update', security: [['bearerAuth' => []]], parameters: [new OA\PathParameter(name: 'id', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'OK'), new OA\Response(response: 401, description: 'Unauthenticated'), new OA\Response(response: 404, description: 'Not found')])]
    public function update(SmartWatchUpdateRequest $request, SmartWatch $smartWatch): JsonResponse
    {
        $smartWatch = $this->service->update($smartWatch, $request->validated());

        return (new SmartWatchResource($smartWatch))->response();
    }

    #[OA\Delete(path: '/smart-watches/{id}', tags: ['Smart Watches'], summary: 'Delete smart watch', operationId: 'smartWatches.destroy', security: [['bearerAuth' => []]], parameters: [new OA\PathParameter(name: 'id', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 204, description: 'Deleted'), new OA\Response(response: 401, description: 'Unauthenticated'), new OA\Response(response: 404, description: 'Not found')])]
    public function destroy(SmartWatch $smartWatch): JsonResponse
    {
        $this->service->delete($smartWatch);

        return response()->json(null, 204);
    }
}
