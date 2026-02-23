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
        return response()->json($this->service->listar());
    }

    public function store(AudifonoStoreRequest $request): JsonResponse
    {
        $audifono = $this->service->crear($request->validated());

        return response()->json($audifono, 201);
    }

    public function show(Audifono $audifono): JsonResponse
    {
        return response()->json($audifono);
    }

    public function update(AudifonoUpdateRequest $request, Audifono $audifono): JsonResponse
    {
        $audifono = $this->service->actualizar($audifono, $request->validated());

        return response()->json($audifono);
    }

    public function destroy(Audifono $audifono): JsonResponse
    {
        $this->service->eliminar($audifono);

        return response()->json(null, 204);
    }
}
