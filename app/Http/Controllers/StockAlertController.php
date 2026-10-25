<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\StockAlert;
use App\Models\User;
use App\Services\StockAlertService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StockAlertController extends Controller
{
    public function __construct(private readonly StockAlertService $stockAlertService)
    {
    }

    public function index(): View
    {
        $alerts = $this->stockAlertService->paginate();

        return view('alerts.index', compact('alerts'));
    }

    public function acknowledge(StockAlert $stockAlert): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();
        
        $this->stockAlertService->acknowledge($stockAlert, $user);

        return back()->with('success', 'Peringatan stok berhasil ditandai selesai.');
    }

    public function checkNow(): RedirectResponse
    {
        $count = $this->stockAlertService->checkAllProducts();

        return back()->with('success', "Pemeriksaan stok selesai. Ditemukan {$count} peringatan baru.");
    }
}
