# Dakia Go-Live Checklist (GoDaddy, No Node Server)

Use this as your working checklist every time you deploy.

## Last Updated

- Date: 2026-04-28
- Updated by: Codex
- Status:
  - Backend (`deployment-godaddy/backend`): uploadable
  - Frontend (`deployment-godaddy/frontend`): uploadable fallback static site
  - Full Next.js admin static export: not fully compatible yet (dynamic routes)
  - Hosting decision: fixed on current GoDaddy shared hosting (no platform change)

## A) Pre-Upload Checklist

- [ ] I have backup of current live files/database
- [ ] I have GoDaddy cPanel access
- [ ] I have DB host/name/user/password ready
- [ ] I confirm GoDaddy plan is shared hosting without Node runtime

## B) Frontend Build Checklist

- [ ] Open PowerShell in `C:\logistics\deployment-godaddy`
- [ ] Run: `powershell -ExecutionPolicy Bypass -File .\prepare-frontend-static.ps1`
- [ ] Confirm files exist in `deployment-godaddy/frontend` (at least `index.html`, `.htaccess`)
- [ ] If script fails, keep fallback frontend files and continue backend go-live

## C) Upload Checklist

- [ ] Upload `deployment-godaddy/backend`
- [ ] Upload `deployment-godaddy/frontend`
- [ ] Place backend under `public_html/api` (or API subdomain root)
- [ ] Place frontend under `public_html`

## D) Backend Setup Checklist (Laravel)

- [ ] Create MySQL database in cPanel
- [ ] Create DB user and grant full privileges
- [ ] Create `.env` using `backend/.env.production.example`
- [ ] Set:
  - [ ] `APP_ENV=production`
  - [ ] `APP_DEBUG=false`
  - [ ] `APP_URL=https://your-api-domain`
  - [ ] Correct DB credentials
- [ ] Ensure `vendor/` is present
- [ ] Set permissions (`755` folders, `644` files)

## E) Verification Checklist

- [ ] Backend test works: `https://your-api-domain/api/logistics/countries`
- [ ] Frontend URL opens on domain
- [ ] Frontend can reach backend API URL
- [ ] HTTPS works on frontend and backend

## F) Pending Work For Tomorrow (Dynamic Route Conversion)

- [ ] Convert dynamic App Router pages to static-compatible approach for GoDaddy
- [ ] Handle all `[id]` routes for static export (`generateStaticParams` or alternative static pages)
- [ ] Handle auth-related dynamic pages under `[lang]` for static export
- [ ] Re-check catch-all/not-found strategy for export compatibility
- [ ] Re-run: `powershell -ExecutionPolicy Bypass -File .\prepare-frontend-static.ps1`
- [ ] Verify generated files in `deployment-godaddy/frontend`
- [ ] Upload refreshed `frontend/` and smoke test all key pages
- [ ] Update this guide status after completion

## G) GoDaddy-Only Production Plan

- [ ] Keep Laravel API on GoDaddy
- [ ] Keep static frontend on GoDaddy
- [ ] Set frontend API base URL to GoDaddy API domain
- [ ] Keep fallback `frontend/index.html` available until full static export is ready

## H) Deployment Log (append every deployment)

- [ ] YYYY-MM-DD - Deployment started
- [ ] YYYY-MM-DD - Backend uploaded
- [ ] YYYY-MM-DD - Frontend uploaded
- [ ] YYYY-MM-DD - API tested
- [ ] YYYY-MM-DD - Go-live confirmed

## Notes

- `next.config.ts` supports static mode using `NEXT_STATIC_EXPORT=true`.
- Redirect warnings during static export are expected for this hosting model.
