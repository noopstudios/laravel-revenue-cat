# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Laravel package (`noopstudios/laravel-revenue-cat`) providing integration with RevenueCat's subscription management platform for iOS/Android apps. Wraps RevenueCat API V2 with Eloquent models, facades, webhook handling, and a Billable trait.

## Commands

```bash
composer test              # Run Pest test suite
composer test-coverage     # Run tests with coverage report
composer analyse           # Run PHPStan static analysis (with Larastan)
composer format            # Format code with Laravel Pint
```

To run a single test file:
```bash
./vendor/bin/pest tests/RevenueCatTest.php
```

To run a single test by name:
```bash
./vendor/bin/pest --filter="test name here"
```

## Architecture

### Core API Client
`src/RevenueCat.php` — Guzzle-based HTTP client for RevenueCat API V2. All API calls (customers, subscriptions, products, offerings, entitlements) go through this class. Registered as a singleton via the service provider with API key and project ID from config.

### Billable Trait Pattern
`src/Concerns/Billable.php` — Added to any Eloquent model (typically User) to make it billable. Provides `customer()` (morphOne) and `subscriptions()` (morphMany) relations, plus convenience methods for entitlements, offerings, and subscription status checks. This is the primary interface consumers use.

### Polymorphic Models
- `src/Models/Customer.php` — Links a RevenueCat customer to any billable Eloquent model via `billable_type`/`billable_id`. Unique constraint on `revenuecat_id`.
- `src/Models/Subscription.php` — Tracks subscription lifecycle with status enum casting, period timestamps, and helper methods (`active()`, `onTrial()`, `onGracePeriod()`, `cancel()`, `pause()`, etc.).

### Webhook Flow
1. `src/Providers/RouteServiceProvider.php` registers POST route at configurable endpoint (default: `/webhook/revenuecat`)
2. `src/Http/Controllers/WebhookController.php` verifies Bearer token, logs request, dispatches `WebhookReceived` event
3. `src/WebhookSignature.php` handles HMAC-SHA256 verification
4. `src/Http/Middleware/VerifyWebhookSignature.php` can be applied for signature-based verification
5. Users can publish a custom webhook handler via `php artisan revenue-cat:publish-webhook-handler`

### Facades
- `src/Facades/RevenueCat.php` — Main facade resolving to the `RevenueCat` API client singleton
- `src/Facades/LaravelRevenueCat.php` — Alternate facade (placeholder)

### Enums
`src/Enums/SubscriptionStatus.php` — Backed enum with values: ACTIVE, CANCELED, EXPIRED, GRACE_PERIOD, PAUSED, TRIAL. Has conversion methods `fromWebhookEvent()` and `fromAPIV2()` for mapping external status strings.

## Testing

Tests use Pest with Orchestra Testbench. The base `TestCase` uses SQLite in-memory database with `RefreshDatabase`. API tests mock Guzzle responses via `MockHandler`. Architecture tests use the Pest Arch plugin.

## Configuration

All config lives in `config/revenue-cat.php`. Key env vars: `REVENUECAT_API_KEY`, `REVENUECAT_PROJECT_ID`, `REVENUECAT_WEBHOOK_SECRET`. The config covers API settings, webhook options (rate limiting, IP allowlist, custom handler), database table names, caching, logging, and error handling (retry logic).

## Package Registration

`LaravelRevenueCatServiceProvider` (using Spatie's PackageServiceProvider) registers config, migrations (customers/subscriptions tables), views, the artisan command, and binds the `RevenueCat` singleton.
