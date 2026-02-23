<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\InvoiceResource;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class InvoiceController extends Controller
{
    public function __construct(
        private InvoiceService $service
    ) {}

    #[OA\Get(path: '/invoices', tags: ['Invoices'], summary: 'List all invoices', operationId: 'invoices.index', responses: [new OA\Response(response: 200, description: 'OK')])]
    public function index(): JsonResponse
    {
        return InvoiceResource::collection($this->service->list())->response();
    }

    #[OA\Get(path: '/invoices/{id}', tags: ['Invoices'], summary: 'Get invoice by ID', operationId: 'invoices.show', parameters: [new OA\PathParameter(name: 'id', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'OK'), new OA\Response(response: 404, description: 'Not found')])]
    public function show(Invoice $invoice): JsonResponse
    {
        $invoice = $this->service->findById($invoice->id) ?? $invoice->load('basket.items');

        return (new InvoiceResource($invoice))->response();
    }
}
