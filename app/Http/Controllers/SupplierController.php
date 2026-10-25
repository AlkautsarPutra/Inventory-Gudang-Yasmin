<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Enums\SupplierStatus;
use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function __construct(
        private readonly SupplierService $supplierService,
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('suppliers.index', [
            'suppliers' => $this->supplierService->paginate(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('suppliers.create', ['statuses' => SupplierStatus::cases()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSupplierRequest $request): RedirectResponse
    {
        $this->supplierService->create(
            $request->validated(),
        );

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier): View
    {
        return view('suppliers.edit', [
            'supplier' => $supplier,
            'statuses' => SupplierStatus::cases(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateSupplierRequest $request,
        Supplier $supplier,
    ): RedirectResponse {
        $this->supplierService->update(
            $supplier,
            $request->validated(),
        );

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier): RedirectResponse
    {
        $this->supplierService->delete($supplier);

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier deactivated successfully.');
    }
}
