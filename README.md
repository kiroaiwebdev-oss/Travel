# TravelCash — Travel Meta-Search + Cashback Super-Platform

> Compare **flights, hotels, trains, cabs, packages, guides, activities & airport transfers**
> across all top providers, book through affiliate links, and earn a configurable share of
> our commission back as **real cashback**.

Built to run on a **single VPS** at MVP scale (10k users / 1k DAU / 100–300 searches per
minute) while staying modular enough to scale without rewrites.

```
Browser ─► Cloudflare ─► Nginx ─► PHP-FPM (Laravel 12)  ─┬─ MySQL 8   (system of record)
                                                          ├─ Redis     (cache/queue/session)
                                                          ├─ Meilisearch (offer search index)
                                                          └─ HTTP ─► FastAPI (AI + workers)
```

## Tech stack

| Layer | Tech |
|-------|------|
| Frontend | HTML5, Tailwind CSS, Alpine.js, vanilla JS, Lucide icons, Inter / Plus Jakarta Sans |
| Backend | PHP 8.4, Laravel 12 (modular monolith) |
| AI / workers | Python 3.12, FastAPI (sidecar only) |
| Data | MySQL 8, Redis 7, Meilisearch 1.10 |
| Infra | Docker Compose, Nginx, Ubuntu VPS, Cloudflare |

## Repository layout

```
.
├── backend/            # Laravel 12 application (the main backend)
│   ├── app/
│   │   ├── Contracts/ProviderAdapter.php       # provider plugin contract
│   │   ├── DTO/                                # SearchQuery, NormalizedOffer
│   │   ├── Services/
│   │   │   ├── Providers/                       # plugin engine + adapters
│   │   │   ├── Search/SearchService.php         # multi-provider fan-out
│   │   │   ├── Cashback/                         # rules, calculator, lifecycle
│   │   │   ├── Wallet/WalletService.php          # double-entry ledger
│   │   │   ├── Affiliate/AffiliateTracker.php    # clicks + postbacks
│   │   │   └── Referral/ReferralService.php
│   │   ├── Http/Controllers/{Api,Auth,User,Admin}
│   │   └── Models/
│   ├── config/{travelcash,providers}.php        # business + plugin config
│   ├── database/migrations + seeders
│   └── resources/views/                         # Blade (marketing, dashboard, admin)
├── ai-service/         # FastAPI sidecar (assistant, recommender, analytics)
│   └── app/providers/  # OpenAI / Gemini / Groq + fallback manager
├── docker/             # Dockerfiles, Nginx, php.ini
├── docker-compose.yml  # full single-VPS stack
└── docs/               # review, architecture, DB, API, deployment
```

## The 3 systems that define this platform

### 1. Provider Plugin Architecture (`docs/01-ARCHITECTURE.md`)
Every provider (Booking.com, Agoda, Expedia, MakeMyTrip, Uber…) sits behind one
`ProviderAdapter` contract. Adapters translate provider JSON into a single
`NormalizedOffer` DTO. **Adding a provider that reuses an existing driver requires only
a database row** — no code, no redeploy. API keys are stored **encrypted**. New/edited
providers appear in search instantly (cache is auto-busted).

### 2. Cashback + Wallet engine
A double-entry-style ledger (`wallet_transactions`) with idempotency keys. Cashback flows
`pending → confirmed → withdrawable → paid` (or `rejected`), driven by provider postbacks.
Rules support fixed / percentage / provider-based / category-based payouts, resolved by
specificity.

### 3. Affiliate tracking + AI
Signed click-out links (tamper-proof amounts), HMAC-verified server-to-server postbacks,
and a FastAPI AI assistant that is **grounded with live platform offers** and fans out
across **Groq → Gemini → OpenAI** with automatic fallback.

## Quick start (on a VPS / local with internet)

```bash
cp .env.example .env
cp backend/.env.example backend/.env
cp ai-service/.env.example ai-service/.env

docker compose up -d --build

# First-time app setup
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan storage:link
```

Open `http://localhost`.

Demo logins (seeded):
- **Admin:** `admin@travelcash.test` / `password`
- **User:** `user@travelcash.test` / `password`

> Providers ship in **demo mode** (realistic sample offers) so the full
> search → click → cashback funnel works immediately. Add real API keys in
> **Admin → Providers** to switch a provider to live with zero code changes.

## Documentation
- [`docs/00-PRE-BUILD-REVIEW.md`](docs/00-PRE-BUILD-REVIEW.md) — product/architecture/DB/security/cost/perf review
- [`docs/01-ARCHITECTURE.md`](docs/01-ARCHITECTURE.md) — system + provider plugin design
- [`docs/02-DATABASE.md`](docs/02-DATABASE.md) — ER diagram, schema, index strategy
- [`docs/03-API.md`](docs/03-API.md) — REST API reference
- [`docs/05-DEPLOYMENT.md`](docs/05-DEPLOYMENT.md) — single-VPS deploy, SSL, backups, Tailwind build

## Build status & honest scope

This repository is a **production-grade foundation + the three core engines**, fully wired
end to end (search, cashback ledger, affiliate funnel, admin, AI). Because the build
sandbox had no outbound internet, `composer install` / `npm install` were not run here —
the code is written to install and boot on your VPS. Tailwind currently loads via the Play
CDN for zero-build startup; `docs/05-DEPLOYMENT.md` covers compiling it for production.

See the deployment doc for the remaining hardening checklist (MFA enablement, full test
suite, queue supervisor tuning).
