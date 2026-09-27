# ADR 001: Search Engine Indexability & On-Demand Static HTML Snapshots on Shared Hosting

## Status
Accepted

## Date
2026-09-27

## Context
GlowAddis (`glowaddis.com.et`) is deployed on shared cPanel hosting running LiteSpeed Web Server.
Previously, all product cards and product details were rendered client-side by Alpine.js via `<template x-for="...">` templates from an inlined JSON payload on the homepage. There were no dedicated product detail URLs (`/products/{id}` or `/products/{slug}`), and the raw HTML served to crawlers contained inert template tags rather than rendered DOM elements.

As a result:
1. Search engine crawlers (Googlebot, Bingbot) and social link preview bots (TelegramBot, facebookexternalhit, WhatsApp, TwitterBot) inspecting the initial HTML payload saw no product text, pricing, descriptions, or individual open-graph images.
2. Sharing a product link in Telegram or social media only generated a generic site preview instead of a rich card with the product's image, title, price, and description.
3. Shared cPanel/LiteSpeed hosting does not reliably allow long-running persistent Node.js processes, and headless browser binaries (Puppeteer, Playwright, Chromium) fail or cannot be compiled due to missing OS-level shared libraries (`libnss3`, `libatk`, `libX11`, etc.) and tight process memory limits.
4. E-commerce catalog data changes frequently (new products added, prices updated, stock status toggled), meaning static build-time generation alone is insufficient without automatic invalidation and on-demand regeneration.

---

## Options Considered

### Option 1: Headless Browser Pre-rendering (Puppeteer / Chromium / Prerender.io)
- **Concept**: A Node.js worker runs headless Chrome, visits the client-side SPA, waits for DOM rendering, and saves the rendered HTML snapshot.
- **Tradeoffs**:
  - Requires headless Chromium and system dependencies that are rarely installed or permitted on shared cPanel environments.
  - High CPU and RAM spikes (~300MB+ per Chromium process), which easily triggers cPanel CloudLinux LVE memory limits (killing processes with 500/503 errors).
  - External prerender SaaS (Prerender.io) adds monthly recurring costs, external network latency, and third-party dependency.

### Option 2: Full Node.js SSR Process (Next.js / Nuxt / Express SSR)
- **Concept**: Run a long-running Node.js process alongside PHP to execute server-side rendering for every incoming request.
- **Tradeoffs**:
  - LiteSpeed/cPanel requires CloudLinux Node.js selector or passenger, which frequently drops connections, crashes on memory bounds, or requires server root access to maintain daemon supervisors (systemd/PM2).
  - Massive architectural rewrite moving away from the existing Laravel backend.

### Option 3: Native Laravel Blade SSR + On-Demand Disk Snapshots & Progressive Alpine Hydration (Selected)
- **Concept**:
  1. Leverage Laravel's built-in Blade rendering engine to generate 100% complete, semantic HTML on the server.
  2. Implement dedicated SEO-friendly product URLs (`/products/{id}/{slug}`) with full OpenGraph, Twitter Cards, canonical tags, and Schema.org JSON-LD structured data.
  3. Pre-render initial product cards directly in the HTML of the main shop catalog (`/`), giving search engines instant visibility in view-source while letting Alpine.js seamlessly hydrate for interactive client-side filtering, live search, and pagination.
  4. Write rendered product pages to an on-demand static disk cache (`storage/app/snapshots/products/{id}.html`). Serve cached snapshots in microseconds with `X-Snapshot-Cache: HIT`, and automatically invalidate / regenerate snapshots whenever a product is created, edited, imported, or deleted.
- **Tradeoffs**:
  - Zero external Node/Chromium dependencies — runs 100% within standard PHP 8+ and Laravel.
  - Near-zero latency: disk snapshots are read directly without database queries.
  - Full compatibility with LiteSpeed Web Server and cPanel file permissions.
  - Automated invalidation hooked into Eloquent lifecycle events (`saved`, `deleted`), admin controllers, and artisan commands (`php artisan products:generate-snapshots`).

---

## Decision
We chose **Option 3: Native Laravel Blade SSR + On-Demand Disk Snapshots & Progressive Hydration**.

1. **Routing & URLs**:
   - `GET /products/{product}/{slug?}`: Dedicated product detail page with 301 canonical redirect for SEO if the slug is omitted or outdated.
   - `GET /sitemap.xml`: Real-time XML sitemap listing the homepage and all active product URLs for search engines.
2. **Snapshot Caching**:
   - Service: `App\Services\ProductSnapshotService` handles generating, storing, retrieving, and purging static HTML files.
   - Artisan Command: `php artisan products:generate-snapshots` allows pre-warming all snapshots or purging them via `--clear`.
   - Automatic Invalidation: Eloquent model events in `Product::booted()` immediately regenerate or remove snapshots upon creation, update, or deletion.
3. **Progressive Hydration on Homepage**:
   - The homepage contains `#ssr-product-grid` populated by Blade `@foreach` with real HTML links, titles, images, and prices.
   - When Alpine loads in interactive browsers, it seamlessly switches to the dynamic client-side grid for instant filtering, search, and modal actions without breaking the crawler HTML payload.
4. **Rich Social & Search Previews**:
   - Every product page contains OpenGraph (`og:title`, `og:description`, `og:image`, `og:price:amount`, `og:price:currency`), Twitter Card (`summary_large_image`), and Google Schema.org `Product` JSON-LD.

---

## Consequences
- **Positive**:
  - `curl` or "View Source" displays complete semantic HTML content for search engines and social bots.
  - Telegram, Facebook, and WhatsApp generate rich previews with images, titles, and ETB prices.
  - Works out-of-the-box on shared cPanel / LiteSpeed hosting without Node.js daemons or Chromium binaries.
  - Extremely fast response times (< 10ms for cached snapshot hits).
- **Maintenance**:
  - Snapshots are stored in `storage/app/snapshots/products/`. If disk space or cache needs a hard flush, `php artisan products:generate-snapshots --clear` safely wipes them.
