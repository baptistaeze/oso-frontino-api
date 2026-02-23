<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AudifonoStoreRequest;
use App\Http\Requests\Api\AudifonoUpdateRequest;
use App\Models\Audifono;
use App\Services\AudifonoService;
use Illuminate\Http\JsonResponse;

class AudifonoController extends Controller
{
    public function __construct(
        private AudifonoService $service
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->service->list());
    }

    public function store(AudifonoStoreRequest $request): JsonResponse
    {
        $audifono = $this->service->create($request->validated());

        return response()->json($audifono, 201);
    }

    public function show(Audifono $audifono): JsonResponse
    {
        return response()->json($audifono);
    }

    public function update(AudifonoUpdateRequest $request, Audifono $audifono): JsonResponse
    {
        $audifono = $this->service->update($audifono, $request->validated());

        return response()->json($audifono);
    }

    public function destroy(Audifono $audifono): JsonResponse
    {
        $this->service->delete($audifono);

        return response()->json(null, 204);
    }
}
