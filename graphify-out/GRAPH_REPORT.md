# Graph Report - webapp  (2026-06-05)

## Corpus Check
- 277 files · ~142,270 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 948 nodes · 1055 edges · 241 communities (228 shown, 13 thin omitted)
- Extraction: 95% EXTRACTED · 5% INFERRED · 0% AMBIGUOUS · INFERRED: 55 edges (avg confidence: 0.81)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `2b554a49`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- [[_COMMUNITY_Community 0|Community 0]]
- [[_COMMUNITY_Community 1|Community 1]]
- [[_COMMUNITY_Community 2|Community 2]]
- [[_COMMUNITY_Community 3|Community 3]]
- [[_COMMUNITY_Community 4|Community 4]]
- [[_COMMUNITY_Community 5|Community 5]]
- [[_COMMUNITY_Community 6|Community 6]]
- [[_COMMUNITY_Community 7|Community 7]]
- [[_COMMUNITY_Community 8|Community 8]]
- [[_COMMUNITY_Community 9|Community 9]]
- [[_COMMUNITY_Community 10|Community 10]]
- [[_COMMUNITY_Community 11|Community 11]]
- [[_COMMUNITY_Community 12|Community 12]]
- [[_COMMUNITY_Community 13|Community 13]]
- [[_COMMUNITY_Community 14|Community 14]]
- [[_COMMUNITY_Community 15|Community 15]]
- [[_COMMUNITY_Community 16|Community 16]]
- [[_COMMUNITY_Community 17|Community 17]]
- [[_COMMUNITY_Community 18|Community 18]]
- [[_COMMUNITY_Community 19|Community 19]]
- [[_COMMUNITY_Community 20|Community 20]]
- [[_COMMUNITY_Community 21|Community 21]]
- [[_COMMUNITY_Community 22|Community 22]]
- [[_COMMUNITY_Community 23|Community 23]]
- [[_COMMUNITY_Community 24|Community 24]]
- [[_COMMUNITY_Community 25|Community 25]]
- [[_COMMUNITY_Community 26|Community 26]]
- [[_COMMUNITY_Community 27|Community 27]]
- [[_COMMUNITY_Community 28|Community 28]]
- [[_COMMUNITY_Community 29|Community 29]]
- [[_COMMUNITY_Community 30|Community 30]]
- [[_COMMUNITY_Community 31|Community 31]]
- [[_COMMUNITY_Community 32|Community 32]]
- [[_COMMUNITY_Community 33|Community 33]]
- [[_COMMUNITY_Community 34|Community 34]]
- [[_COMMUNITY_Community 35|Community 35]]
- [[_COMMUNITY_Community 36|Community 36]]
- [[_COMMUNITY_Community 37|Community 37]]
- [[_COMMUNITY_Community 55|Community 55]]
- [[_COMMUNITY_Community 56|Community 56]]
- [[_COMMUNITY_Community 57|Community 57]]
- [[_COMMUNITY_Community 122|Community 122]]
- [[_COMMUNITY_Community 123|Community 123]]
- [[_COMMUNITY_Community 124|Community 124]]
- [[_COMMUNITY_Community 125|Community 125]]
- [[_COMMUNITY_Community 126|Community 126]]
- [[_COMMUNITY_Community 127|Community 127]]
- [[_COMMUNITY_Community 128|Community 128]]
- [[_COMMUNITY_Community 129|Community 129]]
- [[_COMMUNITY_Community 130|Community 130]]
- [[_COMMUNITY_Community 131|Community 131]]

## God Nodes (most connected - your core abstractions)
1. `TestCase` - 36 edges
2. `Controller` - 33 edges
3. `Promotion` - 19 edges
4. `Allergen` - 17 edges
5. `RouteSmokeTest` - 17 edges
6. `CustomerPagesTest` - 16 edges
7. `MenuItemTest` - 14 edges
8. `Aces & Eights Pizza — cPanel Deployment Guide` - 14 edges
9. `PromotionTest` - 13 edges
10. `Aces & Eights Pizza — Plan 5: Customer Ordering & Checkout` - 13 edges

## Surprising Connections (you probably didn't know these)
- `Brainstorm: Kitchen Notes UI` --semantically_similar_to--> `Spec: Cart & Customisation Design`  [INFERRED] [semantically similar]
  .superpowers/brainstorm/1893-1780409492/content/kitchen-notes.html → docs/superpowers/specs/2026-06-02-cart-customisation-design.md
- `Brainstorm: Cart & Customisation Design Options` --references--> `Plan 2: Cart & Menu Customisation`  [INFERRED]
  .superpowers/brainstorm/1893-1780409492/content/customiser-cart.html → docs/superpowers/plans/2026-06-02-plan-cart-customisation.md
- `Brainstorm: Kitchen Notes UI` --references--> `Plan 2: Cart & Menu Customisation`  [INFERRED]
  .superpowers/brainstorm/1893-1780409492/content/kitchen-notes.html → docs/superpowers/plans/2026-06-02-plan-cart-customisation.md
- `Plan 2: Cart & Menu Customisation` --references--> `Laravel Framework`  [EXTRACTED]
  docs/superpowers/plans/2026-06-02-plan-cart-customisation.md → README.md
- `Kitchen Notes Quick-Pick Chips` --conceptually_related_to--> `Customisation Drawer (cart-drawer.blade.php)`  [INFERRED]
  .superpowers/brainstorm/1893-1780409492/content/kitchen-notes.html → docs/superpowers/plans/2026-06-02-plan-cart-customisation.md

## Hyperedges (group relationships)
- **Alpine.js Cart Frontend: Store + Drawer + Mini-Cart** — concept_alpine_cart_store, concept_customisation_drawer, concept_mini_cart_dropdown [EXTRACTED 1.00]
- **Full Ordering Pipeline: Cart → Checkout → Stripe → Order Schema** — concept_alpine_cart_store, concept_stripe_checkout, concept_order_schema [INFERRED 0.95]
- **Production Readiness: Rate Limiting + Image Upload + DEPLOY.md** — concept_rate_limiting, concept_image_upload, concept_cpanel_deploy [EXTRACTED 1.00]

## Communities (241 total, 13 thin omitted)

### Community 0 - "Community 0"
Cohesion: 0.06
Nodes (26): AccountController, AddressController, PasswordController, DashboardController, KitchenController, View, RedirectResponse, Request (+18 more)

### Community 1 - "Community 1"
Cohesion: 0.08
Nodes (24): 1. Cart Store (`Alpine.store('cart')`), 2. Customisation Drawer, 3. Header Mini Cart, 4. Cart Page (`/cart`), 5. Files Changed / Created, 6. Out of Scope (Plan 2), Behaviour, Behaviour (+16 more)

### Community 2 - "Community 2"
Cohesion: 0.07
Nodes (18): BelongsTo, HasMany, BelongsTo, BelongsToMany, HasMany, BelongsTo, HasMany, BelongsTo (+10 more)

### Community 3 - "Community 3"
Cohesion: 0.07
Nodes (14): static, static, static, static, AllergenFactory, CategoryFactory, DeliveryZoneFactory, MenuItemFactory (+6 more)

### Community 4 - "Community 4"
Cohesion: 0.11
Nodes (13): AllergyController, MenuItemController, RedirectResponse, Request, View, MenuItem, RedirectResponse, Request (+5 more)

### Community 5 - "Community 5"
Cohesion: 0.09
Nodes (11): PromotionController, RedirectResponse, Request, View, RedirectResponse, Request, View, CheckoutController (+3 more)

### Community 6 - "Community 6"
Cohesion: 0.09
Nodes (8): addToCart(), clear(), closeDrawer(), deliveryFee(), _persist(), removeItem(), total(), updateQty()

### Community 7 - "Community 7"
Cohesion: 0.09
Nodes (25): cPanel Shared Hosting Deployment, Menu Item Image Upload, Orders & OrderItems Schema (Plan 5), Promotions & Settings Key-Value Store, Pusher WebSocket Broadcasting, Rate Limiting (throttle middleware), Stripe Checkout Session Flow, Plan 5: Customer Ordering & Checkout (+17 more)

### Community 8 - "Community 8"
Cohesion: 0.11
Nodes (9): Seeder, AdminUserSeeder, AllergenSeeder, CategorySeeder, DatabaseSeeder, DeliveryZoneSeeder, MenuItemSeeder, SettingSeeder (+1 more)

### Community 9 - "Community 9"
Cohesion: 0.14
Nodes (7): SettingsController, RedirectResponse, Request, View, SettingsTest, Setting, User

### Community 10 - "Community 10"
Cohesion: 0.18
Nodes (7): DeliveryController, RedirectResponse, Request, View, DeliveryZoneTest, DeliveryZone, User

### Community 11 - "Community 11"
Cohesion: 0.16
Nodes (9): OrderController, RedirectResponse, Request, View, Dispatchable, OrderStatusUpdated, InteractsWithSockets, SerializesModels (+1 more)

### Community 12 - "Community 12"
Cohesion: 0.11
Nodes (18): dependencies, alpinejs, devDependencies, autoprefixer, axios, concurrently, laravel-echo, laravel-vite-plugin (+10 more)

### Community 16 - "Community 16"
Cohesion: 0.25
Nodes (3): CheckoutTest, MenuItem, User

### Community 17 - "Community 17"
Cohesion: 0.31
Nodes (5): HasMany, Authenticatable, User, Notifiable, UserAddress

### Community 18 - "Community 18"
Cohesion: 0.31
Nodes (10): Aces & Eights Pizza Brand Identity, Aces & Eights Brand Name, Black Color Scheme, Blue Accent Color (EST. 2010), Dotted Border Row, Established 2010, Horizontal Divider Line, Pizza Wordmark (+2 more)

### Community 21 - "Community 21"
Cohesion: 0.22
Nodes (8): description, keywords, license, minimum-stability, name, prefer-stable, $schema, type

### Community 22 - "Community 22"
Cohesion: 0.22
Nodes (9): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+1 more)

### Community 23 - "Community 23"
Cohesion: 0.39
Nodes (4): RedirectResponse, Request, View, PageController

### Community 27 - "Community 27"
Cohesion: 0.25
Nodes (8): require-dev, fakerphp/faker, laravel/pail, laravel/pint, laravel/sail, mockery/mockery, nunomaduro/collision, phpunit/phpunit

### Community 28 - "Community 28"
Cohesion: 0.15
Nodes (19): Brainstorm: Cart & Customisation Design Options, Brainstorm: Kitchen Notes UI, Alpine.js Cart Store ($store.cart), Customisation Drawer (cart-drawer.blade.php), Database Schema (Plan 4), Kitchen Notes Quick-Pick Chips, Laravel Framework, Mini Cart Dropdown (header) (+11 more)

### Community 31 - "Community 31"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 32 - "Community 32"
Cohesion: 0.29
Nodes (7): require, laravel/framework, laravel/tinker, livewire/livewire, php, pusher/pusher-php-server, stripe/stripe-php

### Community 33 - "Community 33"
Cohesion: 0.53
Nodes (3): Request, View, OrderController

### Community 34 - "Community 34"
Cohesion: 0.53
Nodes (4): Request, Response, Closure, EnsureUserIsAdmin

### Community 35 - "Community 35"
Cohesion: 0.14
Nodes (13): Aces & Eights Pizza — Plan 5: Customer Ordering & Checkout, File Map, Schema additions, Task 10: Full Test Suite + Final Verification, Task 1: Registration Backend, Task 2: Orders + OrderItems Migrations, Task 3: Order + OrderItem Models, Task 4: Stripe SDK + Configuration (+5 more)

### Community 37 - "Community 37"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 55 - "Community 55"
Cohesion: 0.67
Nodes (3): Admin Auth Infrastructure, Admin Layout with Alpine.js Drawer, Plan 3: Admin Frontend

### Community 56 - "Community 56"
Cohesion: 0.67
Nodes (3): autoload-dev, psr-4, Tests\\

### Community 57 - "Community 57"
Cohesion: 0.67
Nodes (3): extra, laravel, dont-discover

### Community 123 - "Community 123"
Cohesion: 0.15
Nodes (12): Aces & Eights Pizza — Plan 3: Admin Frontend, File Map, Task 10: Route Smoke Tests + Final Verification, Task 1: Auth Infrastructure, Task 2: Admin Layout + Tailwind Tokens + CSS, Task 3: Admin Dashboard, Task 4: Admin Orders List, Task 5: Admin Order Detail + In-Store Orders (+4 more)

### Community 124 - "Community 124"
Cohesion: 0.15
Nodes (12): Aces & Eights Pizza — Plan 4: Database Foundation & Menu Backend, File Map, Schema Overview, Task 1: Migrations, Task 2: Eloquent Models, Task 3: Factories + Model Tests, Task 4: Seeders (Real Menu Data), Task 5: Customer MenuController — Wire to DB (+4 more)

### Community 125 - "Community 125"
Cohesion: 0.17
Nodes (11): Aces & Eights Pizza — Plan 6: Real-Time Order Status with Pusher, Activating Real-Time (when Pusher credentials available), File Map, Task 1: Install Packages, Task 2: Broadcasting Configuration, Task 3: OrderStatusUpdated Event, Task 4: Fire Event + Broadcasting Tests, Task 5: Frontend Echo Setup (+3 more)

### Community 126 - "Community 126"
Cohesion: 0.18
Nodes (10): Aces & Eights Pizza — Plan 7: Promotions, Delivery Zones & Settings, File Map, Schema additions, Task 1: Migrations, Task 2: Models + Factory + Seeder, Task 3: Promotions — Feature Tests + Admin CRUD, Task 4: Promo Code at Checkout, Task 5: Delivery Zones Admin CRUD (+2 more)

### Community 127 - "Community 127"
Cohesion: 0.20
Nodes (9): Cart & Menu Customisation Implementation Plan, File Map, Task 1: Alpine.js Cart Store, Task 2: Customisation Drawer Component, Task 3: Wire Drawer into App Layout, Task 4: Update Header with Mini Cart, Task 5: Wire Menu + Buttons, Task 6: Build Cart Page (+1 more)

### Community 128 - "Community 128"
Cohesion: 0.28
Nodes (4): BaseTestCase, ExampleTest, TestCase, ExampleTest

### Community 129 - "Community 129"
Cohesion: 0.22
Nodes (8): Aces & Eights Pizza — Plan 9: Deploy Prep & Final Polish, File Map, On your LOCAL machine:, Task 1: Menu Item Image Upload, Task 2: Rate Limiting on Auth + Checkout Routes, Task 3: Branded Error Pages, Task 4: Production Deploy Prep, Task 5: Full Test Suite + Git Tag v1.0

### Community 130 - "Community 130"
Cohesion: 0.57
Nodes (3): RedirectResponse, Request, AuthController

## Knowledge Gaps
- **164 isolated node(s):** `$schema`, `name`, `type`, `description`, `keywords` (+159 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **13 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Controller` connect `Community 0` to `Community 33`, `Community 130`, `Community 4`, `Community 5`, `Community 9`, `Community 10`, `Community 11`, `Community 23`?**
  _High betweenness centrality (0.074) - this node is a cross-community bridge._
- **Why does `TestCase` connect `Community 128` to `Community 131`, `Community 4`, `Community 5`, `Community 9`, `Community 10`, `Community 13`, `Community 14`, `Community 15`, `Community 16`, `Community 20`, `Community 24`, `Community 25`, `Community 26`, `Community 29`, `Community 30`?**
  _High betweenness centrality (0.043) - this node is a cross-community bridge._
- **Why does `Allergen` connect `Community 4` to `Community 8`, `Community 25`, `Community 2`, `Community 15`?**
  _High betweenness centrality (0.032) - this node is a cross-community bridge._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _166 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Community 0` be split into smaller, more focused modules?**
  _Cohesion score 0.05505279034690799 - nodes in this community are weakly interconnected._
- **Should `Community 1` be split into smaller, more focused modules?**
  _Cohesion score 0.08 - nodes in this community are weakly interconnected._
- **Should `Community 2` be split into smaller, more focused modules?**
  _Cohesion score 0.07092198581560284 - nodes in this community are weakly interconnected._