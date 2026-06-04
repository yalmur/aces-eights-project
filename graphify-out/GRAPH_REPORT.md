# Graph Report - .  (2026-06-04)

## Corpus Check
- 174 files · ~147,898 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 763 nodes · 1049 edges · 123 communities (111 shown, 12 thin omitted)
- Extraction: 95% EXTRACTED · 5% INFERRED · 0% AMBIGUOUS · INFERRED: 55 edges (avg confidence: 0.81)
- Token cost: 0 input · 0 output

## Community Hubs (Navigation)
- [[_COMMUNITY_Customer Account & Addresses|Customer Account & Addresses]]
- [[_COMMUNITY_Composer Installer|Composer Installer]]
- [[_COMMUNITY_Domain Models|Domain Models]]
- [[_COMMUNITY_Database Factories|Database Factories]]
- [[_COMMUNITY_Admin Allergy Management|Admin Allergy Management]]
- [[_COMMUNITY_Admin Promotions|Admin Promotions]]
- [[_COMMUNITY_Alpine.js Cart Frontend|Alpine.js Cart Frontend]]
- [[_COMMUNITY_Design Docs & Plans|Design Docs & Plans]]
- [[_COMMUNITY_Database Seeders|Database Seeders]]
- [[_COMMUNITY_Admin Settings|Admin Settings]]
- [[_COMMUNITY_Admin Delivery Zones|Admin Delivery Zones]]
- [[_COMMUNITY_Order Management & Events|Order Management & Events]]
- [[_COMMUNITY_Frontend Dependencies|Frontend Dependencies]]
- [[_COMMUNITY_Customer Page Tests|Customer Page Tests]]
- [[_COMMUNITY_Route Smoke Tests|Route Smoke Tests]]
- [[_COMMUNITY_Admin Menu Item Tests|Admin Menu Item Tests]]
- [[_COMMUNITY_Checkout Tests|Checkout Tests]]
- [[_COMMUNITY_User Model|User Model]]
- [[_COMMUNITY_Brand Identity|Brand Identity]]
- [[_COMMUNITY_Base Test Cases|Base Test Cases]]
- [[_COMMUNITY_Broadcasting & Pusher Tests|Broadcasting & Pusher Tests]]
- [[_COMMUNITY_Composer Metadata|Composer Metadata]]
- [[_COMMUNITY_Composer Scripts|Composer Scripts]]
- [[_COMMUNITY_Page Controller|Page Controller]]
- [[_COMMUNITY_Image Upload Tests|Image Upload Tests]]
- [[_COMMUNITY_Menu Tests|Menu Tests]]
- [[_COMMUNITY_Customer Address Tests|Customer Address Tests]]
- [[_COMMUNITY_Composer Dev Dependencies|Composer Dev Dependencies]]
- [[_COMMUNITY_Checkout Controller|Checkout Controller]]
- [[_COMMUNITY_Password Change Tests|Password Change Tests]]
- [[_COMMUNITY_Registration Tests|Registration Tests]]
- [[_COMMUNITY_Composer Config & Plugins|Composer Config & Plugins]]
- [[_COMMUNITY_Composer Production Deps|Composer Production Deps]]
- [[_COMMUNITY_Order Controller|Order Controller]]
- [[_COMMUNITY_Admin Auth Middleware|Admin Auth Middleware]]
- [[_COMMUNITY_Rate Limit Tests|Rate Limit Tests]]
- [[_COMMUNITY_App Service Provider|App Service Provider]]
- [[_COMMUNITY_Composer Autoload PSR-4|Composer Autoload PSR-4]]
- [[_COMMUNITY_Admin Frontend Concepts|Admin Frontend Concepts]]
- [[_COMMUNITY_Composer Dev Autoload|Composer Dev Autoload]]
- [[_COMMUNITY_Composer Extras|Composer Extras]]
- [[_COMMUNITY_robots.txt|robots.txt]]

## God Nodes (most connected - your core abstractions)
1. `TestCase` - 36 edges
2. `Controller` - 33 edges
3. `Installer` - 22 edges
4. `Promotion` - 19 edges
5. `Allergen` - 17 edges
6. `RouteSmokeTest` - 17 edges
7. `CustomerPagesTest` - 16 edges
8. `MenuItemTest` - 14 edges
9. `PromotionTest` - 13 edges
10. `Setting` - 12 edges

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

## Communities (123 total, 12 thin omitted)

### Community 0 - "Customer Account & Addresses"
Cohesion: 0.05
Nodes (29): AccountController, AddressController, PasswordController, DashboardController, KitchenController, View, RedirectResponse, Request (+21 more)

### Community 1 - "Composer Installer"
Cohesion: 0.08
Nodes (21): checkParams(), checkPlatform(), displayHelp(), ErrorHandler, getHomeDir(), getIniMessage(), getOptValue(), getPlatformIssues() (+13 more)

### Community 2 - "Domain Models"
Cohesion: 0.07
Nodes (18): BelongsTo, HasMany, BelongsTo, BelongsToMany, HasMany, BelongsTo, HasMany, BelongsTo (+10 more)

### Community 3 - "Database Factories"
Cohesion: 0.07
Nodes (14): static, static, static, static, AllergenFactory, CategoryFactory, DeliveryZoneFactory, MenuItemFactory (+6 more)

### Community 4 - "Admin Allergy Management"
Cohesion: 0.11
Nodes (13): AllergyController, MenuItemController, RedirectResponse, Request, View, MenuItem, RedirectResponse, Request (+5 more)

### Community 5 - "Admin Promotions"
Cohesion: 0.11
Nodes (7): PromotionController, RedirectResponse, Request, View, PromotionTest, Promotion, User

### Community 6 - "Alpine.js Cart Frontend"
Cohesion: 0.09
Nodes (8): addToCart(), clear(), closeDrawer(), deliveryFee(), _persist(), removeItem(), total(), updateQty()

### Community 7 - "Design Docs & Plans"
Cohesion: 0.13
Nodes (24): Brainstorm: Cart & Customisation Design Options, Brainstorm: Kitchen Notes UI, Alpine.js Cart Store ($store.cart), cPanel Shared Hosting Deployment, Customisation Drawer (cart-drawer.blade.php), Database Schema (Plan 4), Menu Item Image Upload, Kitchen Notes Quick-Pick Chips (+16 more)

### Community 8 - "Database Seeders"
Cohesion: 0.11
Nodes (9): Seeder, AdminUserSeeder, AllergenSeeder, CategorySeeder, DatabaseSeeder, DeliveryZoneSeeder, MenuItemSeeder, SettingSeeder (+1 more)

### Community 9 - "Admin Settings"
Cohesion: 0.14
Nodes (7): SettingsController, RedirectResponse, Request, View, SettingsTest, Setting, User

### Community 10 - "Admin Delivery Zones"
Cohesion: 0.18
Nodes (7): DeliveryController, RedirectResponse, Request, View, DeliveryZoneTest, DeliveryZone, User

### Community 11 - "Order Management & Events"
Cohesion: 0.16
Nodes (9): OrderController, RedirectResponse, Request, View, Dispatchable, OrderStatusUpdated, InteractsWithSockets, SerializesModels (+1 more)

### Community 12 - "Frontend Dependencies"
Cohesion: 0.11
Nodes (18): dependencies, alpinejs, devDependencies, autoprefixer, axios, concurrently, laravel-echo, laravel-vite-plugin (+10 more)

### Community 16 - "Checkout Tests"
Cohesion: 0.25
Nodes (3): CheckoutTest, MenuItem, User

### Community 17 - "User Model"
Cohesion: 0.31
Nodes (5): HasMany, Authenticatable, User, Notifiable, UserAddress

### Community 18 - "Brand Identity"
Cohesion: 0.31
Nodes (10): Aces & Eights Pizza Brand Identity, Aces & Eights Brand Name, Black Color Scheme, Blue Accent Color (EST. 2010), Dotted Border Row, Established 2010, Horizontal Divider Line, Pizza Wordmark (+2 more)

### Community 19 - "Base Test Cases"
Cohesion: 0.28
Nodes (4): BaseTestCase, ExampleTest, TestCase, ExampleTest

### Community 21 - "Composer Metadata"
Cohesion: 0.22
Nodes (8): description, keywords, license, minimum-stability, name, prefer-stable, $schema, type

### Community 22 - "Composer Scripts"
Cohesion: 0.22
Nodes (9): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+1 more)

### Community 23 - "Page Controller"
Cohesion: 0.39
Nodes (4): RedirectResponse, Request, View, PageController

### Community 27 - "Composer Dev Dependencies"
Cohesion: 0.25
Nodes (8): require-dev, fakerphp/faker, laravel/pail, laravel/pint, laravel/sail, mockery/mockery, nunomaduro/collision, phpunit/phpunit

### Community 28 - "Checkout Controller"
Cohesion: 0.43
Nodes (4): RedirectResponse, Request, View, CheckoutController

### Community 31 - "Composer Config & Plugins"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 32 - "Composer Production Deps"
Cohesion: 0.29
Nodes (7): require, laravel/framework, laravel/tinker, livewire/livewire, php, pusher/pusher-php-server, stripe/stripe-php

### Community 33 - "Order Controller"
Cohesion: 0.53
Nodes (3): Request, View, OrderController

### Community 34 - "Admin Auth Middleware"
Cohesion: 0.53
Nodes (4): Request, Response, Closure, EnsureUserIsAdmin

### Community 37 - "Composer Autoload PSR-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 55 - "Admin Frontend Concepts"
Cohesion: 0.67
Nodes (3): Admin Auth Infrastructure, Admin Layout with Alpine.js Drawer, Plan 3: Admin Frontend

### Community 56 - "Composer Dev Autoload"
Cohesion: 0.67
Nodes (3): autoload-dev, psr-4, Tests\\

### Community 57 - "Composer Extras"
Cohesion: 0.67
Nodes (3): extra, laravel, dont-discover

## Knowledge Gaps
- **58 isolated node(s):** `$schema`, `name`, `type`, `description`, `keywords` (+53 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **12 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Controller` connect `Customer Account & Addresses` to `Order Controller`, `Admin Allergy Management`, `Admin Promotions`, `Admin Settings`, `Admin Delivery Zones`, `Order Management & Events`, `Page Controller`, `Checkout Controller`?**
  _High betweenness centrality (0.114) - this node is a cross-community bridge._
- **Why does `TestCase` connect `Base Test Cases` to `Rate Limit Tests`, `Admin Allergy Management`, `Admin Promotions`, `Admin Settings`, `Admin Delivery Zones`, `Customer Page Tests`, `Route Smoke Tests`, `Admin Menu Item Tests`, `Checkout Tests`, `Broadcasting & Pusher Tests`, `Image Upload Tests`, `Menu Tests`, `Customer Address Tests`, `Password Change Tests`, `Registration Tests`?**
  _High betweenness centrality (0.066) - this node is a cross-community bridge._
- **Why does `Allergen` connect `Admin Allergy Management` to `Database Seeders`, `Menu Tests`, `Domain Models`, `Admin Menu Item Tests`?**
  _High betweenness centrality (0.050) - this node is a cross-community bridge._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _60 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Customer Account & Addresses` be split into smaller, more focused modules?**
  _Cohesion score 0.05026300409117475 - nodes in this community are weakly interconnected._
- **Should `Composer Installer` be split into smaller, more focused modules?**
  _Cohesion score 0.07597402597402597 - nodes in this community are weakly interconnected._
- **Should `Domain Models` be split into smaller, more focused modules?**
  _Cohesion score 0.06775510204081632 - nodes in this community are weakly interconnected._