# Aces & Eights Pizza — Plan 3: Admin Frontend

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build every admin page as a full static Blade view matching the approved stitch designs, with working auth so the client can log in and review all admin pages before backend work begins.

**Architecture:** Same stitch-extraction approach as Plan 2. Each task reads the stitch `code.html`, extracts `<main>` content, adapts to `@extends('layouts.admin')`, replaces `<script>` with Alpine.js, replaces placeholder images with `placehold.co`, wires `href="#"` to real `route()` calls. Auth infrastructure (migrations + seed + login POST) is included as Task 1 — this is the minimum needed to view protected admin pages. All admin data is hardcoded static HTML (no DB queries).

**Tech Stack:** Laravel 11 Blade, Alpine.js 3, Tailwind CSS 3, Material Symbols Outlined, PHP 8.2 at `C:\xampp\php\php.exe`

**Real client data (replace stitch placeholders):**
- Address: `156 & 158 Fortess Road, Tufnell Park, London, NW5 2HP`
- Phone: `+44 020 7485 4033`
- Email: `nw5pizza@gmail.com`
- Hours: `Sun–Thu: 16:00–22:45 | Fri–Sat: 16:00–23:15`

**Stitch source directory:** `C:\AcesAndEightsPizza\Project\stitch_designs\stitch_iterative_design_execution\`

---

## File Map

| Action | File |
|--------|------|
| Modify | `resources/js/app.js` — add `Alpine.store('adminNav')` |
| Create | `database/migrations/YYYY_add_role_to_users_table.php` |
| Create | `database/seeders/AdminUserSeeder.php` |
| Modify | `database/seeders/DatabaseSeeder.php` |
| Create | `app/Http/Controllers/AuthController.php` |
| Create | `app/Http/Controllers/Admin/KitchenController.php` |
| Create | `app/Http/Controllers/Admin/AllergyController.php` |
| Modify | `routes/web.php` — login POST, logout POST, kitchen + allergy routes |
| Modify | `resources/views/layouts/admin.blade.php` — full rebuild |
| Modify | `resources/views/components/admin/sidebar.blade.php` — Alpine drawer |
| Modify | `resources/css/app.css` — add industrial CSS classes |
| Modify | `tailwind.config.js` — add `industrial-gray` color token |
| Modify | `resources/views/admin/dashboard.blade.php` |
| Modify | `resources/views/admin/orders/index.blade.php` |
| Modify | `resources/views/admin/orders/detail.blade.php` |
| Modify | `resources/views/admin/orders/in-store.blade.php` |
| Create | `resources/views/admin/kitchen/index.blade.php` |
| Modify | `resources/views/admin/menu/index.blade.php` |
| Modify | `resources/views/admin/menu/edit.blade.php` |
| Create | `resources/views/admin/allergy/index.blade.php` |
| Modify | `resources/views/admin/delivery/index.blade.php` |
| Modify | `resources/views/admin/settings/index.blade.php` |
| Modify | `tests/Feature/RouteSmokeTest.php` — add admin preview tests |

---

## Task 1: Auth Infrastructure

**Files:**
- Create: `database/migrations/[timestamp]_add_role_to_users_table.php`
- Create: `database/seeders/AdminUserSeeder.php`
- Modify: `database/seeders/DatabaseSeeder.php`
- Create: `app/Http/Controllers/AuthController.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Run default migrations**

```bash
cd C:\AcesAndEightsPizza\webapp
& "C:\xampp\php\php.exe" artisan migrate
```

Expected output includes: `Migrating: ...create_users_table` and ends with `INFO  All migrations ran successfully`.

If you get a DB connection error, check `.env` has `DB_DATABASE=aces_eights`, `DB_USERNAME=root`, `DB_PASSWORD=` and that MySQL is running via XAMPP.

- [ ] **Step 2: Create migration to add role column**

```bash
& "C:\xampp\php\php.exe" artisan make:migration add_role_to_users_table --table=users
```

Open the generated file in `database/migrations/` (newest file ending in `_add_role_to_users_table.php`) and replace its contents with:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('customer')->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
```

Run it:
```bash
& "C:\xampp\php\php.exe" artisan migrate
```

Expected: `Migrating: [timestamp]_add_role_to_users_table` → `Migrated`.

- [ ] **Step 3: Create AdminUserSeeder**

Create `database/seeders/AdminUserSeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->upsert([
            [
                'name'              => 'Admin',
                'email'             => 'admin@acesandeights.com',
                'role'              => 'admin',
                'password'          => Hash::make('admin123'),
                'email_verified_at' => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
        ], ['email'], ['name', 'role', 'password']);
    }
}
```

- [ ] **Step 4: Register seeder and run it**

Open `database/seeders/DatabaseSeeder.php` and add the call:

```php
public function run(): void
{
    $this->call(AdminUserSeeder::class);
}
```

Run:
```bash
& "C:\xampp\php\php.exe" artisan db:seed
```

Expected: `INFO  Seeding database.` with no errors.

- [ ] **Step 5: Create AuthController**

Create `app/Http/Controllers/AuthController.php`:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(
                Auth::user()->role === 'admin'
                    ? route('admin.dashboard')
                    : route('home')
            );
        }

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
```

- [ ] **Step 6: Update web.php routes**

Find and replace the existing auth routes block:

```php
Route::get('/login', fn () => view('auth.login', ['title' => 'Login']))->name('login');
Route::get('/register', fn () => view('auth.register', ['title' => 'Create Account']))->name('register');
Route::post('/logout', fn () => redirect('/'))->name('logout');
```

Replace with:

```php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\KitchenController;
use App\Http\Controllers\Admin\AllergyController;

Route::get('/login', fn () => view('auth.login', ['title' => 'Login']))->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', fn () => view('auth.register', ['title' => 'Create Account']))->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
```

Also add kitchen + allergy inside the admin middleware group (after the settings route):

```php
    // Kitchen Command
    Route::get('/kitchen', [KitchenController::class, 'index'])->name('kitchen.index');

    // Allergy Management
    Route::get('/allergy', [AllergyController::class, 'index'])->name('allergy.index');
```

- [ ] **Step 7: Create KitchenController and AllergyController**

Create `app/Http/Controllers/Admin/KitchenController.php`:

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class KitchenController extends Controller
{
    public function index(): View
    {
        return view('admin.kitchen.index', ['title' => 'Kitchen Command']);
    }
}
```

Create `app/Http/Controllers/Admin/AllergyController.php`:

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AllergyController extends Controller
{
    public function index(): View
    {
        return view('admin.allergy.index', ['title' => 'Allergy Management']);
    }
}
```

- [ ] **Step 8: Wire the login form to POST**

Open `resources/views/auth/login.blade.php`. Find the form element (it currently has no POST action) and update it to:

```blade
<form method="POST" action="{{ route('login.post') }}">
    @csrf
```

Also add error display after the email field:

```blade
@error('email')
    <p class="mt-1 font-mono text-xs text-brand-error">{{ $message }}</p>
@enderror
```

- [ ] **Step 9: Verify login works**

```bash
& "C:\xampp\php\php.exe" artisan serve --port=8000
```

Open browser, go to `http://localhost:8000/login`. Enter:
- Email: `admin@acesandeights.com`
- Password: `admin123`

Expected: redirects to `http://localhost:8000/admin` (admin dashboard stub page).

- [ ] **Step 10: Commit**

```bash
git add database/ app/Http/Controllers/AuthController.php app/Http/Controllers/Admin/KitchenController.php app/Http/Controllers/Admin/AllergyController.php routes/web.php resources/views/auth/login.blade.php
git commit -m "feat: add auth infrastructure, admin user seed, kitchen+allergy controllers"
```

---

## Task 2: Admin Layout + Tailwind Tokens + CSS

**Files:**
- Modify: `resources/views/layouts/admin.blade.php`
- Modify: `resources/views/components/admin/sidebar.blade.php`
- Modify: `resources/css/app.css`
- Modify: `tailwind.config.js`
- Modify: `resources/js/app.js`

- [ ] **Step 1: Add `industrial-gray` to tailwind.config.js**

In `tailwind.config.js`, inside `theme.extend.colors`, add after the existing colors:

```js
'industrial-gray': '#2B2B2B',
'carbon-black': '#121212',
'heritage-gold': '#D4AF37',
'oxblood-red': '#690008',
```

(Note: `oxblood-red` and `primary.DEFAULT` are the same value — adding the alias so stitch classes work without modification.)

- [ ] **Step 2: Add admin CSS classes to app.css**

Append to `resources/css/app.css` inside the `@layer components` block:

```css
  /* Admin industrial utilities */
  .industrial-border {
    @apply border border-[#2B2B2B];
  }
  .industrial-divider {
    @apply border-b-2 border-double border-[#2B2B2B];
  }
  .double-divider {
    border-bottom: 3px double #1b1c1c;
  }
  .gold-button {
    background-color: #690008;
    color: white;
    border-bottom: 2px solid #D4AF37;
    transition: all 0.2s ease;
  }
  .gold-button:active { transform: scale(0.96); opacity: 0.9; }
  .gold-metallic {
    background: linear-gradient(135deg, #D4AF37 0%, #C5A028 50%, #B8860B 100%);
  }
  .industrial-border-b {
    border: none;
    border-bottom: 2px solid #2B2B2B;
    background: transparent;
  }
  .industrial-border-t {
    border: none;
    border-top: 2px solid #2B2B2B;
  }
  .admin-nav-item {
    @apply flex items-center gap-4 py-3 px-4 text-on-surface hover:bg-[#2B2B2B]/10 rounded transition-colors cursor-pointer;
    font-family: 'JetBrains Mono', monospace;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
  }
  .admin-nav-item.active {
    @apply bg-primary text-white;
  }
  /* Toggle switch */
  .toggle-checkbox:checked + .toggle-label { @apply bg-primary; }
  .toggle-checkbox:checked + .toggle-label .toggle-dot { transform: translateX(100%); }
```

- [ ] **Step 3: Add Alpine.store('adminNav') to app.js**

Open `resources/js/app.js`. Before `Alpine.start()`, add:

```js
Alpine.store('adminNav', {
  open: false,
  toggle() { this.open = !this.open },
  close() { this.open = false },
})
```

- [ ] **Step 4: Rebuild admin layout**

Replace the full contents of `resources/views/layouts/admin.blade.php`:

```blade
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title ?? 'Admin' }} — Aces & Eights Pizza</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @livewireStyles
  <style>[x-cloak]{display:none!important}</style>
</head>
<body class="bg-surface text-on-surface font-sans overflow-x-hidden">

  {{-- Backdrop --}}
  <div x-data
       x-show="$store.adminNav.open"
       x-cloak
       @click="$store.adminNav.close()"
       class="fixed inset-0 bg-black/50 z-[60]"></div>

  {{-- TopAppBar --}}
  <header class="fixed top-0 w-full z-50 border-b bg-surface-container-lowest border-surface-variant">
    <div class="flex items-center justify-between px-4 md:px-16 h-16 w-full max-w-[1280px] mx-auto relative">
      <button @click="$store.adminNav.toggle()"
              class="active:scale-95 transition-transform flex items-center justify-center p-2 -ml-2">
        <span class="material-symbols-outlined text-primary">menu</span>
      </button>
      <div class="absolute left-1/2 -translate-x-1/2 flex items-center h-full py-2">
        <img src="{{ asset('images/logo.jpg') }}" alt="Aces & Eights Pizza" class="h-10 w-auto rounded">
      </div>
      <div class="flex items-center gap-3">
        <span class="font-mono text-[10px] text-on-surface-variant hidden sm:block uppercase tracking-widest">
          {{ now()->format('D d M') }}
        </span>
        <a href="{{ route('home') }}" target="_blank"
           class="font-mono text-[10px] text-primary hover:underline uppercase tracking-widest">
          View Site ↗
        </a>
      </div>
    </div>
  </header>

  {{-- Navigation Drawer --}}
  <x-admin.sidebar />

  {{-- Main content area --}}
  <main class="pt-20 pb-12 px-4 md:px-16 max-w-[1280px] mx-auto lg:ml-80">
    @yield('content')
  </main>

  @livewireScripts
</body>
</html>
```

- [ ] **Step 5: Rebuild admin sidebar**

Replace the full contents of `resources/views/components/admin/sidebar.blade.php`:

```blade
<aside x-data
       @keydown.escape.window="$store.adminNav.close()"
       class="fixed top-0 left-0 h-full w-80 bg-surface border-r border-[#2B2B2B] z-[70]
              lg:fixed lg:top-16 lg:h-[calc(100vh-64px)] lg:z-40"
       :class="$store.adminNav.open ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
       style="transition: transform 0.3s cubic-bezier(0.4,0,0.2,1)">

  <div class="flex flex-col h-full py-6 px-4">

    {{-- Mobile close button --}}
    <div class="flex justify-between items-center mb-6 px-2 lg:hidden">
      <h2 class="font-serif text-base font-bold text-primary">Aces & Eights Admin</h2>
      <button @click="$store.adminNav.close()" class="p-2 hover:bg-surface-container rounded-full">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>

    {{-- Nav --}}
    <nav class="space-y-1 flex-1">
      <a href="{{ route('admin.dashboard') }}"
         class="admin-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <span class="material-symbols-outlined text-[20px]">dashboard</span>
        Dashboard
      </a>
      <p class="font-mono text-[9px] text-[#2B2B2B] px-4 pt-4 pb-1 uppercase tracking-widest">Orders</p>
      <a href="{{ route('admin.orders.index') }}"
         class="admin-nav-item {{ request()->routeIs('admin.orders.index') ? 'active' : '' }}">
        <span class="material-symbols-outlined text-[20px]">list_alt</span>
        Online Orders
      </a>
      <a href="{{ route('admin.orders.in-store') }}"
         class="admin-nav-item {{ request()->routeIs('admin.orders.in-store') ? 'active' : '' }}">
        <span class="material-symbols-outlined text-[20px]">storefront</span>
        In-Store Orders
      </a>
      <a href="{{ route('admin.kitchen.index') }}"
         class="admin-nav-item {{ request()->routeIs('admin.kitchen.index') ? 'active' : '' }}">
        <span class="material-symbols-outlined text-[20px]">outdoor_grill</span>
        Kitchen Command
      </a>
      <p class="font-mono text-[9px] text-[#2B2B2B] px-4 pt-4 pb-1 uppercase tracking-widest">Management</p>
      <a href="{{ route('admin.menu.index') }}"
         class="admin-nav-item {{ request()->routeIs('admin.menu.*') ? 'active' : '' }}">
        <span class="material-symbols-outlined text-[20px]">restaurant_menu</span>
        Menu
      </a>
      <a href="{{ route('admin.delivery.index') }}"
         class="admin-nav-item {{ request()->routeIs('admin.delivery.*') ? 'active' : '' }}">
        <span class="material-symbols-outlined text-[20px]">local_shipping</span>
        Delivery
      </a>
      <a href="{{ route('admin.allergy.index') }}"
         class="admin-nav-item {{ request()->routeIs('admin.allergy.*') ? 'active' : '' }}">
        <span class="material-symbols-outlined text-[20px]">warning</span>
        Allergy
      </a>
      <a href="{{ route('admin.promotions.index') }}"
         class="admin-nav-item {{ request()->routeIs('admin.promotions.*') ? 'active' : '' }}">
        <span class="material-symbols-outlined text-[20px]">campaign</span>
        Promotions
      </a>
      <p class="font-mono text-[9px] text-[#2B2B2B] px-4 pt-4 pb-1 uppercase tracking-widest">System</p>
      <a href="{{ route('admin.settings.index') }}"
         class="admin-nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
        <span class="material-symbols-outlined text-[20px]">settings</span>
        Settings
      </a>
    </nav>

    {{-- User footer --}}
    <div class="border-t border-[#2B2B2B]/20 px-2 pt-4 mt-4">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-full bg-primary-container flex items-center justify-center">
          <span class="font-mono text-xs font-bold text-white">
            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
          </span>
        </div>
        <div class="flex-1 min-w-0">
          <p class="font-sans text-xs font-semibold text-on-surface truncate">
            {{ auth()->user()->name ?? 'Admin' }}
          </p>
          <p class="font-mono text-[9px] uppercase text-on-surface-variant tracking-widest">Store Manager</p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" title="Logout" class="text-on-surface-variant hover:text-primary transition-colors">
            <span class="material-symbols-outlined text-[18px]">logout</span>
          </button>
        </form>
      </div>
    </div>

  </div>
</aside>
```

- [ ] **Step 6: Build assets and verify**

```bash
npm run build
```

Expected: `✓ built in Xms` — no errors.

Start server and log in:
```bash
& "C:\xampp\php\php.exe" artisan serve --port=8000
```

Go to `http://localhost:8000/login`, log in as `admin@acesandeights.com` / `admin123`. Expected: admin dashboard stub page with new sidebar visible on left, hamburger menu works on mobile.

- [ ] **Step 7: Commit**

```bash
git add resources/views/layouts/admin.blade.php resources/views/components/admin/sidebar.blade.php resources/css/app.css tailwind.config.js resources/js/app.js
git commit -m "feat: rebuild admin layout with Alpine.js drawer, industrial CSS tokens"
```

---

## Task 3: Admin Dashboard

**Stitch source:** `aces_eights_admin_dashboard_final_engineer_polish/code.html` (lines 265–463)

**Files:**
- Modify: `resources/views/admin/dashboard.blade.php`

- [ ] **Step 1: Extract and adapt the stitch main content**

Read `C:\AcesAndEightsPizza\Project\stitch_designs\stitch_iterative_design_execution\aces_eights_admin_dashboard_final_engineer_polish\code.html`.

Extract everything between `<main` and `</main>` (lines 265–463). Then write `resources/views/admin/dashboard.blade.php`:

```blade
@extends('layouts.admin')
@section('content')

{{-- Paste extracted <main> content here, with these changes: --}}
{{-- 1. Remove the outer <main> tags (layout provides them) --}}
{{-- 2. Replace all <script> blocks with nothing (Alpine handles interactions) --}}
{{-- 3. Replace all src="https://lh3.googleusercontent.com/..." with src="https://placehold.co/400x300/e4e2e1/1b1c1c?text=Photo" --}}
{{-- 4. Replace href="#" navigation links with route() calls per sidebar nav --}}
{{-- 5. Keep ALL Tailwind utility classes exactly as-is --}}

@endsection
```

**Actual content to write** (extracted and adapted from stitch lines 265–463):

```blade
@extends('layouts.admin')
@section('content')

{{-- Header Section --}}
<section class="mb-12">
  <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-4">
    <div>
      <h1 class="font-serif text-4xl md:text-5xl font-black text-on-surface uppercase leading-none">Command Center</h1>
      <p class="font-mono text-xs font-bold text-primary mt-2 uppercase tracking-widest">SHIFT: {{ now()->format('l, g:i A') }}</p>
    </div>
    <div class="flex gap-2">
      <a href="{{ route('admin.menu.create') }}"
         class="gold-button px-6 py-3 font-mono text-xs font-bold uppercase flex items-center justify-center gap-2 w-full md:w-auto">
        <span class="material-symbols-outlined text-base">add</span> Add Manual Order
      </a>
    </div>
  </div>
  <div class="industrial-divider"></div>
</section>

{{-- KPI Bento Grid --}}
<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
  <div class="industrial-border p-6 bg-surface-container-low flex flex-col justify-between h-44">
    <div class="flex justify-between items-start gap-2">
      <span class="font-mono text-xs font-bold text-[#2B2B2B] uppercase">Today's Orders</span>
      <span class="material-symbols-outlined text-primary">confirmation_number</span>
    </div>
    <div class="text-5xl font-black font-serif">142</div>
    <div class="font-mono text-xs text-green-700 font-bold">+12% vs Yesterday</div>
  </div>
  <div class="industrial-border p-6 bg-surface-container-low flex flex-col justify-between h-44">
    <div class="flex justify-between items-start gap-2">
      <span class="font-mono text-xs font-bold text-[#2B2B2B] uppercase">Live Revenue</span>
      <span class="material-symbols-outlined text-primary">payments</span>
    </div>
    <div class="font-black font-serif text-4xl">£3,240</div>
    <div class="font-mono text-xs text-[#2B2B2B]">Current Shift Projection</div>
  </div>
  <div class="industrial-border p-6 bg-surface-container-low flex flex-col justify-between h-44">
    <div class="flex justify-between items-start gap-2">
      <span class="font-mono text-xs font-bold text-[#2B2B2B] uppercase">Active Deliveries</span>
      <span class="material-symbols-outlined text-primary" style="font-variation-settings:'FILL' 1">local_shipping</span>
    </div>
    <div class="text-5xl font-black font-serif text-primary">08</div>
    <div class="font-mono text-xs text-[#2B2B2B]">Avg Delivery: 24 mins</div>
  </div>
  <div class="industrial-border p-6 bg-primary text-white flex flex-col justify-between h-44 shadow-xl">
    <div class="flex justify-between items-start gap-2">
      <span class="font-mono text-xs font-bold uppercase">Kitchen Load</span>
      <span class="material-symbols-outlined text-[#D4AF37]">potted_plant</span>
    </div>
    <div class="font-black font-serif text-4xl">HIGH</div>
    <div class="font-mono text-xs uppercase font-bold text-[#D4AF37]">Slowdown Warning Active</div>
  </div>
</section>

{{-- Main workspace --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

  {{-- Live Production Queue --}}
  <section class="lg:col-span-8">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
      <h3 class="font-serif text-xl font-bold uppercase tracking-tight">Live Production Queue</h3>
      <div x-data="{ tab: 'all' }" class="flex gap-2 p-1 bg-surface-container industrial-border w-full sm:w-auto">
        <button @click="tab = 'all'" :class="tab==='all' ? 'bg-primary text-white' : 'hover:bg-surface-variant'"
                class="flex-1 sm:flex-none px-4 py-1 font-mono text-xs font-bold uppercase transition-colors">All</button>
        <button @click="tab = 'online'" :class="tab==='online' ? 'bg-primary text-white' : 'hover:bg-surface-variant'"
                class="flex-1 sm:flex-none px-4 py-1 font-mono text-xs font-bold uppercase transition-colors">Online</button>
        <button @click="tab = 'instore'" :class="tab==='instore' ? 'bg-primary text-white' : 'hover:bg-surface-variant'"
                class="flex-1 sm:flex-none px-4 py-1 font-mono text-xs font-bold uppercase transition-colors">In-Store</button>
      </div>
    </div>
    <div class="space-y-4">

      {{-- Order Card 1 --}}
      <div class="industrial-border overflow-hidden bg-white">
        <div class="bg-surface-container-highest px-6 py-3 flex flex-wrap justify-between items-center border-b border-[#2B2B2B] gap-2">
          <div class="flex items-center gap-4">
            <span class="font-mono text-sm font-bold">ORD-7721</span>
            <span class="bg-primary text-white text-[10px] px-2 py-0.5 font-bold uppercase rounded-full">Online</span>
          </div>
          <span class="font-mono text-xs font-bold text-[#2B2B2B] uppercase">Received 4m ago</span>
        </div>
        <div class="p-6 flex flex-col md:flex-row justify-between gap-6">
          <div class="flex-1">
            <h4 class="font-serif text-lg font-bold mb-2">James Henderson</h4>
            <ul class="font-sans text-sm space-y-1">
              <li class="flex justify-between"><span>2x The Meat Lover (Large)</span><span class="font-bold">£34.00</span></li>
              <li class="flex justify-between"><span>1x Garlic Bread</span><span class="font-bold">£5.50</span></li>
              <li class="flex justify-between border-t border-dotted border-[#2B2B2B] mt-2 pt-1 font-bold"><span>Total</span><span>£39.50</span></li>
            </ul>
          </div>
          <div class="w-full md:w-64 shrink-0">
            <p class="font-mono text-xs uppercase text-[#2B2B2B] mb-3">Status: Accepted</p>
            <div class="w-full h-2 bg-surface-container mb-4 industrial-border overflow-hidden">
              <div class="h-full bg-primary w-1/5"></div>
            </div>
            <button class="w-full gold-button py-2 font-mono text-xs uppercase flex items-center justify-center gap-2">
              Fire To Oven <span class="material-symbols-outlined text-sm">local_fire_department</span>
            </button>
          </div>
        </div>
      </div>

      {{-- Order Card 2 --}}
      <div class="industrial-border overflow-hidden bg-white">
        <div class="bg-surface-container-highest px-6 py-3 flex flex-wrap justify-between items-center border-b border-[#2B2B2B] gap-2">
          <div class="flex items-center gap-4">
            <span class="font-mono text-sm font-bold">ORD-7718</span>
            <span class="bg-[#2B2B2B] text-white text-[10px] px-2 py-0.5 font-bold uppercase rounded-full">In-Store</span>
          </div>
          <span class="font-mono text-xs font-bold text-[#2B2B2B] uppercase">Received 12m ago</span>
        </div>
        <div class="p-6 flex flex-col md:flex-row justify-between gap-6">
          <div class="flex-1">
            <h4 class="font-serif text-lg font-bold mb-2">Table 04 — Sarah P.</h4>
            <ul class="font-sans text-sm space-y-1">
              <li class="flex justify-between"><span>1x Classic Margherita (12")</span><span class="font-bold">£12.50</span></li>
              <li class="flex justify-between"><span>2x Moretti Draft</span><span class="font-bold">£13.00</span></li>
              <li class="flex justify-between border-t border-dotted border-[#2B2B2B] mt-2 pt-1 font-bold"><span>Total</span><span>£25.50</span></li>
            </ul>
          </div>
          <div class="w-full md:w-64 shrink-0">
            <p class="font-mono text-xs uppercase text-[#2B2B2B] mb-3">Status: Cooking</p>
            <div class="w-full h-2 bg-surface-container mb-4 industrial-border overflow-hidden">
              <div class="h-full bg-primary w-3/5"></div>
            </div>
            <button class="w-full gold-button py-2 font-mono text-xs uppercase flex items-center justify-center gap-2">
              Mark As Ready <span class="material-symbols-outlined text-sm">check_circle</span>
            </button>
          </div>
        </div>
      </div>

      {{-- Order Card 3 (Ready) --}}
      <div class="industrial-border overflow-hidden bg-white/50 border-dashed">
        <div class="bg-green-100 px-6 py-3 flex flex-wrap justify-between items-center border-b border-[#2B2B2B] gap-2">
          <div class="flex items-center gap-4 text-green-800">
            <span class="font-mono text-sm font-bold">ORD-7712</span>
            <span class="bg-green-800 text-white text-[10px] px-2 py-0.5 font-bold uppercase rounded-full">Online</span>
          </div>
          <span class="font-mono text-xs font-bold text-green-800 uppercase">Ready for Pickup</span>
        </div>
        <div class="p-6 flex flex-col md:flex-row justify-between gap-6">
          <div class="flex-1">
            <h4 class="font-serif text-lg font-bold mb-2">Marco V. (Driver Assigned)</h4>
            <p class="font-sans text-sm text-[#2B2B2B] italic">"Leave at gate, code 1234."</p>
          </div>
          <div class="w-full md:w-64 shrink-0">
            <p class="font-mono text-xs uppercase text-[#2B2B2B] mb-3">Status: Ready</p>
            <div class="w-full h-2 bg-surface-container mb-4 industrial-border overflow-hidden">
              <div class="h-full bg-green-600 w-full"></div>
            </div>
            <button class="w-full bg-[#2B2B2B] text-white py-2 font-mono text-xs uppercase flex items-center justify-center gap-2">
              Handed to Driver <span class="material-symbols-outlined text-sm">moped</span>
            </button>
          </div>
        </div>
      </div>

    </div>
  </section>

  {{-- Sidebar Contextual Controls --}}
  <aside class="lg:col-span-4 space-y-6">

    {{-- Station Load --}}
    <div class="industrial-border bg-white p-6">
      <h3 class="font-mono text-xs font-bold uppercase border-b border-[#2B2B2B] pb-2 mb-4">Station Load</h3>
      <div class="space-y-4 font-sans text-sm">
        <div class="flex items-center justify-between">
          <span>Stone Oven 1</span>
          <span class="font-mono font-bold text-primary">FULL</span>
        </div>
        <div class="flex items-center justify-between">
          <span>Stone Oven 2</span>
          <span class="font-mono font-bold text-primary">80%</span>
        </div>
        <div class="flex items-center justify-between">
          <span>Prep Station</span>
          <span class="font-mono font-bold text-green-700">MODERATE</span>
        </div>
      </div>
    </div>

    {{-- Delivery Map Placeholder --}}
    <div class="industrial-border overflow-hidden relative group">
      <img src="https://placehold.co/400x200/e4e2e1/1b1c1c?text=Delivery+Map"
           alt="Delivery Map" class="w-full h-48 object-cover grayscale brightness-75 group-hover:brightness-100 transition-all">
      <div class="absolute inset-0 bg-primary/20 flex items-center justify-center">
        <a href="{{ route('admin.delivery.index') }}"
           class="bg-[#2B2B2B] text-white px-4 py-2 font-mono text-xs font-bold uppercase shadow-xl">
          Expand Delivery Map
        </a>
      </div>
    </div>

    {{-- Shift Personnel --}}
    <div class="industrial-border bg-surface-container-low p-6">
      <h3 class="font-mono text-xs font-bold uppercase border-b border-[#2B2B2B] pb-2 mb-4">Shift Personnel</h3>
      <div class="space-y-3">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-full bg-[#2B2B2B] text-white flex items-center justify-center text-[10px] font-bold">AL</div>
          <div class="flex-1">
            <p class="font-mono text-xs font-bold leading-none">Antonio L. <span class="text-primary">•</span></p>
            <p class="font-mono text-[9px] uppercase text-[#2B2B2B]">Head Pizzaiolo</p>
          </div>
        </div>
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-full bg-[#2B2B2B] text-white flex items-center justify-center text-[10px] font-bold">RM</div>
          <div class="flex-1">
            <p class="font-mono text-xs font-bold leading-none">Rosa M.</p>
            <p class="font-mono text-[9px] uppercase text-[#2B2B2B]">Floor Manager</p>
          </div>
        </div>
      </div>
      <button class="w-full mt-6 py-2 industrial-border font-mono text-xs font-bold uppercase hover:bg-[#2B2B2B] hover:text-white transition-colors">
        Manage Rota
      </button>
    </div>

  </aside>
</div>

@endsection
```

- [ ] **Step 2: Build and verify**

```bash
npm run build
```

Log in as admin, visit `http://localhost:8000/admin`. Expected: command center page with 4 KPI cards, live production queue with 3 order cards, station load sidebar, delivery map placeholder.

- [ ] **Step 3: Commit**

```bash
git add resources/views/admin/dashboard.blade.php
git commit -m "feat: admin dashboard page"
```

---

## Task 4: Admin Orders List

**Stitch source:** No dedicated stitch for orders list — build from the pattern established in the dashboard order cards.

**Files:**
- Modify: `resources/views/admin/orders/index.blade.php`

- [ ] **Step 1: Write the orders index view**

Replace `resources/views/admin/orders/index.blade.php`:

```blade
@extends('layouts.admin')
@section('content')

{{-- Header --}}
<section class="mb-10">
  <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-4">
    <div>
      <h1 class="font-serif text-4xl font-black text-on-surface uppercase">Online Orders</h1>
      <p class="font-mono text-xs text-on-surface-variant mt-1 uppercase tracking-widest">{{ now()->format('l, d M Y') }}</p>
    </div>
    <div x-data="{ status: 'all' }" class="flex gap-1 p-1 industrial-border bg-surface-container">
      @foreach(['all' => 'All', 'pending' => 'Pending', 'cooking' => 'Cooking', 'ready' => 'Ready', 'delivered' => 'Delivered'] as $val => $label)
        <button x-on:click="status = '{{ $val }}'"
                :class="status === '{{ $val }}' ? 'bg-primary text-white' : 'hover:bg-surface-variant text-on-surface'"
                class="px-3 py-1 font-mono text-[10px] font-bold uppercase transition-colors">{{ $label }}</button>
      @endforeach
    </div>
  </div>
  <div class="industrial-divider"></div>
</section>

{{-- Stats bar --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
  <div class="industrial-border p-4 bg-surface-container-low text-center">
    <div class="font-serif text-3xl font-black">142</div>
    <div class="font-mono text-[10px] uppercase text-[#2B2B2B]">Today's Orders</div>
  </div>
  <div class="industrial-border p-4 bg-surface-container-low text-center">
    <div class="font-serif text-3xl font-black text-primary">8</div>
    <div class="font-mono text-[10px] uppercase text-[#2B2B2B]">In Progress</div>
  </div>
  <div class="industrial-border p-4 bg-surface-container-low text-center">
    <div class="font-serif text-3xl font-black text-green-700">3</div>
    <div class="font-mono text-[10px] uppercase text-[#2B2B2B]">Ready</div>
  </div>
  <div class="industrial-border p-4 bg-surface-container-low text-center">
    <div class="font-serif text-3xl font-black">£3,240</div>
    <div class="font-mono text-[10px] uppercase text-[#2B2B2B]">Revenue</div>
  </div>
</div>

{{-- Orders table --}}
<div class="industrial-border overflow-hidden bg-white">
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse min-w-[700px]">
      <thead>
        <tr class="bg-surface-container-high border-b border-[#2B2B2B]">
          <th class="px-6 py-4 font-mono text-xs font-bold uppercase">Order</th>
          <th class="px-6 py-4 font-mono text-xs font-bold uppercase">Customer</th>
          <th class="px-6 py-4 font-mono text-xs font-bold uppercase">Items</th>
          <th class="px-6 py-4 font-mono text-xs font-bold uppercase">Total</th>
          <th class="px-6 py-4 font-mono text-xs font-bold uppercase">Status</th>
          <th class="px-6 py-4 font-mono text-xs font-bold uppercase">Time</th>
          <th class="px-6 py-4 font-mono text-xs font-bold uppercase text-right">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-[#2B2B2B]/10">
        @foreach([
          ['id'=>'ORD-7721','customer'=>'James Henderson','items'=>'2x Meat Lover, 1x Garlic Bread','total'=>'£39.50','status'=>'Accepted','time'=>'4m ago','color'=>'bg-primary'],
          ['id'=>'ORD-7720','customer'=>'Sarah Mitchell','items'=>'1x Tartufo Bianco, 2x Moretti','total'=>'£29.50','status'=>'Cooking','time'=>'11m ago','color'=>'bg-yellow-500'],
          ['id'=>'ORD-7718','customer'=>'Table 04','items'=>'1x Classic Margherita, 2x Moretti','total'=>'£25.50','status'=>'Cooking','time'=>'12m ago','color'=>'bg-yellow-500'],
          ['id'=>'ORD-7715','customer'=>'Marco Vitale','items'=>'3x Spicy Diavola (GF)','total'=>'£43.50','status'=>'Ready','time'=>'18m ago','color'=>'bg-green-600'],
          ['id'=>'ORD-7710','customer'=>'Emma Collins','items'=>'1x Vegan Garden, 1x Cacio e Pepe','total'=>'£24.50','status'=>'Delivered','time'=>'34m ago','color'=>'bg-[#2B2B2B]'],
        ] as $order)
        <tr class="hover:bg-surface-container-low transition-colors">
          <td class="px-6 py-4 font-mono text-sm font-bold">{{ $order['id'] }}</td>
          <td class="px-6 py-4 font-sans text-sm">{{ $order['customer'] }}</td>
          <td class="px-6 py-4 font-sans text-sm text-on-surface-variant">{{ $order['items'] }}</td>
          <td class="px-6 py-4 font-mono text-sm font-bold">{{ $order['total'] }}</td>
          <td class="px-6 py-4">
            <span class="{{ $order['color'] }} text-white text-[10px] px-2 py-0.5 font-bold uppercase rounded-full">{{ $order['status'] }}</span>
          </td>
          <td class="px-6 py-4 font-mono text-xs text-on-surface-variant">{{ $order['time'] }}</td>
          <td class="px-6 py-4 text-right">
            <a href="{{ route('admin.orders.detail', 'preview') }}"
               class="p-2 hover:bg-surface-container rounded transition-colors inline-block">
              <span class="material-symbols-outlined text-on-surface-variant text-lg">visibility</span>
            </a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@endsection
```

- [ ] **Step 2: Commit**

```bash
git add resources/views/admin/orders/index.blade.php
git commit -m "feat: admin orders list page"
```

---

## Task 5: Admin Order Detail + In-Store Orders

**Stitch source (detail):** `aces_eights_admin_order_detail_desktop/code.html` (lines 139–328)
**Stitch source (in-store):** `aces_eights_in_store_order_management_admin/code.html`

**Files:**
- Modify: `resources/views/admin/orders/detail.blade.php`
- Modify: `resources/views/admin/orders/in-store.blade.php`

- [ ] **Step 1: Write order detail view**

Read `C:\AcesAndEightsPizza\Project\stitch_designs\stitch_iterative_design_execution\aces_eights_admin_order_detail_desktop\code.html` lines 139–328.

Replace `resources/views/admin/orders/detail.blade.php`:

```blade
@extends('layouts.admin')
@section('content')

{{-- Order Header & Identity --}}
<section class="flex flex-col gap-6 mb-10">
  <div class="flex justify-between items-end">
    <div>
      <h2 class="font-serif text-3xl font-bold text-on-surface">ORDER #AE-9842</h2>
      <p class="font-sans text-sm text-on-surface-variant mt-1">Customer: <span class="font-bold text-on-surface">Dominic Vitale</span></p>
    </div>
    <div class="bg-secondary-container border-2 border-on-surface px-6 py-2 flex items-center gap-3">
      <span class="material-symbols-outlined text-on-secondary-fixed-variant" style="font-variation-settings:'FILL' 1">restaurant</span>
      <span class="font-mono text-xs font-bold text-on-secondary-fixed-variant uppercase">PREPARING</span>
    </div>
  </div>
  <div class="industrial-divider w-full"></div>
</section>

{{-- Multi-column layout --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">

  {{-- Left: Order details --}}
  <div class="lg:col-span-8 flex flex-col gap-10">

    {{-- Lifecycle stepper --}}
    <section class="bg-surface-container border-2 border-on-surface p-8 flex flex-col gap-10">
      <h3 class="font-mono text-sm font-bold text-primary uppercase tracking-widest">Order Lifecycle</h3>
      <div class="relative flex items-center justify-between w-full px-4">
        <div class="absolute top-1/2 left-0 w-full h-1 bg-outline-variant -translate-y-1/2 z-0"></div>
        <div class="absolute top-1/2 left-0 w-1/3 h-1 bg-primary -translate-y-1/2 z-0"></div>
        @foreach([
          ['icon'=>'check', 'label'=>'Accepted', 'done'=>true],
          ['icon'=>'restaurant', 'label'=>'Cooking', 'done'=>true, 'active'=>true],
          ['icon'=>'local_shipping', 'label'=>'Dispatch', 'done'=>false],
          ['icon'=>'home', 'label'=>'Delivered', 'done'=>false],
        ] as $step)
        <div class="flex flex-col items-center gap-2 z-10">
          <div class="{{ ($step['done'] ?? false) ? 'bg-primary text-white border-on-surface' : 'bg-surface-container-highest text-on-surface-variant border-outline' }} w-12 h-12 rounded-full border-2 flex items-center justify-center shadow-lg">
            <span class="material-symbols-outlined text-[24px]">{{ $step['icon'] }}</span>
          </div>
          <span class="font-mono text-[10px] {{ ($step['active'] ?? false) ? 'text-primary font-bold' : 'text-on-surface-variant' }} uppercase">{{ $step['label'] }}</span>
        </div>
        @endforeach
      </div>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
        <button class="bg-primary-container text-on-primary font-mono text-xs font-bold py-4 px-4 border-b-2 border-secondary-fixed active:scale-95 transition-all hover:bg-primary uppercase">ACCEPT ORDER</button>
        <button class="bg-surface border-2 border-on-surface text-on-surface font-mono text-xs font-bold py-4 px-4 active:scale-95 transition-all hover:bg-surface-container-low uppercase">START COOKING</button>
        <button class="bg-surface border-2 border-on-surface text-on-surface font-mono text-xs font-bold py-4 px-4 active:scale-95 transition-all hover:bg-surface-container-low uppercase">OUT FOR DELIVERY</button>
        <button class="bg-brand-error text-white font-mono text-xs font-bold py-4 px-4 active:scale-95 transition-all hover:opacity-90 uppercase">CANCEL ORDER</button>
      </div>
    </section>

    {{-- Order breakdown --}}
    <section class="border-2 border-on-surface overflow-hidden">
      <div class="bg-on-surface text-surface px-6 py-4 flex justify-between items-center">
        <h3 class="font-serif text-lg font-bold uppercase">Order Breakdown</h3>
        <span class="font-mono text-sm font-bold">Items: 3</span>
      </div>
      <div class="p-8 flex flex-col gap-6 bg-surface-container-lowest">
        <div class="flex justify-between items-start border-b border-outline-variant pb-6">
          <div class="flex flex-col gap-2">
            <span class="font-mono text-sm font-bold">1x THE FULL HOUSE PIZZA (15")</span>
            <div class="flex gap-2">
              <span class="bg-on-primary-fixed-variant text-white text-[10px] px-3 py-1 font-bold">EXTRA SPICY</span>
              <span class="bg-on-primary-fixed-variant text-white text-[10px] px-3 py-1 font-bold">NO OLIVES</span>
            </div>
          </div>
          <span class="font-serif text-xl font-bold">£22.50</span>
        </div>
        <div class="flex justify-between items-start border-b border-outline-variant pb-6">
          <span class="font-mono text-sm font-bold">2x GARLIC BREAD</span>
          <span class="font-serif text-xl font-bold">£11.00</span>
        </div>
        <div class="flex justify-between items-start border-b border-outline-variant pb-6">
          <span class="font-mono text-sm font-bold">1x SAN PELLEGRINO (750ml)</span>
          <span class="font-serif text-xl font-bold">£3.50</span>
        </div>
        <div class="flex flex-col gap-4 pt-4 ml-auto w-full max-w-xs">
          <div class="flex justify-between text-on-surface-variant font-sans text-sm"><span>Subtotal</span><span>£37.00</span></div>
          <div class="flex justify-between text-on-surface-variant font-sans text-sm"><span>Delivery Fee</span><span>£3.50</span></div>
          <div class="flex justify-between pt-4 border-t-2 border-on-surface">
            <span class="font-serif text-lg font-bold uppercase">TOTAL</span>
            <span class="font-serif text-lg font-bold text-primary">£40.50</span>
          </div>
        </div>
      </div>
    </section>
  </div>

  {{-- Right: Sidebar controls --}}
  <aside class="lg:col-span-4 flex flex-col gap-8">

    {{-- Customer Info --}}
    <div class="border-2 border-on-surface p-6 flex flex-col gap-6 bg-surface shadow-[4px_4px_0px_#1b1c1c]">
      <div class="flex items-center justify-between">
        <h3 class="font-mono text-xs font-bold text-primary uppercase tracking-widest">Customer Info</h3>
        <span class="font-mono text-[10px] text-on-surface-variant underline">CUSTOMER HISTORY</span>
      </div>
      <div class="flex flex-col gap-4">
        <div class="flex items-start gap-4">
          <span class="material-symbols-outlined text-primary mt-1">location_on</span>
          <div>
            <p class="font-sans text-sm font-bold">Shipping Address</p>
            <p class="font-sans text-sm text-on-surface-variant">42 Industrial Way, London, NW5 2HP</p>
          </div>
        </div>
        <div class="flex items-center justify-between bg-surface-container-low p-4 border border-outline-variant">
          <div class="flex items-center gap-4">
            <span class="material-symbols-outlined text-primary">phone</span>
            <p class="font-sans text-sm font-bold">+44 7700 900123</p>
          </div>
          <button class="bg-primary text-on-primary p-2 active:scale-95 transition-all">
            <span class="material-symbols-outlined text-[20px]">call</span>
          </button>
        </div>
      </div>
    </div>

    {{-- Dispatch Control --}}
    <div class="border-2 border-on-surface p-6 flex flex-col gap-6 bg-surface shadow-[4px_4px_0px_#1b1c1c]">
      <h3 class="font-mono text-xs font-bold text-primary uppercase tracking-widest">Dispatch Control</h3>
      <div class="flex flex-col gap-5">
        <div class="flex flex-col gap-2">
          <label class="font-mono text-[10px] text-on-surface-variant uppercase tracking-wider">Assign Driver</label>
          <select class="industrial-border-b w-full font-sans text-lg font-bold py-3">
            <option>Not Assigned</option>
            <option>Marco 'Speeder' Rossi</option>
            <option>Luca 'The Wheel' Moretti</option>
          </select>
        </div>
        <div class="flex justify-between items-center bg-surface-container-low p-4">
          <label class="font-mono text-[10px] text-on-surface-variant uppercase tracking-wider">Est. Delivery</label>
          <div class="flex items-center gap-4">
            <button class="w-8 h-8 flex items-center justify-center border-2 border-on-surface hover:bg-on-surface hover:text-surface transition-colors">
              <span class="material-symbols-outlined text-[18px]">remove</span>
            </button>
            <span class="font-serif text-xl font-bold min-w-[70px] text-center">45 MIN</span>
            <button class="w-8 h-8 flex items-center justify-center border-2 border-on-surface hover:bg-on-surface hover:text-surface transition-colors">
              <span class="material-symbols-outlined text-[18px]">add</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    {{-- Admin Notes --}}
    <section class="border-2 border-on-surface p-6 flex flex-col gap-4 bg-surface shadow-[4px_4px_0px_#1b1c1c]">
      <h3 class="font-mono text-xs font-bold text-primary uppercase tracking-widest">Admin & Kitchen Notes</h3>
      <textarea class="industrial-border-b w-full min-h-[120px] font-sans text-sm resize-none p-2 italic bg-surface-container-lowest" placeholder="Add notes for the kitchen or driver..."></textarea>
      <div class="flex justify-end">
        <button class="font-mono text-xs font-bold text-on-surface border-b-2 border-primary hover:text-primary transition-colors uppercase py-1">Save Note</button>
      </div>
    </section>

  </aside>
</div>

@endsection
```

- [ ] **Step 2: Write in-store orders view**

Read `C:\AcesAndEightsPizza\Project\stitch_designs\stitch_iterative_design_execution\aces_eights_in_store_order_management_admin\code.html` and extract the `<main>` content.

Replace `resources/views/admin/orders/in-store.blade.php` with the extracted main content adapted to Blade:
- Remove `<main>` wrapper tags
- Replace any `<script>` blocks with Alpine.js `x-data` on relevant elements
- Replace images with placehold.co
- Wrap in `@extends('layouts.admin') @section('content') ... @endsection`
- Replace any fake addresses with real London address

- [ ] **Step 3: Commit**

```bash
git add resources/views/admin/orders/
git commit -m "feat: admin order detail and in-store orders pages"
```

---

## Task 6: Kitchen Command Center

**Stitch source:** `aces_eights_kitchen_status_lifecycle_tracking_admin/code.html` (lines 178–358)

**Files:**
- Create: `resources/views/admin/kitchen/index.blade.php`

- [ ] **Step 1: Create the view directory and file**

```bash
New-Item -ItemType Directory -Force -Path C:\AcesAndEightsPizza\webapp\resources\views\admin\kitchen
```

- [ ] **Step 2: Write the kitchen command view**

Read `C:\AcesAndEightsPizza\Project\stitch_designs\stitch_iterative_design_execution\aces_eights_kitchen_status_lifecycle_tracking_admin\code.html` lines 178–358.

Create `resources/views/admin/kitchen/index.blade.php`:

```blade
@extends('layouts.admin')
@section('content')

{{-- Page Title --}}
<section class="mb-12">
  <h1 class="font-serif text-4xl font-black text-primary tracking-tighter mb-4 uppercase">KITCHEN COMMAND</h1>
  <div class="double-divider w-full"></div>
  <div class="flex flex-wrap justify-between items-center mt-6 gap-4">
    <div class="flex gap-4 items-center">
      <span class="font-mono text-xs font-bold bg-secondary-container text-on-secondary-container px-4 py-1 border border-on-surface uppercase">LIVE STATUS: ACTIVE</span>
      <span class="font-mono text-xs text-on-surface-variant italic">Refreshed: <span x-data="{ s: 0 }" x-init="setInterval(() => s = (s+1)%60, 1000)" x-text="s + 's ago'">0s ago</span></span>
    </div>
    <div class="flex gap-2">
      <button class="border-2 border-on-surface px-6 py-2 font-mono text-xs font-bold uppercase hover:bg-surface-container-highest transition-all flex items-center gap-2">
        <span class="material-symbols-outlined">print</span> PRINT BATCH
      </button>
      <button class="bg-primary text-on-primary border-2 border-on-surface px-6 py-2 font-mono text-xs font-bold uppercase active:scale-95 transition-all flex items-center gap-2">
        <span class="material-symbols-outlined">add_task</span> NEW ORDER
      </button>
    </div>
  </div>
</section>

{{-- Kanban Board --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

  {{-- Column 1: Order Queue --}}
  <div class="flex flex-col gap-4">
    <div class="flex items-center justify-between border-b-2 border-on-surface pb-2">
      <h3 class="font-serif text-xl font-bold text-primary uppercase">ORDER QUEUE</h3>
      <span class="bg-outline text-on-primary px-3 py-1 font-mono text-xs font-bold rounded-full">3</span>
    </div>
    <div class="flex flex-col gap-4">
      <div class="bg-surface border-2 border-on-surface p-4 flex flex-col gap-4 shadow-[4px_4px_0px_0px_rgba(27,28,28,1)]">
        <div class="flex justify-between items-start">
          <div>
            <p class="font-mono text-xs font-bold text-on-surface-variant">ORDER #1289</p>
            <p class="font-serif text-lg font-bold">The "Dead Man's Hand"</p>
          </div>
          <span class="text-brand-error font-mono text-xs font-bold flex items-center gap-1">
            <span class="material-symbols-outlined text-[18px]">timer</span> 08:42
          </span>
        </div>
        <div class="border-t border-outline-variant pt-2 font-sans text-sm">
          <p>• 1x Large Buffalo Pizza (Extra Spice)</p>
          <p>• 2x Garlic Bread</p>
          <p>• 1x Peroni 500ml</p>
        </div>
        <button class="w-full bg-primary text-on-primary py-3 font-mono text-xs font-bold border-b-4 border-on-primary-fixed-variant hover:brightness-110 active:translate-y-1 active:border-b-0 transition-all uppercase tracking-widest">
          ACCEPT ORDER
        </button>
      </div>
      <div class="bg-surface border-2 border-on-surface p-4 flex flex-col gap-4 shadow-[4px_4px_0px_0px_rgba(27,28,28,1)]">
        <div class="flex justify-between items-start">
          <div>
            <p class="font-mono text-xs font-bold text-on-surface-variant">ORDER #1291</p>
            <p class="font-serif text-lg font-bold">Custom Supreme</p>
          </div>
          <span class="font-mono text-xs font-bold flex items-center gap-1">
            <span class="material-symbols-outlined text-[18px]">timer</span> 02:15
          </span>
        </div>
        <div class="border-t border-outline-variant pt-2 font-sans text-sm">
          <p>• 2x Medium Supreme Thin Crust</p>
          <p class="text-primary font-bold">No Bell Peppers</p>
        </div>
        <button class="w-full bg-primary text-on-primary py-3 font-mono text-xs font-bold border-b-4 border-on-primary-fixed-variant hover:brightness-110 active:translate-y-1 active:border-b-0 transition-all uppercase tracking-widest">
          ACCEPT ORDER
        </button>
      </div>
    </div>
  </div>

  {{-- Column 2: In the Kitchen --}}
  <div class="flex flex-col gap-4">
    <div class="flex items-center justify-between border-b-2 border-on-surface pb-2">
      <h3 class="font-serif text-xl font-bold text-primary uppercase">IN THE KITCHEN</h3>
      <span class="bg-primary text-on-primary px-3 py-1 font-mono text-xs font-bold rounded-full">2</span>
    </div>
    <div class="flex flex-col gap-4">
      <div class="bg-surface border-2 border-on-surface p-4 flex flex-col gap-4 shadow-[4px_4px_0px_0px_rgba(27,28,28,1)] relative overflow-hidden">
        <div class="absolute top-0 right-0 p-2">
          <span class="material-symbols-outlined text-primary" style="font-variation-settings:'FILL' 1">local_fire_department</span>
        </div>
        <div class="flex justify-between items-start">
          <div>
            <p class="font-mono text-xs font-bold text-on-surface-variant">ORDER #1285</p>
            <p class="font-serif text-lg font-bold">Meat Lovers Feast</p>
          </div>
          <span class="text-brand-error font-mono text-xs font-bold flex items-center gap-1">
            <span class="material-symbols-outlined text-[18px]">timer</span> 22:10
          </span>
        </div>
        <div class="border-t border-outline-variant pt-2 font-sans text-sm">
          <p>• 1x XL Meat Lover</p>
          <p>• 1x Spicy Salami Plate</p>
          <p class="text-primary font-bold mt-2">CHEF'S NOTE: EXTRA OVEN TIME</p>
        </div>
        <div class="w-full bg-surface-container-highest h-2 border border-on-surface mb-2">
          <div class="bg-primary h-full w-[75%]"></div>
        </div>
        <button class="w-full bg-secondary text-on-secondary py-3 font-mono text-xs font-bold border-b-4 border-on-secondary-fixed-variant hover:brightness-110 active:translate-y-1 active:border-b-0 transition-all uppercase tracking-widest">
          MARK AS READY
        </button>
      </div>
    </div>
  </div>

  {{-- Column 3: Ready for Dispatch --}}
  <div class="flex flex-col gap-4">
    <div class="flex items-center justify-between border-b-2 border-on-surface pb-2">
      <h3 class="font-serif text-xl font-bold text-primary uppercase">READY FOR DISPATCH</h3>
      <span class="bg-secondary text-on-secondary px-3 py-1 font-mono text-xs font-bold rounded-full">1</span>
    </div>
    <div class="flex flex-col gap-4">
      <div class="bg-secondary-fixed border-2 border-on-surface p-4 flex flex-col gap-4 shadow-[4px_4px_0px_0px_rgba(27,28,28,1)]">
        <div>
          <p class="font-mono text-xs font-bold text-on-secondary-fixed">ORDER #1280</p>
          <p class="font-serif text-lg font-bold">Office Party Pack</p>
        </div>
        <div class="border-t border-on-secondary-fixed-variant/20 pt-2 font-sans text-sm text-on-secondary-fixed">
          <p>• 5x Large Pepperoni</p>
          <p>• 4x 2L Coke</p>
        </div>
        <div class="flex flex-col gap-2 mt-2">
          <label class="font-mono text-[10px] text-on-secondary-fixed uppercase opacity-70">Assign Driver</label>
          <select class="w-full bg-white border-2 border-on-surface py-2 px-3 font-mono text-xs font-bold">
            <option>SELECT DRIVER...</option>
            <option>MARCO (ACTIVE)</option>
            <option>TONY (IDLE)</option>
          </select>
        </div>
        <button class="w-full bg-on-surface text-surface py-3 font-mono text-xs font-bold border-b-4 border-primary hover:brightness-125 transition-all uppercase tracking-widest">
          DISPATCH ORDER
        </button>
      </div>
    </div>
  </div>

  {{-- Column 4: Out for Delivery --}}
  <div class="flex flex-col gap-4">
    <div class="flex items-center justify-between border-b-2 border-on-surface pb-2">
      <h3 class="font-serif text-xl font-bold text-primary uppercase">OUT FOR DELIVERY</h3>
      <span class="bg-on-surface text-surface px-3 py-1 font-mono text-xs font-bold rounded-full">4</span>
    </div>
    <div class="flex flex-col gap-4">
      @foreach([
        ['order'=>'#1275','name'=>'Vegetarian Pesto','driver'=>'MARCO','address'=>'42 Fortess Road, NW5'],
        ['order'=>'#1272','name'=>'Double Pepperoni','driver'=>'TONY','address'=>'14 Junction Rd, N19'],
      ] as $delivery)
      <div class="bg-surface border-2 border-on-surface p-4 flex flex-col gap-4 opacity-75 shadow-[2px_2px_0px_0px_rgba(27,28,28,1)]">
        <div>
          <p class="font-mono text-xs font-bold text-on-surface-variant">ORDER {{ $delivery['order'] }}</p>
          <p class="font-serif text-lg font-bold">{{ $delivery['name'] }}</p>
        </div>
        <div class="border-t border-outline-variant pt-2 font-mono text-[10px]">
          <p class="flex items-center gap-2"><span class="material-symbols-outlined text-[16px]">person</span> DRIVER: {{ $delivery['driver'] }}</p>
          <p class="flex items-center gap-2"><span class="material-symbols-outlined text-[16px]">location_on</span> {{ $delivery['address'] }}</p>
        </div>
        <button class="w-full border-2 border-on-surface py-2 font-mono text-[10px] font-bold uppercase hover:bg-surface-container-highest transition-all">
          TRACK MAP
        </button>
      </div>
      @endforeach
    </div>
  </div>

</div>

@endsection
```

- [ ] **Step 3: Commit**

```bash
git add resources/views/admin/kitchen/
git commit -m "feat: admin kitchen command center page"
```

---

## Task 7: Admin Menu Management

**Stitch source (list):** `aces_eights_menu_management_desktop_alignment_fix/code.html` (lines 213–390)
**Stitch source (edit):** `aces_eights_add_edit_menu_item/code.html` (lines 161–end)

**Files:**
- Modify: `resources/views/admin/menu/index.blade.php`
- Modify: `resources/views/admin/menu/edit.blade.php`

- [ ] **Step 1: Write menu management list**

Replace `resources/views/admin/menu/index.blade.php`:

```blade
@extends('layouts.admin')
@section('content')

{{-- Header --}}
<header class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
  <div class="flex-1">
    <h2 class="font-serif text-4xl font-black text-on-surface uppercase tracking-tight leading-none mb-4">Menu Management</h2>
    <p class="font-sans text-sm text-on-surface-variant max-w-2xl">Modify your offerings, adjust pricing, and toggle item availability for the daily service ledger.</p>
  </div>
  <a href="{{ route('admin.menu.create') }}"
     class="gold-button flex items-center justify-center gap-2 px-6 py-3 font-mono text-xs font-bold uppercase industrial-border whitespace-nowrap">
    <span class="material-symbols-outlined">add</span> ADD NEW ITEM
  </a>
</header>
<div class="double-divider mb-8"></div>

{{-- Search & Filters --}}
<section class="mb-10 space-y-4" x-data="{ category: 'all', search: '' }">
  <div class="relative w-full">
    <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-on-surface-variant">search</span>
    <input x-model="search" class="w-full pl-12 pr-4 py-4 bg-surface-container-low border-industrial-gray border focus:outline-none focus:ring-1 focus:ring-primary font-sans text-sm" placeholder="Search menu items..." type="text"/>
  </div>
  <div class="flex flex-wrap gap-2">
    @foreach(['all'=>'All Items','pizza'=>'Pizzas','starter'=>'Starters','salad'=>'Salads','pasta'=>'Pasta','dessert'=>'Desserts','drink'=>'Drinks'] as $val => $label)
    <button @click="category = '{{ $val }}'"
            :class="category === '{{ $val }}' ? 'bg-primary text-white' : 'bg-transparent hover:bg-surface-container text-on-surface'"
            class="px-5 py-2 rounded-full font-mono text-[10px] font-bold industrial-border transition-colors uppercase">{{ $label }}</button>
    @endforeach
  </div>

  {{-- Table --}}
  <div class="bg-surface industrial-border overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse min-w-[700px]">
        <thead>
          <tr class="bg-surface-container-high border-b border-[#2B2B2B]">
            <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase tracking-wider">Item</th>
            <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase tracking-wider">Category</th>
            <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase tracking-wider">Price</th>
            <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase tracking-wider text-center">Active</th>
            <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase tracking-wider text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#2B2B2B]/20">
          @foreach([
            ['id'=>'classic-margherita','name'=>'Classic Margherita','desc'=>'San Marzano D.O.P, Fior di latte, Basil','cat'=>'Pizzas','price'=>'£12.50','active'=>true],
            ['id'=>'spicy-diavola','name'=>'Spicy Diavola','desc'=>'San Marzano, Mozzarella, Nduja, Calabrese Salami','cat'=>'Pizzas','price'=>'£14.50','active'=>true],
            ['id'=>'the-meat-lover','name'=>'The Meat Lover','desc'=>'Tomato, Mozzarella, Salami, Smoked Pancetta','cat'=>'Pizzas','price'=>'£17.00','active'=>true],
            ['id'=>'garlic-bread','name'=>'Garlic Bread','desc'=>'Sourdough, Roasted Garlic Butter, Parsley','cat'=>'Starters','price'=>'£5.50','active'=>true],
            ['id'=>'tiramisu','name'=>'Tiramisu','desc'=>'Mascarpone, Savoiardi, Espresso, Valrhona Cocoa','cat'=>'Desserts','price'=>'£7.00','active'=>true],
            ['id'=>'moretti-draft','name'=>'Moretti Draft','desc'=>'Italian lager on draft, frosted glass','cat'=>'Drinks','price'=>'£6.50','active'=>false],
          ] as $item)
          <tr class="{{ !$item['active'] ? 'opacity-60' : '' }} hover:bg-surface-container-low transition-colors">
            <td class="px-6 py-4">
              <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-surface-container industrial-border overflow-hidden flex-shrink-0">
                  <img src="https://placehold.co/56x56/e4e2e1/1b1c1c?text=+" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                </div>
                <div>
                  <p class="font-mono text-xs font-bold text-on-surface">{{ $item['name'] }}</p>
                  <p class="font-sans text-xs text-on-surface-variant">{{ $item['desc'] }}</p>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 font-sans text-sm">{{ $item['cat'] }}</td>
            <td class="px-6 py-4 font-mono text-sm font-bold">{{ $item['price'] }}</td>
            <td class="px-6 py-4">
              <div class="flex justify-center" x-data="{ on: {{ $item['active'] ? 'true' : 'false' }} }">
                <label class="flex items-center cursor-pointer">
                  <div class="relative">
                    <input @change="on = $event.target.checked" :checked="on" class="sr-only" type="checkbox"/>
                    <div :class="on ? 'bg-primary' : 'bg-[#2B2B2B]/30'" class="block w-10 h-6 rounded-full transition-colors">
                      <div :class="on ? 'translate-x-4' : 'translate-x-1'" class="absolute top-1 left-0 bg-white w-4 h-4 rounded-full transition-transform"></div>
                    </div>
                  </div>
                </label>
              </div>
            </td>
            <td class="px-6 py-4 text-right">
              <div class="flex justify-end gap-2">
                <a href="{{ route('admin.menu.edit', $item['id']) }}" class="p-2 hover:bg-surface-container rounded transition-colors" title="Edit">
                  <span class="material-symbols-outlined text-on-surface-variant">edit</span>
                </a>
                <button class="p-2 hover:bg-brand-error/10 rounded transition-colors" title="Delete">
                  <span class="material-symbols-outlined text-brand-error">delete</span>
                </button>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  {{-- Pagination --}}
  <div class="mt-6 flex flex-col md:flex-row justify-between items-center gap-4 text-on-surface-variant font-mono text-xs font-bold">
    <p>Showing 6 of 42 active menu items</p>
    <div class="flex gap-2">
      <button class="p-2 industrial-border hover:bg-surface-container transition-colors opacity-50" disabled>
        <span class="material-symbols-outlined">chevron_left</span>
      </button>
      <button class="px-4 py-2 industrial-border bg-primary text-white">1</button>
      <button class="px-4 py-2 industrial-border hover:bg-surface-container transition-colors">2</button>
      <button class="px-4 py-2 industrial-border hover:bg-surface-container transition-colors">3</button>
      <button class="p-2 industrial-border hover:bg-surface-container transition-colors">
        <span class="material-symbols-outlined">chevron_right</span>
      </button>
    </div>
  </div>
</section>

@endsection
```

- [ ] **Step 2: Write add/edit menu item view**

Read `C:\AcesAndEightsPizza\Project\stitch_designs\stitch_iterative_design_execution\aces_eights_add_edit_menu_item\code.html` lines 161–end.

Replace `resources/views/admin/menu/edit.blade.php` with the extracted `<main>` content adapted to Blade:
- Remove `<main>` wrapper tags
- Wrap in `@extends('layouts.admin') @section('content') ... @endsection`  
- Replace stitch header with: `<div class="flex items-center gap-2 text-on-surface-variant mb-2 text-sm"><a href="{{ route('admin.menu.index') }}" class="hover:text-primary">Menu Management</a> <span>›</span> <span>{{ $itemId ?? 'Add New Item' }}</span></div>`
- Replace `<form class="grid..." >` wrapper — keep the form but add `method="POST" action="#"` and `@csrf`
- Replace Google CDN image src values with `https://placehold.co/400x400/e4e2e1/1b1c1c?text=Upload+Photo`
- Keep ALL other Tailwind classes and Alpine/interaction markup exactly as-is
- Replace final Save/Publish button with: `<button type="submit" class="w-full bg-primary text-on-primary py-4 font-mono text-xs font-bold uppercase tracking-widest">SAVE CHANGES</button>`

- [ ] **Step 3: Commit**

```bash
git add resources/views/admin/menu/
git commit -m "feat: admin menu management list and edit pages"
```

---

## Task 8: Allergy Management

**Stitch source:** `aces_eights_allergy_information_management_admin/code.html` (lines 160–293)

**Files:**
- Create: `resources/views/admin/allergy/index.blade.php`

- [ ] **Step 1: Create directory**

```bash
New-Item -ItemType Directory -Force -Path C:\AcesAndEightsPizza\webapp\resources\views\admin\allergy
```

- [ ] **Step 2: Write the allergy management view**

Read `C:\AcesAndEightsPizza\Project\stitch_designs\stitch_iterative_design_execution\aces_eights_allergy_information_management_admin\code.html` lines 160–293.

Create `resources/views/admin/allergy/index.blade.php`:

```blade
@extends('layouts.admin')
@section('content')

{{-- Header --}}
<section class="mb-8">
  <h2 class="font-serif text-4xl font-black text-on-surface uppercase tracking-tight">ALLERGY MANAGEMENT</h2>
  <div class="double-divider text-on-surface-variant mt-2"></div>
</section>

{{-- Section 1: Global Allergy Alerts --}}
<section class="mb-10 bg-surface-container-low border-2 border-on-surface p-6" x-data="{ alerts: true }">
  <div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
      <span class="material-symbols-outlined text-primary" style="font-variation-settings:'FILL' 1">campaign</span>
      <h3 class="font-mono text-xs font-bold uppercase">Global Safety Alerts</h3>
    </div>
    <label class="relative inline-flex items-center cursor-pointer">
      <input x-model="alerts" class="sr-only peer" type="checkbox" checked/>
      <div class="w-11 h-6 bg-surface-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
    </label>
  </div>
  <p class="font-sans text-sm text-on-surface-variant leading-relaxed">
    When enabled, a high-visibility warning banner will appear across the top of the consumer site regarding ingredient cross-contamination and the current allergy protocol.
  </p>
</section>

{{-- Section 2: Allergen Library --}}
<section class="mb-10">
  <div class="flex items-center justify-between mb-4">
    <h3 class="font-mono text-xs font-bold uppercase">Allergen Library</h3>
    <button class="text-primary font-mono text-[10px] font-bold flex items-center gap-1 border-b border-primary uppercase">
      <span class="material-symbols-outlined text-[18px]">add</span> ADD NEW
    </button>
  </div>
  <div class="grid grid-cols-1 gap-3">
    @foreach([
      ['icon'=>'egg','label'=>'Dairy & Eggs','active'=>true],
      ['icon'=>'bakery_dining','label'=>'Gluten / Wheat','active'=>true],
      ['icon'=>'set_meal','label'=>'Shellfish','active'=>false],
      ['icon'=>'nutrition','label'=>'Tree Nuts','active'=>true],
      ['icon'=>'grass','label'=>'Soy','active'=>true],
    ] as $allergen)
    <div class="bg-surface border border-on-surface flex items-center p-3 justify-between">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-primary-container flex items-center justify-center text-on-primary">
          <span class="material-symbols-outlined">{{ $allergen['icon'] }}</span>
        </div>
        <span class="font-mono text-xs font-bold uppercase">{{ $allergen['label'] }}</span>
      </div>
      <div class="flex items-center gap-4">
        <span class="material-symbols-outlined text-on-surface-variant {{ $allergen['active'] ? '' : 'text-primary' }}" style="font-variation-settings:'FILL' 1">
          {{ $allergen['active'] ? 'visibility' : 'visibility_off' }}
        </span>
        <span class="material-symbols-outlined text-on-surface-variant">edit</span>
      </div>
    </div>
    @endforeach
  </div>
</section>

{{-- Section 3: Menu Item Mapping --}}
<section class="mb-10">
  <h3 class="font-mono text-xs font-bold uppercase mb-4">Menu Item Allergen Mapping</h3>
  <div class="relative mb-6">
    <input class="w-full bg-surface border-0 border-b-2 border-on-surface py-3 pl-10 focus:ring-0 focus:border-primary placeholder:text-on-surface-variant/50 font-sans text-sm"
           placeholder="Search menu items (e.g. Margherita, Garlic Bread)" type="text"/>
    <span class="material-symbols-outlined absolute left-2 top-3 text-on-surface-variant">search</span>
  </div>
  <div class="border-2 border-on-surface overflow-hidden">
    <div class="bg-on-surface text-surface p-4 flex justify-between items-center">
      <h4 class="font-mono text-xs font-bold uppercase">Classic Margherita</h4>
      <span class="font-mono text-[10px]">SKU: AE-001</span>
    </div>
    <div class="p-4 bg-surface-container">
      <p class="font-mono text-[10px] uppercase mb-3 text-on-surface-variant">Active Allergen Tags:</p>
      <div class="flex flex-wrap gap-2 mb-6" x-data="{ tags: ['Gluten','Dairy'] }">
        <template x-for="tag in tags" :key="tag">
          <span class="bg-primary text-white text-[10px] font-bold px-3 py-1 rounded-full flex items-center gap-1 uppercase">
            <span x-text="tag"></span>
            <span @click="tags.splice(tags.indexOf(tag),1)" class="material-symbols-outlined text-[12px] cursor-pointer">close</span>
          </span>
        </template>
        <button class="bg-on-surface text-surface text-[10px] font-bold px-3 py-1 rounded-full flex items-center gap-1 uppercase">
          <span class="material-symbols-outlined text-[12px]">add</span> ADD TAG
        </button>
      </div>
      <button class="w-full border-2 border-on-surface py-3 text-on-surface font-mono text-xs font-bold uppercase hover:bg-surface-container-highest transition-all active:scale-[0.98]">
        SAVE ITEM MAPPING
      </button>
    </div>
  </div>
</section>

{{-- Section 4: Checkout Disclaimer CMS --}}
<section class="mb-8">
  <h3 class="font-mono text-xs font-bold uppercase mb-4">Checkout Disclaimer CMS</h3>
  <div class="border-2 border-on-surface bg-white">
    <div class="border-b border-on-surface flex gap-2 p-2 bg-surface-container">
      <button class="p-1 hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined">format_bold</span></button>
      <button class="p-1 hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined">format_italic</span></button>
      <button class="p-1 hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined">link</span></button>
      <div class="w-[1px] h-6 bg-on-surface-variant/30 self-center"></div>
      <button class="p-1 hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined">history</span></button>
    </div>
    <textarea class="w-full border-0 p-4 font-sans text-sm leading-relaxed focus:ring-0 resize-none" rows="6">ACES & EIGHTS PIZZA CO. TAKES FOOD SAFETY SERIOUSLY. Please be advised that our kitchen handles wheat, dairy, and eggs. While we take meticulous steps to prevent cross-contact, we cannot guarantee a 100% allergen-free environment for those with severe sensitivities. By proceeding with your order, you acknowledge these risks. Contact our floor manager for specific ingredient concerns.</textarea>
  </div>
  <p class="font-mono text-[10px] text-on-surface-variant mt-2 italic">* This text is legally required at the point of purchase in all digital storefronts.</p>
</section>

<button class="w-full bg-primary text-on-primary py-5 font-mono text-xs font-bold uppercase tracking-widest shadow-lg active:scale-95 transition-transform">
  PUBLISH CHANGES
</button>

@endsection
```

- [ ] **Step 3: Commit**

```bash
git add resources/views/admin/allergy/
git commit -m "feat: admin allergy management page"
```

---

## Task 9: Delivery Management + General Settings

**Stitch source (delivery):** `aces_eights_delivery_management_desktop_sync/code.html` (lines 146–285)
**Stitch source (settings):** `aces_eights_general_settings_admin/code.html` (lines 159–335)

**Files:**
- Modify: `resources/views/admin/delivery/index.blade.php`
- Modify: `resources/views/admin/settings/index.blade.php`

- [ ] **Step 1: Write delivery management view**

Read `C:\AcesAndEightsPizza\Project\stitch_designs\stitch_iterative_design_execution\aces_eights_delivery_management_desktop_sync\code.html` lines 146–285.

Replace `resources/views/admin/delivery/index.blade.php`:
- Extract main content (lines 146–285)
- Remove `<main>` wrapper
- Wrap in `@extends('layouts.admin') @section('content') ... @endsection`
- Replace all `src="https://lh3.googleusercontent.com/..."` with `src="https://placehold.co/800x500/e4e2e1/1b1c1c?text=Dispatch+Map"`
- Replace currency `$` with `£` in fee inputs
- Replace `href="#"` links with `route('admin.delivery.index')` where self-referential, or `#` for placeholder actions
- Replace fake runner names with: Jimmy Rossi, Sal Agnello, Marco Vitale, Tony Moretti

- [ ] **Step 2: Write general settings view**

Read `C:\AcesAndEightsPizza\Project\stitch_designs\stitch_iterative_design_execution\aces_eights_general_settings_admin\code.html` lines 159–335.

Replace `resources/views/admin/settings/index.blade.php`:
- Extract main content
- Wrap in `@extends('layouts.admin') @section('content') ... @endsection`
- Replace fake address with: `156 & 158 Fortess Road, Tufnell Park, London, NW5 2HP`
- Replace fake phone with: `+44 020 7485 4033`
- Replace fake email with: `nw5pizza@gmail.com`
- Replace fake opening times with: Mon–Thu `16:00–22:45`, Fri `16:00–23:15`, Sat `16:00–23:15`, Sun `16:00–22:45`
- Replace stitch hero text with: `FORGED IN THE FIRE OF TRADITION. SERVED WITH INDUSTRIAL PRECISION. ACES & EIGHTS PIZZA — ESTABLISHED 2012.`
- Replace stitch story text with: `Born from the hum of machinery and the heat of the forge, Aces & Eights was founded on the premise that the best food is made by hand, with tools that have stood the test of time.`
- Replace any `<img src="https://lh3.googleusercontent.com/..."` with `<img src="{{ asset('images/logo.jpg') }}"`
- Replace `href="#"` with `#` for placeholder actions

- [ ] **Step 3: Build and commit**

```bash
npm run build
git add resources/views/admin/delivery/index.blade.php resources/views/admin/settings/index.blade.php
git commit -m "feat: admin delivery management and general settings pages"
```

---

## Task 10: Route Smoke Tests + Final Verification

**Files:**
- Modify: `tests/Feature/RouteSmokeTest.php`

- [ ] **Step 1: Add admin route tests to RouteSmokeTest.php**

Open `tests/Feature/RouteSmokeTest.php` and add:

```php
/**
 * Admin routes return 200 for authenticated admin user.
 *
 * @dataProvider adminRouteProvider
 */
public function test_admin_routes_return_200_for_admin(string $uri): void
{
    $admin = \App\Models\User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get($uri);

    $response->assertStatus(200);
}

public static function adminRouteProvider(): array
{
    return [
        'admin dashboard'  => ['/admin'],
        'admin orders'     => ['/admin/orders'],
        'admin in-store'   => ['/admin/orders/in-store'],
        'admin order detail' => ['/admin/orders/preview'],
        'admin kitchen'    => ['/admin/kitchen'],
        'admin menu'       => ['/admin/menu'],
        'admin menu create'=> ['/admin/menu/create'],
        'admin allergy'    => ['/admin/allergy'],
        'admin delivery'   => ['/admin/delivery'],
        'admin promotions' => ['/admin/promotions'],
        'admin settings'   => ['/admin/settings'],
    ];
}

public function test_admin_routes_redirect_unauthenticated_customer(): void
{
    $customer = \App\Models\User::factory()->create(['role' => 'customer']);

    $response = $this->actingAs($customer)->get('/admin');

    $response->assertStatus(403);
}
```

- [ ] **Step 2: Run tests**

```bash
& "C:\xampp\php\php.exe" artisan test tests/Feature/RouteSmokeTest.php
```

Expected: `Tests: 27+ passed` (16 original + 11 admin + 1 forbidden test).

If a view crashes (e.g. missing component), read the error trace and fix the specific view file.

- [ ] **Step 3: Production build**

```bash
npm run build
```

Expected: `✓ built in Xms` — no errors.

- [ ] **Step 4: Manual verification checklist**

Log in at `http://localhost:8000/login` with `admin@acesandeights.com` / `admin123`.

- [ ] `/admin` → Dashboard with 4 KPI cards, production queue, station load sidebar
- [ ] `/admin/orders` → Orders list with status badges, stats bar
- [ ] `/admin/orders/preview` → Order detail with lifecycle stepper, order breakdown, dispatch panel
- [ ] `/admin/kitchen` → Kanban board with 4 columns (Queue / Kitchen / Dispatch / Delivery)
- [ ] `/admin/menu` → Menu table with toggle switches, search, category filters
- [ ] `/admin/menu/create` → Edit form with Core Details, Allergy checkboxes, Image upload zone, Publishing toggles
- [ ] `/admin/allergy` → Global alerts toggle, allergen library, item mapping, disclaimer CMS
- [ ] `/admin/delivery` → Dispatch map placeholder, active runners list, zonal fees
- [ ] `/admin/settings` → Store info form with real address/phone/email, opening hours grid, CMS panel, System Ledger
- [ ] Admin sidebar: hamburger opens on mobile (Alpine.js drawer works), all links navigate correctly
- [ ] Log out → redirects to homepage

- [ ] **Step 5: Final commit**

```bash
git add .
git commit -m "feat: Plan 3 complete — full admin frontend with auth, all pages, smoke tests"
```
