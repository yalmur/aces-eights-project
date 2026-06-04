# Aces & Eights Pizza — cPanel Deployment Guide

## Prerequisites
- cPanel shared hosting with PHP 8.2+
- SSH access (or cPanel File Manager)
- MySQL database created in cPanel
- Domain/subdomain configured

---

## Step 1: Upload Files

**Via SSH (recommended):**
```bash
ssh user@yourserver.com
cd ~/
git clone https://github.com/yourusername/aces-eights.git webapp
cd webapp
```

**Via FTP:** Upload all files EXCEPT `node_modules/` and `vendor/` to `~/webapp/`.

---

## Step 2: Point Domain to Public Folder

In **cPanel → Domains**, point your domain to `~/webapp/public/`

OR create a symlink via SSH:
```bash
rm -rf ~/public_html
ln -s ~/webapp/public ~/public_html
```

---

## Step 3: Install PHP Dependencies

```bash
cd ~/webapp
php --version     # verify 8.2+
composer install --no-dev --optimize-autoloader
```

---

## Step 4: Configure Environment

```bash
cp .env.production.example .env
nano .env         # fill in DB, Stripe, Pusher credentials
php artisan key:generate
```

---

## Step 5: Build Frontend Assets (run LOCALLY before deploying)

```bash
# On your LOCAL machine:
npm run build
git add public/build/
git commit -m "chore: production assets"
git push
```

Then on server: `git pull`

---

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

**⚠ Change the admin password** immediately after first login:
- URL: `/login`, email: `admin@acesandeights.com`, password: `admin123`
- Go to `/admin/settings` to update store info

---

## Step 7: Storage + Optimize

```bash
php artisan storage:link
php artisan optimize
```

---

## Step 8: Stripe Webhook

1. Stripe Dashboard → Developers → Webhooks → Add endpoint
2. URL: `https://yourdomain.com/stripe/webhook`
3. Events: `checkout.session.completed`
4. Copy the signing secret → paste into `.env` as `STRIPE_WEBHOOK_SECRET`

---

## Step 9: Cron Job

In **cPanel → Cron Jobs**, add:
```
* * * * * cd ~/webapp && php artisan schedule:run >> /dev/null 2>&1
```

---

## Step 10: SSL Certificate

**cPanel → SSL/TLS → Let's Encrypt** → Install for your domain.

---

## Go-Live Checklist

- [ ] Homepage loads over HTTPS
- [ ] Menu shows real items with prices
- [ ] Admin login works (`/login`)
- [ ] Place a test order (use Stripe test card `4242 4242 4242 4242`)
- [ ] Stripe webhook fires (check `/admin/orders` for accepted status)
- [ ] Upload a menu item image
- [ ] Register a new customer account
- [ ] Password change works
- [ ] Admin settings: update store name/address
- [ ] Delivery zones show correct fees
- [ ] Error pages: visit `/nonexistent-page` → branded 404

---

## Common Issues

**500 error after deploy:**
```bash
chmod -R 755 storage bootstrap/cache
php artisan config:clear
php artisan optimize
```

**Images not showing:**
```bash
php artisan storage:link   # creates public/storage symlink
```

**APP_DEBUG errors visible:**
Set `APP_DEBUG=false` in `.env` then `php artisan optimize`.

**Pusher not connecting:**
Verify `PUSHER_APP_KEY` in `.env` matches `VITE_PUSHER_APP_KEY` and rebuild assets locally.
