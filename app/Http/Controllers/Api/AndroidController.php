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

    #[OA\Get(
        path: '/androids',
        tags: ['Androids'],
        summary: 'List all androids',
        operationId: 'androids.index',
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(example: ['data' => [['id' => 1, 'name' => 'Samsung Galaxy', 'price' => 899.99, 'quantityStock' => 5, 'imagePath' => 'http://localhost:8002/storage/products/androids/img.jpg']]])),
        ]
    )]
    public function index(): JsonResponse
    {
        return AndroidResource::collection($this->service->list())->response();
    }

    #[OA\Post(
        path: '/androids',
        tags: ['Androids'],
        summary: 'Create android',
        operationId: 'androids.store',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: [
                new OA\JsonContent(
                    required: ['name', 'price'],
                    example: ['name' => 'Samsung Galaxy', 'description' => 'Android smartphone', 'price' => 899.99, 'discount' => 10, 'quantity_stock' => 5, 'amount' => 809.99, 'image_path' => 'storage/products/androids/img.jpg']
                ),
                new OA\MediaType(
                    mediaType: 'multipart/form-data',
                    schema: new OA\Schema(
                        type: 'object',
                        required: ['name', 'price'],
                        properties: [
                            new OA\Property(property: 'name', type: 'string', example: 'Samsung Galaxy'),
                            new OA\Property(property: 'description', type: 'string'),
                            new OA\Property(property: 'price', type: 'number', example: 899.99),
                            new OA\Property(property: 'discount', type: 'number'),
                            new OA\Property(property: 'quantity_stock', type: 'integer', example: 5),
                            new OA\Property(property: 'amount', type: 'number'),
                            new OA\Property(property: 'image', type: 'string', format: 'binary', description: 'Imagen JPG, PNG o GIF (max 5MB)'),
                            new OA\Property(property: 'image_path', type: 'string', description: 'Path alternativo si no subes imagen'),
                        ]
                    )
                ),
            ]
        ),
        responses: [new OA\Response(response: 201, description: 'Created'), new OA\Response(response: 401, description: 'Unauthenticated')]
    )]
    public function store(AndroidStoreRequest $request): JsonResponse
    {
        $android = $this->service->create($request->validatedData());

        return (new AndroidResource($android))->response()->setStatusCode(201);
    }

    #[OA\Get(
        path: '/androids/{id}',
        tags: ['Androids'],
        summary: 'Get android by ID',
        operationId: 'androids.show',
        parameters: [new OA\PathParameter(name: 'id', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'OK'), new OA\Response(response: 404, description: 'Not found')]
    )]
    public function show(Android $android): JsonResponse
    {
        return (new AndroidResource($android))->response();
    }

    #[OA\Put(
        path: '/androids/{id}',
        tags: ['Androids'],
        summary: 'Update android',
        operationId: 'androids.update',
        security: [['bearerAuth' => []]],
        parameters: [new OA\PathParameter(name: 'id', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(
            content: [
                new OA\JsonContent(example: ['name' => 'Samsung Galaxy S24', 'description' => 'Updated', 'price' => 949.99, 'discount' => 5, 'quantity_stock' => 10, 'amount' => 902.99, 'image_path' => 'storage/products/androids/img.jpg']),
                new OA\MediaType(
                    mediaType: 'multipart/form-data',
                    schema: new OA\Schema(
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'name', type: 'string'),
                            new OA\Property(property: 'description', type: 'string'),
                            new OA\Property(property: 'price', type: 'number'),
                            new OA\Property(property: 'discount', type: 'number'),
                            new OA\Property(property: 'quantity_stock', type: 'integer'),
                            new OA\Property(property: 'amount', type: 'number'),
                            new OA\Property(property: 'image', type: 'string', format: 'binary', description: 'Imagen JPG, PNG o GIF'),
                            new OA\Property(property: 'image_path', type: 'string'),
                        ]
                    )
                ),
            ]
        ),
        responses: [new OA\Response(response: 200, description: 'OK'), new OA\Response(response: 401, description: 'Unauthenticated'), new OA\Response(response: 404, description: 'Not found')]
    )]
    public function update(AndroidUpdateRequest $request, Android $android): JsonResponse
    {
        $android = $this->service->update($android, $request->validatedData());

        return (new AndroidResource($android))->response();
    }

    #[OA\Delete(path: '/androids/{id}', tags: ['Androids'], summary: 'Delete android', operationId: 'androids.destroy', security: [['bearerAuth' => []]], parameters: [new OA\PathParameter(name: 'id', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 204, description: 'Deleted'), new OA\Response(response: 401, description: 'Unauthenticated'), new OA\Response(response: 404, description: 'Not found')])]
    public function destroy(Android $android): JsonResponse
    {
        $this->service->delete($android);

        return response()->json(null, 204);
    }
}
