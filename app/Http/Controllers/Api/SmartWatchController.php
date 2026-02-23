<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SmartWatchStoreRequest;
use App\Http\Requests\Api\SmartWatchUpdateRequest;
use App\Models\SmartWatch;
use App\Services\SmartWatchService;
use Illuminate\Http\JsonResponse;

class SmartWatchController extends Controller
{
    public function __construct(
        private SmartWatchService $service
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->service->list());
    }

    public function store(SmartWatchStoreRequest $request): JsonResponse
    {
        $smartWatch = $this->service->create($request->validated());

        return response()->json($smartWatch, 201);
    }

    public function show(SmartWatch $smartWatch): JsonResponse
    {
        return response()->json($smartWatch);
    }

    public function update(SmartWatchUpdateRequest $request, SmartWatch $smartWatch): JsonResponse
    {
        $smartWatch = $this->service->update($smartWatch, $request->validated());

        return response()->json($smartWatch);
    }

    public function destroy(SmartWatch $smartWatch): JsonResponse
    {
        $this->service->delete($smartWatch);

        return response()->json(null, 204);
    }
}
