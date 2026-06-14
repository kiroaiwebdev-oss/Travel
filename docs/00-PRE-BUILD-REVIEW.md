# Pre-Build Review — TravelCash Super Platform

> Mandatory review pass executed **before** writing implementation code, as required.
> Each section identifies flaws, risks, and the concrete decision we adopted.

---

## 1. Product Review

**What the platform is:** An affiliate-driven travel meta-search + cashback wallet. Users
search Flights / Hotels / Trains / Cabs / Packages / Guides, click out to affiliate
providers, book there, and receive a configurable slice of our commission back as cashback.

**Flaws / risks identified**

| # | Risk | Decision |
|---|------|----------|
| P1 | "Build everything at once" leads to nothing shipping. | Ship a **vertical MVP**: real search → click tracking → cashback ledger → wallet. Other verticals reuse the same engine. |
| P2 | We don't own inventory; providers can break the funnel. | **Provider Plugin architecture** isolates every provider behind one contract. A broken provider degrades gracefully, never crashes search. |
| P3 | Cashback is a **financial liability**. Bugs = real money lost. | Cashback uses a **double-entry-style ledger** with explicit states (`pending → confirmed → withdrawable / rejected`). Nothing is auto-paid; admin confirms via provider postback. |
| P4 | Affiliate commission confirmation lags weeks. | Cashback stays `pending` until provider postback confirms the booking; only then it becomes `confirmable`. |

**MVP scope (this build):** core engines + 2 reference verticals (Hotels, Flights) fully wired
through the generic adapter, plus the wallet/cashback/affiliate/admin/AI foundations.

---

## 2. Architecture Review

**Decision: Modular monolith on a single VPS.** Laravel 12 is the system of record and owns all
business logic. Python/FastAPI is a **sidecar** for AI + heavy data jobs only (never the main API).

```
Browser ──► Cloudflare ──► Nginx ──► PHP-FPM (Laravel 12)
                                      ├── MySQL 8   (source of truth)
                                      ├── Redis     (cache, queue, sessions, rate-limit)
                                      ├── Meilisearch (search index for cached offers)
                                      └── HTTP ──► FastAPI (AI assistant, recommender, workers)
```

**Flaws / risks identified**

| # | Risk | Decision |
|---|------|----------|
| A1 | Microservices would over-engineer a 0-user MVP. | **Modular monolith**. Each domain (Cashback, Affiliate, Providers, Search) is a self-contained module/namespace so it can be extracted later without rewrite. |
| A2 | Live provider API calls on every search = slow + rate-limited + costly. | **Search-through-cache**: provider results normalized → cached in Redis + indexed in Meilisearch. Live calls happen on a TTL, not per request. |
| A3 | Synchronous affiliate postbacks block web requests. | Postbacks + cashback state transitions run on **Redis queue workers**. |
| A4 | Tight coupling to one provider's response shape. | A `NormalizedOffer` DTO is the only thing the rest of the app sees. Adapters translate provider JSON → DTO. |

---

## 3. Database Review

**Decision: MySQL 8, normalized core + denormalized read tables for search logs/analytics.**

**Flaws / risks identified**

| # | Risk | Decision |
|---|------|----------|
| D1 | Money in floats = rounding errors. | All monetary columns are `DECIMAL(15,2)`; currency stored explicitly. |
| D2 | Wallet balance drift. | Balance is **derived/validated from the transaction ledger**; `wallets.balance` is a cached projection, reconciled by a job. |
| D3 | Provider secrets in plaintext. | `provider_configurations.config` is an **encrypted JSON cast** (Laravel `encrypted:array`). |
| D4 | High-volume tables (clicks, search_logs) bloat. | Indexed on hot columns + partition-ready by month; designed for archival. |
| D5 | Soft business data loss. | Key tables use soft deletes + `audit_logs` for every privileged mutation. |

Index strategy and the full ER overview live in `docs/02-DATABASE.md`.

---

## 4. Security Review (OWASP Top 10 mapped)

| OWASP | Mitigation in this build |
|-------|--------------------------|
| A01 Broken Access Control | RBAC (roles + permissions tables), policy/middleware gates, admin guard. |
| A02 Cryptographic Failures | `APP_KEY` encryption for provider secrets; bcrypt/argon for passwords; HTTPS via Cloudflare + Nginx. |
| A03 Injection | Eloquent/PDO parameter binding everywhere; no raw string SQL. |
| A04 Insecure Design | Cashback ledger states + postback verification + idempotency keys. |
| A05 Security Misconfig | Hardened Nginx headers, separate `.env`, debug off in prod. |
| A06 Vulnerable Components | Pinned composer/pip versions; Dependabot-ready. |
| A07 Auth Failures | Laravel auth, JWT for API, OAuth (Google) ready, MFA-ready columns, login throttling. |
| A08 Data Integrity | Signed affiliate redirect URLs, HMAC-verified postbacks. |
| A09 Logging Failures | `audit_logs` + structured app logs + login/device tracking. |
| A10 SSRF | Provider base URLs allow-listed; outbound HTTP via a guarded client. |

---

## 5. Cost Review (MVP, single VPS)

Target: support 10k users / 1k DAU / 100–300 searches per minute on **one** box.

| Item | Spec | Est. monthly |
|------|------|--------------|
| VPS (4 vCPU / 8 GB / 160 GB SSD) | runs all Docker services | ~$24–40 |
| Cloudflare | Free / Pro | $0–20 |
| Domain | — | ~$1 |
| AI API usage | pay-per-use, fallback to cheapest (Groq first) | usage-based |
| **Total fixed** | | **~$25–60 / mo** |

Decision: Meilisearch over Elasticsearch (10x lighter RAM). Redis does cache + queue + sessions
(no separate broker). No managed DB. This keeps fixed cost under ~$60/mo at MVP.

---

## 6. Performance Review

| Target | Strategy |
|--------|----------|
| Homepage < 2s | Server-rendered Blade, Tailwind, deferred JS, Cloudflare edge cache for static. |
| API < 300ms | Redis response cache, eager loading, indexed queries. |
| Search < 500ms | Serve from Meilisearch/Redis cache; provider fan-out happens async on TTL refresh. |
| Lighthouse 90+ | Lazy images, system/Inter font, no heavy framework, skeleton loaders. |

**Conclusion:** Architecture validated for MVP scale. Approved to implement.
