# Architecture

## Overview

TravelCash is a **modular monolith**. Laravel 12 owns all business logic and is the system
of record. Python/FastAPI is a **sidecar** for AI and heavy data jobs only. Everything runs
on one VPS via Docker Compose.

```
                         ┌─────────────────────────────────────────┐
   Cloudflare ──► Nginx ─┤  PHP-FPM (Laravel 12)                    │
                         │   • web (Blade) + REST API (Sanctum)     │
                         │   • SearchService → ProviderManager      │
                         │   • Cashback / Wallet / Affiliate engines│
                         └───┬───────────┬───────────┬─────────────┘
                             │           │           │
                       MySQL 8        Redis      Meilisearch
                   (source of truth) (cache/      (offer index)
                                      queue/
                                      session)
                             │
                  Nginx /ai ─┴─► FastAPI sidecar (assistant, recommender, analytics)
```

## Request lifecycles

### Search
1. `SearchController` / `SearchApiController` builds an immutable `SearchQuery` DTO.
2. `SearchService` checks Redis (`cacheKey`). On miss it **fans out** to every active
   provider for that category via `ProviderManager::activeFor()`.
3. Each provider's adapter returns `NormalizedOffer[]`; `CashbackCalculator` attaches an
   estimated cashback to each.
4. Results are filtered, sorted (best value / price / cashback / rating), and a **signed**
   click-out URL is attached. The query is logged to `search_logs`.

### Click → Booking → Cashback
1. User clicks an offer → signed `/go/{provider}` route → `AffiliateTracker::registerClick()`
   creates a `booking_click` (uuid sub-id), drops a tracking cookie, and 302s to the
   provider's deep-link.
2. Provider books → sends a **server-to-server postback** to `/api/v1/postback/{network}`.
3. `AffiliateTracker::handlePostback()` HMAC-verifies, creates a `booking` (idempotent on
   `provider + external_ref`) and a **pending** cashback.
4. Confirmation postbacks call `CashbackService::confirm()`; after the hold period the
   scheduler matures it to `withdrawable`. Cancellations reverse it.

## Provider Plugin Architecture

```
config/providers.php           providers (DB)              provider_configurations (DB)
  driver => AdapterClass   +   adapter = "driver"     +    config (ENCRYPTED: keys, urls)
        │                          │                              │
        └──────────► ProviderManager::adapterFor(Provider) ───────┘
                                   │
                     ProviderAdapter (for / supports / search / buildBookUrl)
                                   │
            ┌──────────────────────┼─────────────────────────┐
   GenericRestAdapter        BookingComAdapter          MakeMyTripAdapter
   (config-driven field map)  (bespoke normalize)        (multi-category)
                                   │
                          NormalizedOffer[]  ◄── the ONLY shape the rest of the app sees
```

Key properties:
- **Zero-code onboarding** for providers that fit the generic REST adapter — just a DB row.
- **Instant activation:** `Provider::saved/deleted` busts the active-provider cache.
- **Graceful degradation:** a failing provider returns `[]`, never breaks search.
- **Demo mode:** providers without a `base_url`/keys serve realistic sample offers so the
  funnel works from first install.
- **SSRF guard:** outbound provider calls are restricted to an allow-list in production.

## Money safety (cashback liability)
- Monetary columns are `DECIMAL(15,2)`; never floats in the DB.
- `WalletService` is the single writer of balances, using row locks + idempotency keys.
- `wallets.balance` / `pending_balance` are cached projections; the append-only
  `wallet_transactions` ledger is the truth and is reconcilable.

## Scaling path (no rewrite)
- Each domain (`Services/Providers`, `Services/Cashback`, `Services/Affiliate`, `Services/Search`)
  is self-contained and can be extracted to its own service later.
- Redis queue workers already absorb postbacks and cashback maturation.
- Meilisearch can move to its own node; MySQL can move to managed/replicated — config only.
