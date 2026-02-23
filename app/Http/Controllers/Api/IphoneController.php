<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\IphoneStoreRequest;
use App\Http\Requests\Api\IphoneUpdateRequest;
use App\Http\Resources\Api\IphoneResource;
use App\Models\Iphone;
use App\Services\IphoneService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class IphoneController extends Controller
{
    public function __construct(
        private IphoneService $service
    ) {}

    #[OA\Get(
        path: '/iphones',
        tags: ['iPhones'],
        summary: 'List all iPhones',
        operationId: 'iphones.index',
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of iPhones',
                content: new OA\JsonContent(example: [
                    'data' => [[
                        'id' => 1,
                        'name' => 'iPhone 15 Pro',
                        'description' => 'Latest iPhone model',
                        'price' => 999.99,
                        'discount' => 10.0,
                        'quantityStock' => 5,
                        'amount' => 899.99,
                        'imagePath' => 'storage/iphones/iphone15.jpg',
                        'createdAt' => '2025-02-23T10:00:00+00:00',
                        'updatedAt' => '2025-02-23T10:00:00+00:00',
                    ]],
                ])
            ),
        ]
    )]
    public function index(): JsonResponse
    {
        return IphoneResource::collection($this->service->list())->response();
    }

    #[OA\Post(
        path: '/iphones',
        tags: ['iPhones'],
        summary: 'Create iPhone',
        operationId: 'iphones.store',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: [
                new OA\JsonContent(
                    required: ['name', 'price'],
                    example: [
                        'name' => 'iPhone 15 Pro',
                        'description' => 'Latest iPhone model',
                        'price' => 999.99,
                        'discount' => 10,
                        'quantity_stock' => 5,
                        'amount' => 899.99,
                        'image_path' => 'storage/iphones/iphone15.jpg',
                    ]
                ),
                new OA\MediaType(
                    mediaType: 'multipart/form-data',
                    schema: new OA\Schema(
                        type: 'object',
                        required: ['name', 'price'],
                        properties: [
                            new OA\Property(property: 'name', type: 'string', example: 'iPhone 15 Pro'),
                            new OA\Property(property: 'description', type: 'string'),
                            new OA\Property(property: 'price', type: 'number', example: 999.99),
                            new OA\Property(property: 'discount', type: 'number'),
                            new OA\Property(property: 'quantity_stock', type: 'integer', example: 5),
                            new OA\Property(property: 'amount', type: 'number'),
                            new OA\Property(property: 'image', type: 'string', format: 'binary', description: 'Imagen JPG, PNG o GIF (max 5MB)'),
                            new OA\Property(property: 'image_path', type: 'string'),
                        ]
                    )
                ),
            ]
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'iPhone created',
                content: new OA\JsonContent(example: [
                    'data' => [
                        'id' => 1,
                        'name' => 'iPhone 15 Pro',
                        'description' => 'Latest iPhone model',
                        'price' => 999.99,
                        'discount' => 10.0,
                        'quantityStock' => 5,
                        'amount' => 899.99,
                        'imagePath' => 'storage/iphones/iphone15.jpg',
                        'createdAt' => '2025-02-23T10:00:00+00:00',
                        'updatedAt' => '2025-02-23T10:00:00+00:00',
                    ],
                ])
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(IphoneStoreRequest $request): JsonResponse
    {
        $iphone = $this->service->create($request->validatedData());

        return (new IphoneResource($iphone))->response()->setStatusCode(201);
    }

    #[OA\Get(
        path: '/iphones/{id}',
        tags: ['iPhones'],
        summary: 'Get iPhone by ID',
        operationId: 'iphones.show',
        parameters: [new OA\PathParameter(name: 'id', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(
                response: 200,
                description: 'iPhone details',
                content: new OA\JsonContent(example: [
                    'data' => [
                        'id' => 1,
                        'name' => 'iPhone 15 Pro',
                        'description' => 'Latest iPhone model',
                        'price' => 999.99,
                        'discount' => 10.0,
                        'quantityStock' => 5,
                        'amount' => 899.99,
                        'imagePath' => 'storage/iphones/iphone15.jpg',
                        'createdAt' => '2025-02-23T10:00:00+00:00',
                        'updatedAt' => '2025-02-23T10:00:00+00:00',
                    ],
                ])
            ),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function show(Iphone $iphone): JsonResponse
    {
        return (new IphoneResource($iphone))->response();
    }

    #[OA\Put(
        path: '/iphones/{id}',
        tags: ['iPhones'],
        summary: 'Update iPhone',
        operationId: 'iphones.update',
        security: [['bearerAuth' => []]],
        parameters: [new OA\PathParameter(name: 'id', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(
            content: [
                new OA\JsonContent(example: [
                    'name' => 'iPhone 15 Pro Max',
                    'description' => 'Updated description',
                    'price' => 1099.99,
                    'discount' => 15,
                    'quantity_stock' => 10,
                    'amount' => 934.99,
                    'image_path' => 'storage/iphones/iphone15pro.jpg',
                ]),
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
        responses: [
            new OA\Response(response: 200, description: 'iPhone updated'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Not found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function update(IphoneUpdateRequest $request, Iphone $iphone): JsonResponse
    {
        $iphone = $this->service->update($iphone, $request->validatedData());

        return (new IphoneResource($iphone))->response();
    }

    #[OA\Delete(
        path: '/iphones/{id}',
        tags: ['iPhones'],
        summary: 'Delete iPhone',
        operationId: 'iphones.destroy',
        security: [['bearerAuth' => []]],
        parameters: [new OA\PathParameter(name: 'id', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 204, description: 'Deleted'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function destroy(Iphone $iphone): JsonResponse
    {
        $this->service->delete($iphone);

        return response()->json(null, 204);
    }
}
