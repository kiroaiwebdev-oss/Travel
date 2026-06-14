# Deployment — Single Ubuntu VPS

Target box: 4 vCPU / 8 GB RAM / 160 GB SSD running Docker. All services come up with one
Compose file.

## 1. Prerequisites
```bash
# On a fresh Ubuntu 22.04/24.04 VPS
sudo apt update && sudo apt install -y docker.io docker-compose-plugin git
sudo usermod -aG docker $USER   # re-login after this
```

## 2. Clone & configure
```bash
git clone <your-repo> travelcash && cd travelcash
cp .env.example .env                      # compose-level DB/Meili secrets
cp backend/.env.example backend/.env      # Laravel app config
cp ai-service/.env.example ai-service/.env

# Edit the three .env files: set strong DB passwords, MEILI_MASTER_KEY,
# APP_URL=https://yourdomain.com, and (optionally) AI provider keys + Google OAuth.
```

## 3. Boot the stack
```bash
docker compose up -d --build
docker compose exec app php artisan key:generate
docker compose exec app php artisan jwt:secret        # tymon/jwt-auth
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan storage:link
docker compose exec app php artisan scout:import "App\Models\CachedOffer"   # optional: warm index
```
Services: `app` (php-fpm), `queue`, `scheduler`, `nginx` (:80/:443), `mysql`, `redis`,
`meilisearch`, `ai` (FastAPI).

## 4. TLS / Cloudflare
- **Recommended:** point your domain at Cloudflare, set SSL mode to *Full*, and let
  Cloudflare terminate TLS to Nginx :80. Laravel forces HTTPS URLs in production.
- **Origin TLS (optional):** drop `fullchain.pem` + `privkey.pem` into
  `docker/nginx/certs/` and add a `:443 ssl` server block.

## 5. Production Tailwind build (replaces the Play CDN)
The app ships with Tailwind via the Play CDN for instant startup. For production, compile:
```bash
cd backend
npm install
npm run build          # outputs to public/build (Vite)
```
Then swap `@include('partials.tailwind')` for the compiled stylesheet. The theme tokens in
`partials/tailwind.blade.php` mirror what belongs in `tailwind.config.js`.

## 6. Backups
```bash
# MySQL (cron daily)
docker compose exec -T mysql mysqldump -u root -p"$DB_ROOT_PASSWORD" travelcash | gzip > backup-$(date +%F).sql.gz
# Volumes: mysql_data, redis_data, meili_data are persisted by Compose.
```

## 7. Monitoring & logs
- App logs: `docker compose logs -f app` / Laravel `storage/logs`.
- Health: `GET /up` (Laravel) and `GET /ai/health` (sidecar).
- Queue: `docker compose logs -f queue`; scheduler runs `cashback:mature` hourly + nightly prune.

## 8. Go-live checklist
- [ ] `APP_DEBUG=false`, strong `APP_KEY`, unique DB/Meili/JWT secrets
- [ ] `TC_PROVIDER_ALLOWED_HOSTS` set (SSRF allow-list) once real provider APIs are added
- [ ] Real provider API keys entered in **Admin → Providers** (flips demo → live)
- [ ] Google OAuth credentials + redirect URI
- [ ] Cloudflare WAF + rate limiting in front
- [ ] Off-box backup destination for the daily dump
- [ ] Compile Tailwind for production (step 5)
```
