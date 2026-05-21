# Website Health & Maintenance Intelligence Dashboard
## System Architecture

```
╔══════════════════════════════════════════════════════════════════════════════╗
║              WORDIE — WEBSITE HEALTH INTELLIGENCE PLATFORM                   ║
╚══════════════════════════════════════════════════════════════════════════════╝

┌─────────────────────────────────────────────────────────────────────────────┐
│  DATA SOURCES (External APIs)                                                │
│                                                                              │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  ┌────────────────┐  │
│  │  WP Engine   │  │  GitHub API  │  │  PageSpeed   │  │  WordPress     │  │
│  │  REST API    │  │  v3 + GraphQL│  │  Insights v5 │  │  REST API      │  │
│  │              │  │              │  │              │  │                │  │
│  │ • Installs   │  │ • Commits    │  │ • CWV scores │  │ • Plugin list  │  │
│  │ • Backups    │  │ • PRs        │  │ • LCP/INP    │  │ • Themes       │  │
│  │ • Logs       │  │ • Deploys    │  │ • CLS        │  │ • WP version   │  │
│  │ • Uptime     │  │ • Dependabot │  │ • TTFB       │  │ • Users        │  │
│  │ • PHP ver    │  │ • Alerts     │  │ • Mobile +   │  │ • Activity     │  │
│  │ • WAF events │  │ • Failed CI  │  │   Desktop    │  │   log          │  │
│  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘  └───────┬────────┘  │
│         └─────────────────┴──────────────────┴──────────────────┘           │
└──────────────────────────────────┬──────────────────────────────────────────┘
                                   │
                                   ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│  AGGREGATION LAYER (Node.js / TypeScript)                                   │
│                                                                              │
│  ┌──────────────────────────────────────────────────────────────────────┐   │
│  │  Integration Adapters (src/services/)                                │   │
│  │                                                                      │   │
│  │  WPEngineService  │  GitHubService  │  PageSpeedService  │  WPService│   │
│  │  ─────────────────┼─────────────────┼────────────────────┼──────────│   │
│  │  normalise() → UnifiedMetric schema                                  │   │
│  └──────────────────────────────────────────────────────────────────────┘   │
│                                   │                                          │
│  ┌──────────────────────────────────────────────────────────────────────┐   │
│  │  Rules Engine (src/engine/rules.ts)                                  │   │
│  │                                                                      │   │
│  │  evaluatePerformance() → TrafficLight                                │   │
│  │  evaluateSecurity()    → TrafficLight                                │   │
│  │  evaluateReliability() → TrafficLight                                │   │
│  │  evaluateUpdates()     → TrafficLight                                │   │
│  │  evaluateBackups()     → TrafficLight                                │   │
│  │  evaluateEngineering() → TrafficLight                                │   │
│  │  calculateHealthScore() → 0–100                                      │   │
│  └──────────────────────────────────────────────────────────────────────┘   │
│                                   │                                          │
│  ┌──────────────────────────────────────────────────────────────────────┐   │
│  │  Scheduled Jobs (src/jobs/)                                          │   │
│  │                                                                      │   │
│  │  DailySnapshot     → runs 00:00 AEST — collect + store metrics       │   │
│  │  MonthlyReport     → runs 1st of month 06:00 — generate report       │   │
│  │  HourlyUptimePing  → runs :00 — check uptime + fire alerts           │   │
│  └──────────────────────────────────────────────────────────────────────┘   │
└──────────────────────────────────┬──────────────────────────────────────────┘
                                   │
                                   ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│  DATA LAYER (PostgreSQL)                                                     │
│                                                                              │
│  websites          metrics_snapshots      monthly_reports                   │
│  ─────────────     ─────────────────      ───────────────                   │
│  id                id                     id                                │
│  slug              website_id             website_id                        │
│  client_name       snapshot_date          report_month (YYYY-MM)            │
│  domain            performance_raw        health_score                      │
│  wp_engine_id      security_raw           traffic_light                     │
│  github_repo       reliability_raw        sections_json                     │
│  created_at        updates_raw            comparison_delta                  │
│                    backups_raw            generated_at                      │
│  incidents         engineering_raw        pdf_url                           │
│  ─────────         health_score                                              │
│  id                traffic_lights_json    deployments                       │
│  website_id        created_at             ───────────                       │
│  type                                     id                                │
│  severity          security_events        website_id                        │
│  description       ───────────────        deployed_at                       │
│  started_at        id                     environment                       │
│  resolved_at       website_id             status                            │
│  impact_summary    event_type             commit_sha                        │
│                    severity               deployed_by                       │
│                    description            pr_number                         │
│                    blocked_at                                                │
└──────────────────────────────────┬──────────────────────────────────────────┘
                                   │
                                   ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│  API LAYER (Express REST API)                                                │
│                                                                              │
│  GET  /api/sites                      → list all client sites               │
│  GET  /api/sites/:id/dashboard        → live dashboard data                 │
│  GET  /api/sites/:id/reports          → report archive list                 │
│  GET  /api/sites/:id/reports/:month   → full monthly report                 │
│  GET  /api/sites/:id/incidents        → incident history                    │
│  GET  /api/sites/:id/deployments      → deployment log                      │
│  POST /api/sites/:id/snapshot/trigger → force refresh (admin)               │
│  GET  /api/health                     → system health check                 │
└──────────────────────────────────┬──────────────────────────────────────────┘
                                   │
                                   ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│  FRONTEND (Next.js 14 App Router + TypeScript + Tailwind)                   │
│                                                                              │
│  /                         → Agency overview (all client sites)             │
│  /sites/[siteId]           → Live dashboard for one site                    │
│  /sites/[siteId]/reports   → Monthly report archive                         │
│  /sites/[siteId]/reports/[month] → Full report (print-ready)                │
│                                                                              │
│  Components:                                                                 │
│  • ExecutiveSummaryCard    • TrafficLightGrid    • HealthScoreGauge          │
│  • AlertFeed               • TrendIndicator      • RecommendationList        │
│  • MetricDrillDown         • DeploymentTimeline  • ReportPage                │
└─────────────────────────────────────────────────────────────────────────────┘
```

## Technology Decisions

| Layer | Choice | Reason |
|---|---|---|
| Frontend | Next.js 14 (App Router) | SSR for report pages, RSC for performance, excellent print CSS support |
| Styling | Tailwind CSS | Rapid theming with Wordie design tokens |
| Backend | Node.js + Express + TypeScript | Strong API ecosystem, shared types with frontend |
| Database | PostgreSQL | JSONB for flexible raw metric storage, strong query support for trends |
| Job Scheduling | node-cron (or pg-boss for prod) | Reliable cron within the process; pg-boss for distributed |
| Caching | Redis | Cache expensive API calls (PageSpeed, GitHub) — 1hr TTL |
| Hosting | Railway or Render | Zero-DevOps for initial launch; supports PostgreSQL add-ons |
| Auth | NextAuth.js (JWT) | Simple token-based access for client portal links |

## Security Model

- Per-client JWT tokens for dashboard access (shareable client link)
- API keys stored in environment variables only
- Rate limiting on all public endpoints
- CORS locked to dashboard domain
- Read-only GitHub token scope

## Scalability Notes

- Each website is a row in `websites` — add clients without schema changes
- `metrics_snapshots` is append-only for full audit trail
- `monthly_reports` stores the fully-rendered JSON to avoid re-computation
- Redis cache prevents hammering external APIs on dashboard load
