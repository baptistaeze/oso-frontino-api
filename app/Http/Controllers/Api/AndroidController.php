<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AndroidStoreRequest;
use App\Http\Requests\Api\AndroidUpdateRequest;
use App\Http\Resources\Api\AndroidResource;
use App\Models\Android;
use App\Services\AndroidService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class AndroidController extends Controller
{
    public function __construct(
        private AndroidService $service
    ) {}

    #[OA\Get(path: '/androids', tags: ['Androids'], summary: 'List all androids', operationId: 'androids.index', responses: [new OA\Response(response: 200, description: 'OK')])]
    public function index(): JsonResponse
    {
        return AndroidResource::collection($this->service->list())->response();
    }

    #[OA\Post(path: '/androids', tags: ['Androids'], summary: 'Create android', operationId: 'androids.store', security: [['bearerAuth' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(required: ['name', 'price'], example: ['name' => 'Samsung Galaxy', 'price' => 899.99])), responses: [new OA\Response(response: 201, description: 'Created'), new OA\Response(response: 401, description: 'Unauthenticated')])]
    public function store(AndroidStoreRequest $request): JsonResponse
    {
        $android = $this->service->create($request->validated());

        return (new AndroidResource($android))->response()->setStatusCode(201);
    }

    #[OA\Get(path: '/androids/{id}', tags: ['Androids'], summary: 'Get android by ID', operationId: 'androids.show', parameters: [new OA\PathParameter(name: 'id', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'OK'), new OA\Response(response: 404, description: 'Not found')])]
    public function show(Android $android): JsonResponse
    {
        return (new AndroidResource($android))->response();
    }

    #[OA\Put(path: '/androids/{id}', tags: ['Androids'], summary: 'Update android', operationId: 'androids.update', security: [['bearerAuth' => []]], parameters: [new OA\PathParameter(name: 'id', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'OK'), new OA\Response(response: 401, description: 'Unauthenticated'), new OA\Response(response: 404, description: 'Not found')])]
    public function update(AndroidUpdateRequest $request, Android $android): JsonResponse
    {
        $android = $this->service->update($android, $request->validated());

        return (new AndroidResource($android))->response();
    }

    #[OA\Delete(path: '/androids/{id}', tags: ['Androids'], summary: 'Delete android', operationId: 'androids.destroy', security: [['bearerAuth' => []]], parameters: [new OA\PathParameter(name: 'id', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 204, description: 'Deleted'), new OA\Response(response: 401, description: 'Unauthenticated'), new OA\Response(response: 404, description: 'Not found')])]
    public function destroy(Android $android): JsonResponse
    {
        $this->service->delete($android);

        return response()->json(null, 204);
    }
}
