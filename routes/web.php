<?php

use App\Http\Controllers\DashboardMarketingController;
use App\Http\Controllers\MasterVoucherController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::middleware(['role:marketing'])->group(function () {
        Route::resource('master-vouchers', MasterVoucherController::class);
        Route::get('marketing', [DashboardMarketingController::class, 'index'])->name('dashboard.marketing');
    });

    Route::middleware(['role:cashier'])->group(function () {
        Route::resource('transactions', TransactionController::class);
        Route::get('transactions/{transaction}/download', [TransactionController::class, 'downloadInvoice'])->name('transactions.downloadInvoice');
    });
});

require __DIR__.'/settings.php';
