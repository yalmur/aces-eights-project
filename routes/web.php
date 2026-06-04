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
use App\Http\Controllers\OurMenuController;

/*
|--------------------------------------------------------------------------
| Customer Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/our-menu', [OurMenuController::class, 'index'])->name('our-menu');
Route::get('/menu', [MenuController::class, 'index'])->name('menu');
Route::get('/menu/{slug}', [MenuController::class, 'show'])->name('menu.show');
Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
});

Route::post('/stripe/webhook', [App\Http\Controllers\StripeWebhookController::class, 'handle'])->name('stripe.webhook');
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
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login.post');
Route::get('/register', fn () => view('auth.register', ['title' => 'Create Account']))->name('register');
Route::post('/register', [App\Http\Controllers\AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

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
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');

    // Menu
    Route::get('/menu',             [MenuItemController::class, 'index'])->name('menu.index');
    Route::get('/menu/create',      [MenuItemController::class, 'create'])->name('menu.create');
    Route::post('/menu',            [MenuItemController::class, 'store'])->name('menu.store');
    Route::get('/menu/{item}/edit', [MenuItemController::class, 'edit'])->name('menu.edit');
    Route::put('/menu/{item}',      [MenuItemController::class, 'update'])->name('menu.update');
    Route::delete('/menu/{item}',   [MenuItemController::class, 'destroy'])->name('menu.destroy');

    // Delivery Zones
    Route::get('/delivery',           [DeliveryController::class, 'index'])->name('delivery.index');
    Route::post('/delivery',          [DeliveryController::class, 'store'])->name('delivery.store');
    Route::put('/delivery/{zone}',    [DeliveryController::class, 'update'])->name('delivery.update');
    Route::delete('/delivery/{zone}', [DeliveryController::class, 'destroy'])->name('delivery.destroy');

    // Promotions
    Route::get('/promotions',              [PromotionController::class, 'index'])->name('promotions.index');
    Route::get('/promotions/create',       [PromotionController::class, 'create'])->name('promotions.create');
    Route::post('/promotions',             [PromotionController::class, 'store'])->name('promotions.store');
    Route::get('/promotions/{promo}/edit', [PromotionController::class, 'edit'])->name('promotions.edit');
    Route::put('/promotions/{promo}',      [PromotionController::class, 'update'])->name('promotions.update');
    Route::delete('/promotions/{promo}',   [PromotionController::class, 'destroy'])->name('promotions.destroy');

    // Settings
    Route::get('/settings',  [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Kitchen Command
    Route::get('/kitchen', [App\Http\Controllers\Admin\KitchenController::class, 'index'])->name('kitchen.index');

    // Allergy Management
    Route::get('/allergy',                       [App\Http\Controllers\Admin\AllergyController::class, 'index'])->name('allergy.index');
    Route::post('/allergy/settings',             [App\Http\Controllers\Admin\AllergyController::class, 'saveSettings'])->name('allergy.settings');
    Route::post('/allergy/map',                  [App\Http\Controllers\Admin\AllergyController::class, 'saveMap'])->name('allergy.map');
    Route::patch('/allergens/{allergen}/toggle', [App\Http\Controllers\Admin\AllergyController::class, 'toggle'])->name('allergens.toggle');
    Route::delete('/allergens/{allergen}',       [App\Http\Controllers\Admin\AllergyController::class, 'destroyAllergen'])->name('allergens.destroy');
});
