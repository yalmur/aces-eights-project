<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Account\AccountController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\DeliveryController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\SettingsController;

/*
|--------------------------------------------------------------------------
| Customer Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/menu', [MenuController::class, 'index'])->name('menu');
Route::get('/menu/{slug}', [MenuController::class, 'show'])->name('menu.show');
Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::get('/orders/{order}/confirmation', [OrderController::class, 'confirmation'])->name('orders.confirmation');
Route::get('/orders/{order}/tracking', [OrderController::class, 'tracking'])->name('orders.tracking');
Route::get('/booking', [BookingController::class, 'index'])->name('booking');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'sendContact'])->name('contact.send');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::get('/login', fn () => view('auth.login', ['title' => 'Login']))->name('login');
Route::get('/register', fn () => view('auth.register', ['title' => 'Create Account']))->name('register');
Route::post('/logout', fn () => redirect('/'))->name('logout');

/*
|--------------------------------------------------------------------------
| Account Routes (requires login)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->prefix('account')->group(function () {
    Route::get('/', [AccountController::class, 'index'])->name('account');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (requires login + admin role)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Orders
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/in-store', [AdminOrderController::class, 'inStore'])->name('orders.in-store');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.detail');

    // Menu
    Route::get('/menu', [MenuItemController::class, 'index'])->name('menu.index');
    Route::get('/menu/create', [MenuItemController::class, 'create'])->name('menu.create');
    Route::get('/menu/{item}/edit', [MenuItemController::class, 'edit'])->name('menu.edit');

    // Delivery
    Route::get('/delivery', [DeliveryController::class, 'index'])->name('delivery.index');

    // Promotions
    Route::get('/promotions', [PromotionController::class, 'index'])->name('promotions.index');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
});
