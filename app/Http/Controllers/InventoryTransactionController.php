<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Inventory\Exceptions\InvalidInventoryTransaction;
use App\Enums\InventoryTransactionType;
use App\Http\Requests\ReverseInventoryTransactionRequest;
use App\Http\Requests\StoreInventoryTransactionRequest;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\InventoryTransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class InventoryTransactionController extends Controller
{
    public function __construct(private readonly InventoryTransactionService $transactions)
    {
    }

    public function index(): View
    {
        return view('inventory-transactions.index', [
            'transactions' => $this->transactions->paginate(),
        ]);
    }

    public function create(): View
    {
        return view('inventory-transactions.create', [
            'products' => Product::query()->where('status', 'ACTIVE')->orderBy('product_name')->get(),
            'suppliers' => Supplier::query()->where('status', 'ACTIVE')->orderBy('company_name')->get(),
            'types' => array_filter(
                InventoryTransactionType::cases(),
                static fn (InventoryTransactionType $type): bool => $type !== InventoryTransactionType::REVERSAL,
            ),
        ]);
    }

    public function store(StoreInventoryTransactionRequest $request): RedirectResponse
    {
        try {
            $transaction = $this->transactions->create(
                $request->validated(),
                $request->user(),
                Gate::forUser($request->user())->allows('override-negative-stock'),
            );
        } catch (InvalidInventoryTransaction $exception) {
            return back()->withInput()->withErrors(['transaction' => $exception->getMessage()]);
        }

        return redirect()
            ->route('inventory-transactions.show', $transaction)
            ->with('success', 'Inventory transaction recorded successfully.');
    }

    public function show(InventoryTransaction $inventoryTransaction): View
    {
        return view('inventory-transactions.show', [
            'transaction' => $this->transactions->findById($inventoryTransaction->getKey()),
        ]);
    }

    public function reverse(
        ReverseInventoryTransactionRequest $request,
        InventoryTransaction $inventoryTransaction,
    ): RedirectResponse {
        try {
            $reversal = $this->transactions->reverse(
                $inventoryTransaction,
                $request->user(),
                Gate::forUser($request->user())->allows('override-negative-stock'),
                $request->string('notes')->toString(),
            );
        } catch (InvalidInventoryTransaction $exception) {
            return back()->withErrors(['transaction' => $exception->getMessage()]);
        }

        return redirect()
            ->route('inventory-transactions.show', $reversal)
            ->with('success', 'The transaction was reversed successfully.');
    }
}
