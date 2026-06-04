# Aces & Eights Pizza — Plan 9: Deploy Prep & Final Polish

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Complete three finishing touches — menu item image uploads, security rate-limiting, and branded error pages — then produce a production-ready deployment checklist for cPanel shared hosting so the client can go live.

**Architecture:** Image upload uses Laravel's `public` disk (stored in `storage/app/public/menu/`, served via `storage/` symlink). Rate-limiting uses Laravel's built-in `throttle` middleware added directly to specific POST routes in `web.php` — no additional packages. Error pages are plain Blade files in `resources/views/errors/` that extend `layouts.app` with branded content; Laravel auto-serves them on HTTP 404/500. Deploy prep produces a `DEPLOY.md` file with step-by-step cPanel setup + a `php artisan deploy` command sequence.

**Tech Stack:** Laravel 11, Blade, Tailwind CSS 3, PHP 8.2, cPanel shared hosting

---

## File Map

| Action | File |
|--------|------|
| Modify | `app/Http/Controllers/Admin/MenuItemController.php` — handle image upload |
| Modify | `resources/views/admin/menu/edit.blade.php` — show existing image |
| Modify | `routes/web.php` — add throttle middleware to POST login/register/checkout |
| Create | `resources/views/errors/404.blade.php` |
| Create | `resources/views/errors/500.blade.php` |
| Create | `resources/views/errors/403.blade.php` |
| Create | `DEPLOY.md` — production deployment guide |
| Create | `tests/Feature/ImageUploadTest.php` |
| Create | `tests/Feature/RateLimitTest.php` |

---

## Task 1: Menu Item Image Upload

**Files:**
- Modify: `app/Http/Controllers/Admin/MenuItemController.php`
- Modify: `resources/views/admin/menu/edit.blade.php`

- [ ] **Step 1: Write failing image upload test**

Create `tests/Feature/ImageUploadTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageUploadTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        Storage::fake('public');
    }

    public function test_admin_can_upload_image_when_creating_menu_item(): void
    {
        $category = Category::factory()->create();
        $image    = UploadedFile::fake()->image('pizza.jpg', 800, 600);

        $this->actingAs($this->admin)->post('/admin/menu', [
            'name'         => 'Test Pizza',
            'category_id'  => $category->id,
            'base_price'   => '12.50',
            'is_available' => '1',
            'image'        => $image,
        ]);

        $item = MenuItem::where('name', 'Test Pizza')->first();
        $this->assertNotNull($item->image_path);
        Storage::disk('public')->assertExists($item->image_path);
    }

    public function test_admin_can_upload_image_when_updating_menu_item(): void
    {
        $item  = MenuItem::factory()->create(['image_path' => null]);
        $image = UploadedFile::fake()->image('new.jpg', 800, 600);

        $this->actingAs($this->admin)->put("/admin/menu/{$item->id}", [
            'name'         => $item->name,
            'category_id'  => $item->category_id,
            'base_price'   => $item->base_price,
            'is_available' => '1',
            'image'        => $image,
        ]);

        $item->refresh();
        $this->assertNotNull($item->image_path);
        Storage::disk('public')->assertExists($item->image_path);
    }

    public function test_old_image_deleted_when_new_image_uploaded(): void
    {
        Storage::disk('public')->put('menu/old.jpg', 'fake image content');
        $item = MenuItem::factory()->create(['image_path' => 'menu/old.jpg']);

        $image = UploadedFile::fake()->image('replacement.jpg');

        $this->actingAs($this->admin)->put("/admin/menu/{$item->id}", [
            'name'         => $item->name,
            'category_id'  => $item->category_id,
            'base_price'   => $item->base_price,
            'is_available' => '1',
            'image'        => $image,
        ]);

        Storage::disk('public')->assertMissing('menu/old.jpg');
        Storage::disk('public')->assertExists($item->fresh()->image_path);
    }

    public function test_image_is_optional(): void
    {
        $category = Category::factory()->create();

        $this->actingAs($this->admin)->post('/admin/menu', [
            'name'        => 'No Image Pizza',
            'category_id' => $category->id,
            'base_price'  => '10.00',
        ]);

        $item = MenuItem::where('name', 'No Image Pizza')->first();
        $this->assertNull($item?->image_path);
    }
}
```

Run: `& "C:\xampp\php\php.exe" artisan test tests/Feature/ImageUploadTest.php` — expect failures.

- [ ] **Step 2: Update MenuItemController to handle image uploads**

Open `app/Http/Controllers/Admin/MenuItemController.php`. Add import at top:

```php
use Illuminate\Support\Facades\Storage;
```

In the `store()` method, after the existing validation, add image handling. Find where `$item = MenuItem::create([...])` is called and add this BEFORE it:

```php
// Handle image upload
$imagePath = null;
if ($request->hasFile('image')) {
    $imagePath = $request->file('image')->store('menu', 'public');
}
```

Then inside `MenuItem::create([...])`, add `'image_path' => $imagePath,`.

In the `update()` method, add this BEFORE `$menuItem->update([...])`:

```php
// Handle image upload
if ($request->hasFile('image')) {
    // Delete old image if exists
    if ($menuItem->image_path) {
        Storage::disk('public')->delete($menuItem->image_path);
    }
    $menuItem->image_path = $request->file('image')->store('menu', 'public');
}
```

Then in `$menuItem->update([...])`, add `'image_path' => $menuItem->image_path,`.

Also update the validation in the `validated()` helper method to include image:

```php
'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
```

The full updated `validated()` private method:

```php
private function validated(Request $request, ?int $excludeId = null): array
{
    $data = $request->validate([
        'name'             => 'required|string|max:255',
        'category_id'      => 'required|exists:categories,id',
        'description'      => 'nullable|string',
        'base_price'       => 'required|numeric|min:0',
        'is_available'     => 'boolean',
        'is_featured'      => 'boolean',
        'allergens'        => 'nullable|array',
        'allergens.*'      => 'exists:allergens,id',
        'ingredients'      => 'nullable|array',
        'ingredients.*'    => 'string|max:100',
        'image'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
    ]);

    $data['is_available'] = $request->boolean('is_available');
    $data['is_featured']  = $request->boolean('is_featured');

    return $data;
}
```

Note: the `image` field is NOT returned in `$data` from `validated()` — it's handled separately in `store()` and `update()` directly via `$request->file('image')`. The validation in `validated()` just ensures it's valid if present.

- [ ] **Step 3: Update admin menu edit view to show existing image**

Open `resources/views/admin/menu/edit.blade.php`. Find the image upload zone (the div with `cloud_upload` icon and `data-alt` attribute or `placehold.co` image). Add a preview of the existing image ABOVE the upload zone:

```blade
{{-- Existing image preview --}}
@if($item?->image_path)
<div class="mb-4 flex items-center gap-4">
  <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}"
       class="w-20 h-20 object-cover industrial-border">
  <div>
    <p class="font-mono text-[10px] uppercase text-on-surface-variant">Current Image</p>
    <p class="font-mono text-[10px] text-on-surface truncate max-w-[200px]">{{ basename($item->image_path) }}</p>
  </div>
</div>
@endif
```

- [ ] **Step 4: Run image upload tests — expect pass**

```bash
& "C:\xampp\php\php.exe" artisan test tests/Feature/ImageUploadTest.php
```

Expected: 4 passed.

- [ ] **Step 5: Create storage symlink (needed for local testing)**

```bash
& "C:\xampp\php\php.exe" artisan storage:link
```

Expected: `INFO  The [public/storage] link has been connected to [storage/app/public].`
If it already exists: `INFO  The [public/storage] link already exists.` — both are fine.

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/Admin/MenuItemController.php resources/views/admin/menu/edit.blade.php tests/Feature/ImageUploadTest.php
git commit -m "feat: menu item image upload with old-image cleanup"
```

---

## Task 2: Rate Limiting on Auth + Checkout Routes

**Files:**
- Modify: `routes/web.php`

- [ ] **Step 1: Write failing rate limit test**

Create `tests/Feature/RateLimitTest.php`:

```php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_is_throttled_after_5_attempts(): void
    {
        // Make 5 failed login attempts
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => 'test@test.com', 'password' => 'wrong']);
        }

        // 6th attempt should be rate-limited
        $response = $this->post('/login', ['email' => 'test@test.com', 'password' => 'wrong']);

        $response->assertStatus(429);
    }

    public function test_registration_is_throttled_after_3_attempts(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $this->post(route('register.post'), [
                'name' => 'User', 'email' => "test{$i}@test.com",
                'password' => 'pass', 'password_confirmation' => 'pass',
            ]);
        }

        $response = $this->post(route('register.post'), [
            'name' => 'User', 'email' => 'test99@test.com',
            'password' => 'pass', 'password_confirmation' => 'pass',
        ]);

        $response->assertStatus(429);
    }
}
```

Run: `& "C:\xampp\php\php.exe" artisan test tests/Feature/RateLimitTest.php` — expect failures (no throttle yet).

- [ ] **Step 2: Add throttle middleware to routes**

Open `routes/web.php`. Make three targeted changes:

**Change 1** — Add throttle to login POST:
```php
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login.post')->middleware('throttle:5,1');
```

**Change 2** — Add throttle to register POST:
```php
Route::post('/register', [App\Http\Controllers\AuthController::class, 'register'])->name('register.post')->middleware('throttle:3,1');
```

**Change 3** — Add throttle to checkout POST (inside the auth middleware group):
```php
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store')->middleware('throttle:10,1');
```

- [ ] **Step 3: Run rate limit tests — expect pass**

```bash
& "C:\xampp\php\php.exe" artisan test tests/Feature/RateLimitTest.php
```

Expected: 2 passed.

- [ ] **Step 4: Commit**

```bash
git add routes/web.php tests/Feature/RateLimitTest.php
git commit -m "feat: rate limit login (5/min), register (3/min), checkout (10/min)"
```

---

## Task 3: Branded Error Pages

**Files:**
- Create: `resources/views/errors/404.blade.php`
- Create: `resources/views/errors/500.blade.php`
- Create: `resources/views/errors/403.blade.php`

- [ ] **Step 1: Create the errors directory and 404 view**

Create `resources/views/errors/404.blade.php`:

```blade
@extends('layouts.app')

@section('content')
<div class="max-w-container mx-auto px-4 lg:px-16 py-24 text-center">
  <div class="mb-8">
    <h1 class="font-serif text-8xl font-black text-outline mb-4">404</h1>
    <h2 class="font-serif text-3xl font-black text-on-surface uppercase mb-4">Page Not Found</h2>
    <p class="font-sans text-sm text-on-surface-variant max-w-md mx-auto mb-8">
      Looks like this page went the way of the dough — it doesn't exist. Try heading back to our menu.
    </p>
  </div>
  <div class="flex gap-4 justify-center flex-wrap">
    <a href="{{ route('home') }}" class="btn-primary">← BACK TO HOME</a>
    <a href="{{ route('menu') }}" class="btn-secondary">VIEW MENU</a>
  </div>
</div>
@endsection
```

- [ ] **Step 2: Create 500 view**

Create `resources/views/errors/500.blade.php`:

```blade
@extends('layouts.app')

@section('content')
<div class="max-w-container mx-auto px-4 lg:px-16 py-24 text-center">
  <div class="mb-8">
    <h1 class="font-serif text-8xl font-black text-primary mb-4">500</h1>
    <h2 class="font-serif text-3xl font-black text-on-surface uppercase mb-4">Something Went Wrong</h2>
    <p class="font-sans text-sm text-on-surface-variant max-w-md mx-auto mb-8">
      Our kitchen had an unexpected problem. We've been notified and are working on it. Please try again in a moment.
    </p>
  </div>
  <a href="{{ route('home') }}" class="btn-primary">← BACK TO HOME</a>
</div>
@endsection
```

- [ ] **Step 3: Create 403 view**

Create `resources/views/errors/403.blade.php`:

```blade
@extends('layouts.app')

@section('content')
<div class="max-w-container mx-auto px-4 lg:px-16 py-24 text-center">
  <div class="mb-8">
    <h1 class="font-serif text-8xl font-black text-outline mb-4">403</h1>
    <h2 class="font-serif text-3xl font-black text-on-surface uppercase mb-4">Access Denied</h2>
    <p class="font-sans text-sm text-on-surface-variant max-w-md mx-auto mb-8">
      You don't have permission to view this page.
    </p>
  </div>
  <a href="{{ route('home') }}" class="btn-primary">← BACK TO HOME</a>
</div>
@endsection
```

- [ ] **Step 4: Verify error pages render**

```bash
& "C:\xampp\php\php.exe" artisan tinker --execute="echo view('errors.404')->render() ? 'OK' : 'FAIL';"
```

Expected: `OK` (view renders without error).

- [ ] **Step 5: Commit**

```bash
git add resources/views/errors/
git commit -m "feat: branded 404, 500, 403 error pages"
```

---

## Task 4: Production Deploy Prep

**Files:**
- Create: `DEPLOY.md`
- Create: `.env.production.example`

- [ ] **Step 1: Create production .env example**

Create `.env.production.example` (safe to commit — no real secrets):

```ini
APP_NAME="Aces & Eights Pizza"
APP_ENV=production
APP_KEY=base64:GENERATE_WITH_ARTISAN_KEY_GENERATE
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_cpanel_db_name
DB_USERNAME=your_cpanel_db_user
DB_PASSWORD=your_cpanel_db_password

BROADCAST_CONNECTION=pusher
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
CACHE_STORE=file

MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=465
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_mail_password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Aces & Eights Pizza"

STRIPE_KEY=pk_live_YOUR_LIVE_KEY
STRIPE_SECRET=sk_live_YOUR_LIVE_KEY
STRIPE_WEBHOOK_SECRET=whsec_YOUR_WEBHOOK_SECRET

PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=eu

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

- [ ] **Step 2: Create DEPLOY.md**

Create `DEPLOY.md`:

```markdown
# Aces & Eights Pizza — cPanel Deployment Guide

## Prerequisites
- cPanel shared hosting with PHP 8.2+
- SSH access (recommended) or cPanel File Manager
- MySQL database created in cPanel
- Domain/subdomain pointing to server

## Step 1: Upload Files

**Option A — Git (SSH, recommended):**
```bash
ssh user@yourserver.com
cd ~/
git clone https://github.com/yourusername/aces-eights.git webapp
cd webapp
```

**Option B — FTP/File Manager:**
Upload all files EXCEPT `node_modules/` and `vendor/` to `~/webapp/`.

## Step 2: Point Domain to Public Folder

In cPanel → Domains → point your domain/subdomain to `~/webapp/public/`

OR create a symlink:
```bash
rm -rf ~/public_html
ln -s ~/webapp/public ~/public_html
```

## Step 3: Install PHP Dependencies

```bash
cd ~/webapp
php artisan --version  # verify PHP 8.2
composer install --no-dev --optimize-autoloader
```

## Step 4: Configure Environment

```bash
cp .env.production.example .env
nano .env  # fill in DB credentials, Stripe keys, Pusher keys
php artisan key:generate
```

## Step 5: Build Frontend Assets (do this LOCALLY before uploading)

```bash
# On your LOCAL machine:
npm run build
git add public/build/
git commit -m "chore: build production assets"
git push
```

Then on server: `git pull`

## Step 6: Database Setup

```bash
php artisan migrate --force
php artisan db:seed --class=AdminUserSeeder
php artisan db:seed --class=CategorySeeder
php artisan db:seed --class=AllergenSeeder
php artisan db:seed --class=ToppingSeeder
php artisan db:seed --class=DeliveryZoneSeeder
php artisan db:seed --class=MenuItemSeeder
php artisan db:seed --class=SettingSeeder
```

**Change the admin password after first login:**
Log in at `/login` with `admin@acesandeights.com` / `admin123`, then go to `/account` to change.

## Step 7: Storage + Optimize

```bash
php artisan storage:link
php artisan optimize
```

## Step 8: Stripe Webhook

In Stripe Dashboard → Webhooks → Add endpoint:
- URL: `https://yourdomain.com/stripe/webhook`
- Events: `checkout.session.completed`

Copy the webhook signing secret to `.env` as `STRIPE_WEBHOOK_SECRET`.

## Step 9: Cron Job (Laravel Scheduler)

In cPanel → Cron Jobs, add:
```
* * * * * cd ~/webapp && php artisan schedule:run >> /dev/null 2>&1
```

## Step 10: SSL Certificate

cPanel → SSL/TLS → Let's Encrypt → Install for your domain.

## Verify Checklist

- [ ] Homepage loads: `https://yourdomain.com`
- [ ] Menu loads with real items
- [ ] Admin login works: `/login` → `admin@acesandeights.com`
- [ ] Place a test order (Stripe test mode)
- [ ] Stripe webhook fires: check Laravel logs
- [ ] Admin settings: update store info
- [ ] Upload a menu item image
- [ ] Register a new customer account
- [ ] Change password works

## Common Issues

**500 error on fresh install:**
- Check `storage/` and `bootstrap/cache/` are writable: `chmod -R 755 storage bootstrap/cache`

**Images not showing:**
- Run `php artisan storage:link` — creates `public/storage` symlink

**APP_DEBUG=true showing on production:**
- Set `APP_DEBUG=false` in `.env`, then `php artisan optimize`

**Queue jobs not processing (Pusher events):**
- `QUEUE_CONNECTION=sync` in `.env` processes jobs immediately (fine for shared hosting)
```

- [ ] **Step 3: Add production optimization to config**

Open `config/app.php` if it exists, or verify `APP_ENV=production` and `APP_DEBUG=false` are set via `.env`. No code change needed — `.env.production.example` handles this.

Verify the app reads `APP_DEBUG` correctly:
```bash
& "C:\xampp\php\php.exe" artisan config:show app | grep debug
```

Expected: shows `debug` config key reads from `APP_DEBUG` env var.

- [ ] **Step 4: Commit**

```bash
git add DEPLOY.md .env.production.example
git commit -m "docs: add cPanel deployment guide and production .env template"
```

---

## Task 5: Full Test Suite + Git Tag v1.0

- [ ] **Step 1: Run full test suite**

```bash
& "C:\xampp\php\php.exe" artisan test
```

Expected: all tests pass (101 from Plan 8 + 4 image + 2 rate limit = 107 total).

Fix any failures before proceeding.

- [ ] **Step 2: Final production build**

```bash
& "C:\xampp\php\php.exe" artisan config:clear
& "C:\xampp\php\php.exe" artisan optimize
npm run build
```

Expected: `✓ built in Xms` — no errors.

- [ ] **Step 3: Final commit + git tag**

```bash
git add .
git commit -m "feat: Plan 9 complete — image upload, rate limiting, error pages, deploy guide"

git tag -a v1.0.0 -m "v1.0.0 — Full stack pizza ordering system, plans 1-9 complete"
```

- [ ] **Step 4: Verify route list is clean**

```bash
& "C:\xampp\php\php.exe" artisan route:list --except-vendor
```

Check for any duplicate or conflicting routes. Expected: clean list with no warnings.

- [ ] **Step 5: Summary of what was built**

Verify these URLs work locally:

```
http://localhost:8000/             → Homepage with real menu data
http://localhost:8000/menu         → Menu page with 71 items, category filters
http://localhost:8000/cart         → Cart with delivery/collection toggle
http://localhost:8000/checkout     → Checkout form (pre-filled from saved address)
http://localhost:8000/booking      → Table booking (TableAgent 3-step flow)
http://localhost:8000/about        → About Us
http://localhost:8000/contact      → Contact form
http://localhost:8000/register     → Registration (rate-limited)
http://localhost:8000/login        → Login (rate-limited)
http://localhost:8000/account      → Order history + addresses + password change
http://localhost:8000/admin        → Admin dashboard (KPI, live orders)
http://localhost:8000/admin/orders → Orders list (real DB)
http://localhost:8000/admin/menu   → Menu CRUD with image upload
http://localhost:8000/admin/allergy→ Allergen management
http://localhost:8000/admin/delivery→ Delivery zones
http://localhost:8000/admin/promotions → Promo code CRUD
http://localhost:8000/admin/settings → Store info (saved to DB)
```
