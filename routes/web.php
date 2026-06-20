<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Account\AccountController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\DeliveryController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\OurMenuController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\PartyHallController;

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
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store')->middleware('throttle:10,1');
});

Route::post('/stripe/webhook', [App\Http\Controllers\StripeWebhookController::class, 'handle'])->name('stripe.webhook')->middleware('throttle:60,1');
Route::post('/promo/check', [App\Http\Controllers\PromoController::class, 'check'])->name('promo.check')->middleware('throttle:20,1');
Route::get('/delivery-fee', [CheckoutController::class, 'deliveryFee'])->name('delivery.fee')->middleware('throttle:30,1');
Route::get('/orders/{order}/confirmation', [OrderController::class, 'confirmation'])->name('orders.confirmation');
Route::get('/orders/{order}/tracking', [OrderController::class, 'tracking'])->name('orders.tracking');
Route::get('/party-hall',  [PartyHallController::class, 'index'])->name('party-hall');
Route::post('/party-hall', [PartyHallController::class, 'submit'])->name('party-hall.submit')->middleware('throttle:5,1');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'sendContact'])->name('contact.send');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::get('/login', fn () => view('auth.login', ['title' => 'Login']))->name('login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login.post')->middleware('throttle:5,1');
Route::get('/register', fn () => view('auth.register', ['title' => 'Create Account']))->name('register');
Route::post('/register', [App\Http\Controllers\AuthController::class, 'register'])->name('register.post')->middleware('throttle:3,1');
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
Route::get('/auth/{provider}/redirect',  [SocialAuthController::class, 'redirect'])->name('social.redirect')->middleware('guest');
Route::get('/auth/{provider}/callback',  [SocialAuthController::class, 'callback'])->name('social.callback')->middleware('guest');

// Password Reset
Route::get('/forgot-password',        [PasswordResetController::class, 'showForgotForm'])->name('password.request')->middleware('guest');
Route::post('/forgot-password',       [PasswordResetController::class, 'sendLink'])->name('password.email')->middleware('guest');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset')->middleware('guest');
Route::post('/reset-password',        [PasswordResetController::class, 'reset'])->name('password.update')->middleware('guest');

/*
|--------------------------------------------------------------------------
| Account Routes (requires login)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->prefix('account')->group(function () {
    Route::get('/',                              [AccountController::class, 'index'])->name('account');
    Route::post('/addresses',                    [App\Http\Controllers\Account\AddressController::class, 'store'])->name('account.addresses.store');
    Route::delete('/addresses/{address}',        [App\Http\Controllers\Account\AddressController::class, 'destroy'])->name('account.addresses.destroy');
    Route::patch('/addresses/{address}/default', [App\Http\Controllers\Account\AddressController::class, 'setDefault'])->name('account.addresses.default');
    Route::post('/password',                     [App\Http\Controllers\Account\PasswordController::class, 'update'])->name('account.password');
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
    Route::get('/orders/in-store',  [AdminOrderController::class, 'inStore'])->name('orders.in-store');
    Route::post('/orders/in-store', [AdminOrderController::class, 'storeInStore'])->name('orders.in-store.store');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.detail');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');

    // Menu
    Route::get('/menu',             [MenuItemController::class, 'index'])->name('menu.index');
    Route::get('/menu/create',      [MenuItemController::class, 'create'])->name('menu.create');
    Route::post('/menu',            [MenuItemController::class, 'store'])->name('menu.store');
    Route::get('/menu/{item}/edit', [MenuItemController::class, 'edit'])->name('menu.edit');
    Route::put('/menu/{item}',      [MenuItemController::class, 'update'])->name('menu.update');
    Route::delete('/menu/{item}',      [MenuItemController::class, 'destroy'])->name('menu.destroy');
    Route::patch('/menu/{item}/toggle', [MenuItemController::class, 'toggleAvailability'])->name('menu.toggle');

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
    Route::get('/kitchen/poll', [App\Http\Controllers\Admin\KitchenController::class, 'poll'])->name('kitchen.poll');

    // Party Hall Inquiries
    Route::get('/party-hall', [App\Http\Controllers\Admin\PartyHallController::class, 'index'])->name('party-hall.index');
    Route::patch('/party-hall/{inquiry}', [App\Http\Controllers\Admin\PartyHallController::class, 'update'])->name('party-hall.update');

    // Allergy Management
    Route::get('/allergy',                       [App\Http\Controllers\Admin\AllergyController::class, 'index'])->name('allergy.index');
    Route::post('/allergy/settings',             [App\Http\Controllers\Admin\AllergyController::class, 'saveSettings'])->name('allergy.settings');
    Route::post('/allergy/map',                  [App\Http\Controllers\Admin\AllergyController::class, 'saveMap'])->name('allergy.map');
    Route::post('/allergens',                    [App\Http\Controllers\Admin\AllergyController::class, 'storeAllergen'])->name('allergens.store');
    Route::patch('/allergens/{allergen}/toggle', [App\Http\Controllers\Admin\AllergyController::class, 'toggle'])->name('allergens.toggle');
    Route::delete('/allergens/{allergen}',       [App\Http\Controllers\Admin\AllergyController::class, 'destroyAllergen'])->name('allergens.destroy');
});
