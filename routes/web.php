<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Routes - Adit Kejut POS
|--------------------------------------------------------------------------
*/

// === Guest (belum login) ===
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
});

// === Authenticated (sudah login) ===
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard (otomatis sesuai role)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // POS — semua role bisa akses
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos', [PosController::class, 'store'])->name('pos.store');
    Route::get('/pos/receipt/{transaction}', [PosController::class, 'receipt'])->name('pos.receipt');
    Route::get('/pos/history', [PosController::class, 'history'])->name('pos.history');
    Route::get('/pos/search-products', [PosController::class, 'searchProducts'])->name('pos.search');

    // Produk — semua role bisa lihat
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');

    // === Role: Admin & Owner ===
    Route::middleware('role:admin,owner')->group(function () {
        // Kategori
        Route::resource('categories', CategoryController::class);

        // Supplier
        Route::resource('suppliers', \App\Http\Controllers\SupplierController::class);

        // Produk (CRUD selain index)
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        // Pembelian / Restock
        Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
        Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
        Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');

        // Retur Penjualan
        Route::get('/returns', [ReturnController::class, 'index'])->name('returns.index');
        Route::get('/returns/create', [ReturnController::class, 'create'])->name('returns.create');
        Route::post('/returns', [ReturnController::class, 'store'])->name('returns.store');

        // Laporan Penjualan
        Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');

        // Kartu Stok
        Route::get('/reports/stock-card', [ReportController::class, 'stockCard'])->name('reports.stock-card');
    });

    // === Role: Owner Only ===
    Route::middleware('role:owner')->group(function () {
        // Kelola User
        Route::resource('users', UserController::class);

        // Utang Usaha
        Route::get('/debts', [DebtController::class, 'index'])->name('debts.index');
        Route::get('/debts/{purchase}', [DebtController::class, 'show'])->name('debts.show');
        Route::post('/debts/pay-supplier', [DebtController::class, 'paySupplier'])->name('debts.paySupplier');
        Route::post('/debts/{purchase}/pay', [DebtController::class, 'pay'])->name('debts.pay');

        // Keuangan Laporan
        Route::get('/reports/profit-loss', [ReportController::class, 'profitLoss'])->name('reports.profit-loss');
        Route::get('/reports/balance-sheet', [ReportController::class, 'balanceSheet'])->name('reports.balance-sheet');

        // Audit Log
        Route::get('/reports/audit-log', [ReportController::class, 'auditLog'])->name('reports.audit-log');
    });
});
