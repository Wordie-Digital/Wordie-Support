# Build Plan — Wordie Health Intelligence Dashboard

## Phase 1 — Foundation (Week 1–2)

**Goal:** Working dashboard with real data for one client site.

### Backend
- [ ] Provision PostgreSQL on Railway (or Render)
- [ ] Run `schema.sql` migration
- [ ] Deploy API service (`apps/api`)
- [ ] Set environment variables (WP Engine, GitHub, PageSpeed API keys)
- [ ] Test `POST /api/sites/:id/snapshot/trigger` manually — confirm data flows in
- [ ] Verify `metrics_snapshots` rows are being written correctly

### Frontend
- [ ] Deploy dashboard (`apps/dashboard`) on Vercel
- [ ] Connect `NEXT_PUBLIC_API_URL` to deployed API
- [ ] Replace mock data with live `fetch()` calls in `page.tsx` and `sites/[siteId]/page.tsx`
- [ ] Verify overview page lists the first live client site

### First client onboarding
- [ ] Add first website row to `websites` table
- [ ] Trigger manual snapshot — confirm all 6 category scores are present
- [ ] Share dashboard link with client contact

**Exit criteria:** One live client site with real health data visible in the dashboard.

---

## Phase 2 — Automation (Week 3–4)

**Goal:** Daily snapshots running automatically. Monthly report generated and reviewed.

### Scheduled jobs
- [ ] Enable `scheduleDailySnapshots()` in production — confirm 00:00 AEST run
- [ ] Confirm `deployments` and `security_events` tables are being populated
- [ ] Enable `scheduleMonthlyReports()` — manually trigger for current month to test
- [ ] Review generated `monthly_reports` JSON for accuracy

### Report refinement
- [ ] Review generated report copy with internal team
- [ ] Fine-tune rules engine thresholds for each client category
- [ ] Add per-site `alert_rules` overrides where needed (e.g. lower PageSpeed expectation for a legacy site)

### Incident tracking
- [ ] Wire up WP Engine webhook or polling for downtime events → `incidents` table
- [ ] Test incident appears in dashboard alert feed

**Exit criteria:** Daily snapshots running, first monthly report generated and approved.

---

## Phase 3 — All clients + email delivery (Week 5–6)

**Goal:** All retainer clients onboarded. Monthly report emailed on the 1st.

### Onboarding
- [ ] Add all active retainer clients to `websites` table
- [ ] Run historical backfill (trigger snapshots for last 30 days where possible)
- [ ] Review dashboard data quality for each client

### Email delivery
- [ ] Integrate email service (Resend or SendGrid)
- [ ] Build HTML email template using report data (mjml or React Email)
- [ ] Set `report_email` column on each website row
- [ ] Test end-to-end: report generates → email delivers → client reviews

### Client portal access
- [ ] Add JWT auth to API (`/api/sites/:id/*` endpoints require a token)
- [ ] Generate per-client shareable dashboard links
- [ ] Send first branded email to clients with dashboard link

**Exit criteria:** All clients onboarded. Monthly report emails sent and opened.

---

## Phase 4 — Polish & Handover (Week 7–8)

**Goal:** System is stable, documented, and agency team can operate it independently.

### Quality
- [ ] Add Redis caching for PageSpeed API calls (60-min TTL)
- [ ] Add Redis caching for GitHub API calls (15-min TTL)
- [ ] Add error alerting (Slack webhook or email) for failed snapshots
- [ ] Test print / PDF export on all report pages

### Observability
- [ ] Add `/api/health` monitoring (UptimeRobot or Better Uptime)
- [ ] Alert if daily snapshot job fails for any site
- [ ] Dashboard for agency team showing all clients in one view (already built at `/`)

### Documentation
- [ ] Internal runbook: how to add a new client
- [ ] Internal runbook: how to re-run a failed snapshot
- [ ] Internal runbook: how to manually override a traffic light

**Exit criteria:** System handed over to agency ops. Running without intervention for 2 weeks.

---

## Technology dependencies and API access checklist

| Service | Credential needed | Where to get it |
|---|---|---|
| WP Engine | API username + password | my.wpengine.com → API Access |
| GitHub | Personal access token (read:repo, security_events) | github.com → Settings → Tokens |
| Google PageSpeed | API key | console.cloud.google.com → Credentials |
| PostgreSQL | Connection string | Railway or Render → Database |
| Email (Resend) | API key | resend.com |

---

## Deployment topology (production)

```
Vercel (dashboard)  →  Railway (API + PostgreSQL + Redis)
                                    ↓
                        External APIs (WP Engine, GitHub, PageSpeed)
```

Estimated monthly infrastructure cost: AU$20–40 (Railway starter + Vercel hobby).
