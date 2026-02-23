<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Http\JsonResponse;

class InvoiceController extends Controller
{
    public function __construct(
        private InvoiceService $service
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->service->list());
    }

    public function show(Invoice $invoice): JsonResponse
    {
        $invoice = $this->service->findById($invoice->id) ?? $invoice->load('basket.items');

        return response()->json($invoice);
    }
}
