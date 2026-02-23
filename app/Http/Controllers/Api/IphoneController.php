<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\IphoneStoreRequest;
use App\Http\Requests\Api\IphoneUpdateRequest;
use App\Models\Iphone;
use App\Services\IphoneService;
use Illuminate\Http\JsonResponse;

class IphoneController extends Controller
{
    public function __construct(
        private IphoneService $service
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->service->list());
    }

    public function store(IphoneStoreRequest $request): JsonResponse
    {
        $iphone = $this->service->create($request->validated());

        return response()->json($iphone, 201);
    }

    public function show(Iphone $iphone): JsonResponse
    {
        return response()->json($iphone);
    }

    public function update(IphoneUpdateRequest $request, Iphone $iphone): JsonResponse
    {
        $iphone = $this->service->update($iphone, $request->validated());

        return response()->json($iphone);
    }

    public function destroy(Iphone $iphone): JsonResponse
    {
        $this->service->delete($iphone);

        return response()->json(null, 204);
    }
}
