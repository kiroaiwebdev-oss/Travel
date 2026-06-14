# API Reference

Base URL: `/api/v1`. JSON in/out. Authenticated routes use a **JWT** bearer token
(`Authorization: Bearer <token>`). Rate limits are per-route (shown below).

## Auth

### POST `/auth/register`  · throttle 10/min
```json
{ "name": "Asha", "email": "asha@example.com", "password": "secret123" }
```
→ `201` `{ "access_token", "token_type": "bearer", "expires_in", "user" }`

### POST `/auth/login`  · throttle 10/min
```json
{ "email": "asha@example.com", "password": "secret123" }
```
→ `200` `{ "access_token", ... }`

### GET `/auth/me` · POST `/auth/logout` · POST `/auth/refresh`  *(auth)*

## Search

### GET `/search`  · throttle 120/min
Query params:

| param | type | notes |
|-------|------|-------|
| `category` | required | `flights\|hotels\|trains\|cabs\|packages\|guides\|activities\|transfers` |
| `origin`, `destination` | string | per category |
| `depart_date`, `return_date` | date | |
| `travellers`, `rooms` | int | |
| `sort` | enum | `best_value\|lowest_price\|highest_cashback\|highest_rating` |
| `filters[price_max]`, `filters[rating]`, `filters[max_stops]`, `filters[providers][]` | | |

Response:
```json
{
  "data": [
    {
      "provider_slug": "booking-com", "provider_name": "Booking.com",
      "category": "hotels", "title": "Grand Plaza Goa",
      "price": 5400.00, "cashback": 259.20, "currency": "INR",
      "rating": 4.5, "review_count": 1203,
      "images": ["..."], "amenities": ["Free WiFi","Pool"],
      "go_url": "https://.../go/booking-com?...&signature=...",
      "hash": "…"
    }
  ],
  "meta": { "count": 24, "response_ms": 38, "cache_hit": false, "best_cashback": 612.0 }
}
```

### GET `/categories`
Returns the configured verticals + icons.

## AI assistant

### POST `/ai/assistant`  · throttle 30/min
```json
{ "message": "Best hotel in Goa under ₹5000", "category": "hotels", "destination": "Goa" }
```
Laravel grounds the request with live offers, then proxies to the FastAPI sidecar which
fans out across Groq → Gemini → OpenAI.
```json
{ "message": "…recommendation…", "provider_used": "groq", "suggestions": ["…"] }
```

## Wallet  *(auth)*
- `GET /wallet` — balances projection
- `GET /wallet/transactions` — paginated ledger
- `GET /cashback` — paginated cashback history

## Affiliate postback (server-to-server)

### POST `/postback/{network}`
HMAC-signed (`X-Signature: hex(hmac_sha256(sorted_payload, network.postback_secret))`).
```json
{ "provider":"booking-com", "click_id":"<uuid>", "booking_ref":"BK123",
  "amount":5400, "commission":432, "status":"confirmed", "category":"hotels" }
```
→ `200` `{ "ok": true, "booking": { … } }` · `401` invalid signature · idempotent on `booking_ref`.

## FastAPI sidecar (internal, behind `/ai`)
- `GET /ai/health` — provider configuration + fallback priority
- `POST /ai/assistant` — assistant (called by Laravel)
- `POST /ai/recommend` — cashback-aware ranking of offers

## Web (session) routes
`/` home · `/search` results · `/login` `/register` `/auth/google` · `/dashboard/*`
(wallet, cashback, bookings, saved, referrals, withdrawals, support, profile) ·
`/admin/*` (dashboard, analytics, providers, cashback-rules, users, withdrawals, settings).
