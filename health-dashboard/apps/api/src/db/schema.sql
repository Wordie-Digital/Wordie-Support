-- ============================================================
-- WORDIE Health Intelligence Dashboard — PostgreSQL Schema
-- ============================================================

CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- ────────────────────────────────────────────────────────────
-- Websites (one row per client site)
-- ────────────────────────────────────────────────────────────
CREATE TABLE websites (
  id               UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  slug             TEXT NOT NULL UNIQUE,          -- e.g. "acme-corp"
  client_name      TEXT NOT NULL,
  domain           TEXT NOT NULL,                 -- e.g. "acme.com.au"
  logo_url         TEXT,
  wp_engine_install TEXT,                         -- WP Engine install name
  github_repo      TEXT,                          -- "org/repo"
  pagespeed_url    TEXT,                          -- URL to test (may differ from domain)
  timezone         TEXT DEFAULT 'Australia/Sydney',
  report_email     TEXT[],                        -- recipients for monthly reports
  active           BOOLEAN DEFAULT true,
  created_at       TIMESTAMPTZ DEFAULT NOW(),
  updated_at       TIMESTAMPTZ DEFAULT NOW()
);

-- ────────────────────────────────────────────────────────────
-- Metrics Snapshots (daily collection, append-only)
-- ────────────────────────────────────────────────────────────
CREATE TABLE metrics_snapshots (
  id               UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  website_id       UUID NOT NULL REFERENCES websites(id),
  snapshot_date    DATE NOT NULL,

  -- Raw API responses (JSONB for flexibility)
  performance_raw  JSONB,    -- PageSpeed API full response
  security_raw     JSONB,    -- WAF events, vulnerability scan
  reliability_raw  JSONB,    -- uptime logs, response times
  updates_raw      JSONB,    -- WP core, plugins, themes
  backups_raw      JSONB,    -- backup status from WP Engine
  engineering_raw  JSONB,    -- GitHub activity

  -- Derived / normalised scores
  performance_score     INTEGER CHECK (performance_score BETWEEN 0 AND 100),
  security_score        INTEGER CHECK (security_score BETWEEN 0 AND 100),
  reliability_score     INTEGER CHECK (reliability_score BETWEEN 0 AND 100),
  updates_score         INTEGER CHECK (updates_score BETWEEN 0 AND 100),
  backups_score         INTEGER CHECK (backups_score BETWEEN 0 AND 100),
  engineering_score     INTEGER CHECK (engineering_score BETWEEN 0 AND 100),
  health_score          INTEGER CHECK (health_score BETWEEN 0 AND 100),

  -- Traffic lights per category
  traffic_lights_json   JSONB NOT NULL DEFAULT '{}',
  -- e.g. {"performance":"GREEN","security":"AMBER","reliability":"GREEN",...}

  -- Computed key metrics (denormalised for fast queries)
  pagespeed_mobile      INTEGER,
  pagespeed_desktop     INTEGER,
  lcp_mobile            NUMERIC(5,2),   -- seconds
  inp_mobile            NUMERIC(5,2),
  cls_mobile            NUMERIC(5,3),
  uptime_pct_7d         NUMERIC(6,3),
  uptime_pct_30d        NUMERIC(6,3),
  blocked_attacks_24h   INTEGER DEFAULT 0,
  pending_updates       INTEGER DEFAULT 0,
  critical_vulns        INTEGER DEFAULT 0,
  backup_last_success   TIMESTAMPTZ,
  backup_status         TEXT,           -- 'SUCCESS' | 'FAILED' | 'UNKNOWN'
  deploy_count_7d       INTEGER DEFAULT 0,
  failed_builds_7d      INTEGER DEFAULT 0,
  open_security_alerts  INTEGER DEFAULT 0,

  created_at            TIMESTAMPTZ DEFAULT NOW(),

  UNIQUE (website_id, snapshot_date)
);

CREATE INDEX idx_snapshots_website_date ON metrics_snapshots (website_id, snapshot_date DESC);

-- ────────────────────────────────────────────────────────────
-- Monthly Reports
-- ────────────────────────────────────────────────────────────
CREATE TABLE monthly_reports (
  id               UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  website_id       UUID NOT NULL REFERENCES websites(id),
  report_month     TEXT NOT NULL,   -- "2026-01" (YYYY-MM)

  health_score     INTEGER CHECK (health_score BETWEEN 0 AND 100),
  health_score_prev INTEGER,        -- previous month for delta
  overall_status   TEXT NOT NULL,   -- 'GREEN' | 'AMBER' | 'RED'

  -- Full report content (structured JSON matches ReportDocument type)
  report_json      JSONB NOT NULL,

  -- Pre-rendered HTML for PDF export
  html_snapshot    TEXT,

  -- Sent status
  generated_at     TIMESTAMPTZ DEFAULT NOW(),
  sent_at          TIMESTAMPTZ,
  sent_to          TEXT[],

  UNIQUE (website_id, report_month)
);

CREATE INDEX idx_reports_website_month ON monthly_reports (website_id, report_month DESC);

-- ────────────────────────────────────────────────────────────
-- Incidents
-- ────────────────────────────────────────────────────────────
CREATE TABLE incidents (
  id               UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  website_id       UUID NOT NULL REFERENCES websites(id),
  type             TEXT NOT NULL,   -- 'DOWNTIME' | 'SECURITY' | 'PERFORMANCE' | 'BACKUP_FAILURE'
  severity         TEXT NOT NULL,   -- 'CRITICAL' | 'HIGH' | 'MEDIUM' | 'LOW'
  title            TEXT NOT NULL,
  description      TEXT,
  started_at       TIMESTAMPTZ NOT NULL,
  resolved_at      TIMESTAMPTZ,
  duration_minutes INTEGER GENERATED ALWAYS AS (
    EXTRACT(EPOCH FROM (COALESCE(resolved_at, NOW()) - started_at)) / 60
  ) STORED,
  impact_summary   TEXT,           -- plain English for client report
  root_cause       TEXT,
  resolution       TEXT,
  source           TEXT,           -- 'WPENGINE' | 'GITHUB' | 'PAGESPEED' | 'MANUAL'
  source_id        TEXT,           -- external ID for deduplication
  created_at       TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX idx_incidents_website_date ON incidents (website_id, started_at DESC);
CREATE INDEX idx_incidents_unresolved ON incidents (website_id) WHERE resolved_at IS NULL;

-- ────────────────────────────────────────────────────────────
-- Deployments
-- ────────────────────────────────────────────────────────────
CREATE TABLE deployments (
  id               UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  website_id       UUID NOT NULL REFERENCES websites(id),
  deployed_at      TIMESTAMPTZ NOT NULL,
  environment      TEXT NOT NULL,   -- 'PRODUCTION' | 'STAGING'
  status           TEXT NOT NULL,   -- 'SUCCESS' | 'FAILED' | 'ROLLED_BACK'
  commit_sha       TEXT,
  commit_message   TEXT,
  branch           TEXT,
  pr_number        INTEGER,
  pr_title         TEXT,
  deployed_by      TEXT,
  duration_seconds INTEGER,
  is_hotfix        BOOLEAN DEFAULT false,
  github_run_id    TEXT,
  created_at       TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX idx_deployments_website_date ON deployments (website_id, deployed_at DESC);

-- ────────────────────────────────────────────────────────────
-- Security Events
-- ────────────────────────────────────────────────────────────
CREATE TABLE security_events (
  id               UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  website_id       UUID NOT NULL REFERENCES websites(id),
  event_type       TEXT NOT NULL,   -- 'WAF_BLOCK' | 'DDOS' | 'BRUTE_FORCE' | 'VULN_FOUND' | 'DEPENDABOT'
  severity         TEXT NOT NULL,   -- 'CRITICAL' | 'HIGH' | 'MEDIUM' | 'LOW' | 'INFO'
  title            TEXT NOT NULL,
  description      TEXT,
  source_ip        TEXT,
  blocked          BOOLEAN DEFAULT true,
  cve_id           TEXT,
  affected_plugin  TEXT,
  affected_version TEXT,
  patched_version  TEXT,
  occurred_at      TIMESTAMPTZ NOT NULL,
  source           TEXT,            -- 'WPENGINE_WAF' | 'GITHUB_DEPENDABOT' | 'WORDFENCE'
  source_id        TEXT,
  created_at       TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX idx_security_website_date ON security_events (website_id, occurred_at DESC);
CREATE INDEX idx_security_critical ON security_events (website_id, severity) WHERE severity IN ('CRITICAL', 'HIGH');

-- ────────────────────────────────────────────────────────────
-- Alert Rules (configurable thresholds per site)
-- ────────────────────────────────────────────────────────────
CREATE TABLE alert_rules (
  id               UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  website_id       UUID REFERENCES websites(id),  -- NULL = global default
  category         TEXT NOT NULL,
  metric_key       TEXT NOT NULL,
  operator         TEXT NOT NULL,   -- 'lt' | 'gt' | 'eq' | 'lte' | 'gte'
  amber_threshold  NUMERIC,
  red_threshold    NUMERIC,
  description      TEXT,
  active           BOOLEAN DEFAULT true
);

-- Seed default rules
INSERT INTO alert_rules (website_id, category, metric_key, operator, amber_threshold, red_threshold, description) VALUES
  (NULL, 'performance',  'pagespeed_mobile',    'lt', 80, 60,   'Mobile PageSpeed score'),
  (NULL, 'performance',  'lcp_mobile',          'gt', 2.5, 4.0, 'Largest Contentful Paint (seconds)'),
  (NULL, 'performance',  'cls_mobile',          'gt', 0.1, 0.25,'Cumulative Layout Shift'),
  (NULL, 'performance',  'inp_mobile',          'gt', 200, 500, 'Interaction to Next Paint (ms)'),
  (NULL, 'reliability',  'uptime_pct_7d',       'lt', 99.9, 99.0,'7-day uptime percentage'),
  (NULL, 'reliability',  'uptime_pct_30d',      'lt', 99.9, 99.5,'30-day uptime percentage'),
  (NULL, 'security',     'critical_vulns',      'gt', 0, 0,     'Critical vulnerabilities detected'),
  (NULL, 'security',     'open_security_alerts','gt', 2, 5,     'Open Dependabot / security alerts'),
  (NULL, 'updates',      'pending_updates',     'gt', 3, 10,    'Pending WordPress plugin updates'),
  (NULL, 'backups',      'backup_status',       'eq', NULL, NULL,'Backup failure (string check)'),
  (NULL, 'engineering',  'failed_builds_7d',    'gt', 1, 3,     'Failed CI builds in last 7 days');
