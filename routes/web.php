<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Client\CatalogueController;
use App\Http\Controllers\Client\OrderController as ClientOrderController;
use Illuminate\Support\Facades\Route;

// ─── Page d'accueil ───────────────────────────────────────
Route::get('/', [App\Http\Controllers\Client\CatalogueController::class, 'index'])->name('home');
// ─── Auth ─────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// ─── Admin (gestionnaire) ─────────────────────────────────
// ─── Admin (gestionnaire) ─────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:gestionnaire'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Produits
    Route::resource('products', AdminProductController::class)->except(['show']);
    Route::patch('products/{product}/archive', [AdminProductController::class, 'archive'])->name('products.archive');

    // Commandes
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::patch('/orders/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('orders.cancel');
});

// ─── Routes publiques ─────────────────────────────────────
Route::get('/catalogue', [App\Http\Controllers\Client\CatalogueController::class, 'index'])->name('client.catalogue');
Route::get('/catalogue/{product:slug}', [App\Http\Controllers\Client\CatalogueController::class, 'show'])->name('client.product.show');

// ─── Client ───────────────────────────────────────────────
Route::prefix('client')->name('client.')->middleware(['auth', 'role:client'])->group(function () {
    // Catalogue
    Route::get('/catalogue', [CatalogueController::class, 'index'])->name('catalogue');
    Route::get('/catalogue/{product:slug}', [CatalogueController::class, 'show'])->name('product.show');

    // Commandes
    Route::get('/orders', [ClientOrderController::class, 'index'])->name('orders.index');
    Route::post('/orders', [ClientOrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [ClientOrderController::class, 'show'])->name('orders.show');
});