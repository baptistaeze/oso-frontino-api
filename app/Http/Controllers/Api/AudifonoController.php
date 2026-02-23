<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AudifonoStoreRequest;
use App\Http\Requests\Api\AudifonoUpdateRequest;
use App\Http\Resources\Api\AudifonoResource;
use App\Models\Audifono;
use App\Services\AudifonoService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class AudifonoController extends Controller
{
    public function __construct(
        private AudifonoService $service
    ) {}

    #[OA\Get(path: '/audifonos', tags: ['Audifonos'], summary: 'List all audifonos', operationId: 'audifonos.index', responses: [new OA\Response(response: 200, description: 'OK')])]
    public function index(): JsonResponse
    {
        return AudifonoResource::collection($this->service->list())->response();
    }

    #[OA\Post(path: '/audifonos', tags: ['Audifonos'], summary: 'Create audifono', operationId: 'audifonos.store', security: [['bearerAuth' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(required: ['name', 'price'], example: ['name' => 'AirPods Pro', 'price' => 249.99])), responses: [new OA\Response(response: 201, description: 'Created'), new OA\Response(response: 401, description: 'Unauthenticated')])]
    public function store(AudifonoStoreRequest $request): JsonResponse
    {
        $audifono = $this->service->create($request->validated());

        return (new AudifonoResource($audifono))->response()->setStatusCode(201);
    }

    #[OA\Get(path: '/audifonos/{id}', tags: ['Audifonos'], summary: 'Get audifono by ID', operationId: 'audifonos.show', parameters: [new OA\PathParameter(name: 'id', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'OK'), new OA\Response(response: 404, description: 'Not found')])]
    public function show(Audifono $audifono): JsonResponse
    {
        return (new AudifonoResource($audifono))->response();
    }

    #[OA\Put(path: '/audifonos/{id}', tags: ['Audifonos'], summary: 'Update audifono', operationId: 'audifonos.update', security: [['bearerAuth' => []]], parameters: [new OA\PathParameter(name: 'id', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'OK'), new OA\Response(response: 401, description: 'Unauthenticated'), new OA\Response(response: 404, description: 'Not found')])]
    public function update(AudifonoUpdateRequest $request, Audifono $audifono): JsonResponse
    {
        $audifono = $this->service->update($audifono, $request->validated());

        return (new AudifonoResource($audifono))->response();
    }

    #[OA\Delete(path: '/audifonos/{id}', tags: ['Audifonos'], summary: 'Delete audifono', operationId: 'audifonos.destroy', security: [['bearerAuth' => []]], parameters: [new OA\PathParameter(name: 'id', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 204, description: 'Deleted'), new OA\Response(response: 401, description: 'Unauthenticated'), new OA\Response(response: 404, description: 'Not found')])]
    public function destroy(Audifono $audifono): JsonResponse
    {
        $this->service->delete($audifono);

        return response()->json(null, 204);
    }
}
