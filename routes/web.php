<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Middleware\RestrictLan;
use App\Http\Middleware\BasicAdminAuth;

// Public facing (But LAN restricted for safety, although prompt says LAN restricted for whole system)
// "Restrict access so only company network users can access the system"
Route::middleware([RestrictLan::class])->group(function () {
    
    Route::get('/', [ProductController::class, 'index'])->name('shop.index');
    Route::get('/orders', function () { return redirect()->route('shop.index'); });
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::post('/custom-request', [OrderController::class, 'submitCustomRequest'])->name('custom-request.store');
    
    // Chatbot Route
    Route::post('/api/chat', [\App\Http\Controllers\ChatbotController::class, 'invoke'])->name('api.chat');

    // Admin Auth
    Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login.form');
    Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
    Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    // Admin Protected
    Route::middleware([BasicAdminAuth::class])->prefix('admin')->group(function () {
        Route::get('/', function () {
            return redirect()->route('admin.orders');
        })->name('admin.dashboard');

        Route::get('/products', [ProductController::class, 'adminIndex'])->name('admin.products');
        Route::get('/products/create', [ProductController::class, 'create'])->name('admin.products.create');
        Route::post('/products/import', [ProductController::class, 'import'])->name('admin.products.import');
        Route::post('/products', [ProductController::class, 'store'])->name('admin.products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('admin.products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('admin.products.destroy');
        Route::delete('/products', [ProductController::class, 'destroyAll'])->name('admin.products.destroy-all');

        Route::get('/orders', [OrderController::class, 'adminIndex'])->name('admin.orders');
        Route::get('/orders/check-new', [OrderController::class, 'checkNew'])->name('admin.orders.check-new');
        Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.status');
        Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('admin.orders.destroy');
    });

});
