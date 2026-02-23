<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AndroidStoreRequest;
use App\Http\Requests\Api\AndroidUpdateRequest;
use App\Models\Android;
use App\Services\AndroidService;
use Illuminate\Http\JsonResponse;

class AndroidController extends Controller
{
    public function __construct(
        private AndroidService $service
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->service->list());
    }

    public function store(AndroidStoreRequest $request): JsonResponse
    {
        $android = $this->service->create($request->validated());

        return response()->json($android, 201);
    }

    public function show(Android $android): JsonResponse
    {
        return response()->json($android);
    }

    public function update(AndroidUpdateRequest $request, Android $android): JsonResponse
    {
        $android = $this->service->update($android, $request->validated());

        return response()->json($android);
    }

    public function destroy(Android $android): JsonResponse
    {
        $this->service->delete($android);

        return response()->json(null, 204);
    }
}
