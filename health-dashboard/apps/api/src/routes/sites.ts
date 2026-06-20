/**
 * Site API routes.
 * GET /api/sites                    — list all sites
 * GET /api/sites/:id/dashboard      — live dashboard payload
 * GET /api/sites/:id/reports        — report archive
 * GET /api/sites/:id/reports/:month — full monthly report
 * GET /api/sites/:id/incidents      — incident history
 * GET /api/sites/:id/deployments    — deployment log
 * POST /api/sites/:id/snapshot/trigger — force a fresh snapshot
 */

import { Router, Request, Response } from 'express';
import { db } from '../db/client';
import { generateHealthReport } from '../engine/rules';
import { collectSnapshot } from '../jobs/daily-snapshot';

export const sitesRouter = Router();

// ── List all sites ─────────────────────────────────────────────────────────
sitesRouter.get('/', async (_req: Request, res: Response) => {
  try {
    const sites = await db.query(`
      SELECT
        w.id, w.slug, w.client_name, w.domain, w.logo_url, w.active,
        s.health_score, s.overall_status AS traffic_light,
        s.snapshot_date AS last_snapshot,
        s.pagespeed_mobile, s.uptime_pct_30d, s.pending_updates,
        s.backup_status, s.critical_vulns
      FROM websites w
      LEFT JOIN LATERAL (
        SELECT * FROM metrics_snapshots
        WHERE website_id = w.id
        ORDER BY snapshot_date DESC
        LIMIT 1
      ) s ON true
      WHERE w.active = true
      ORDER BY w.client_name
    `);
    res.json({ sites: sites.rows });
  } catch (err) {
    res.status(500).json({ error: 'Failed to fetch sites' });
  }
});

// ── Live dashboard for one site ────────────────────────────────────────────
sitesRouter.get('/:id/dashboard', async (req: Request, res: Response) => {
  try {
    const { id } = req.params;

    const [site, latestSnapshot, prev30Snapshot, recentIncidents, recentDeploys] =
      await Promise.all([
        db.query(`SELECT * FROM websites WHERE id = $1`, [id]),
        db.query(`
          SELECT * FROM metrics_snapshots
          WHERE website_id = $1
          ORDER BY snapshot_date DESC LIMIT 1`, [id]),
        db.query(`
          SELECT * FROM metrics_snapshots
          WHERE website_id = $1
            AND snapshot_date < CURRENT_DATE - 28
          ORDER BY snapshot_date DESC LIMIT 1`, [id]),
        db.query(`
          SELECT * FROM incidents
          WHERE website_id = $1
          ORDER BY started_at DESC LIMIT 5`, [id]),
        db.query(`
          SELECT * FROM deployments
          WHERE website_id = $1
          ORDER BY deployed_at DESC LIMIT 5`, [id]),
        ]);

    if (!site.rows[0]) return res.status(404).json({ error: 'Site not found' });

    const snapshot = latestSnapshot.rows[0];
    const prev     = prev30Snapshot.rows[0];

    if (!snapshot) {
      return res.json({
        site: site.rows[0],
        status: 'NO_DATA',
        message: 'No snapshot data available yet. First collection pending.',
      });
    }

    const tl = snapshot.traffic_lights_json;
    const delta = prev ? snapshot.health_score - prev.health_score : null;

    res.json({
      site: site.rows[0],
      snapshot_date:    snapshot.snapshot_date,
      health_score:     snapshot.health_score,
      health_score_delta: delta,
      overall_status:   snapshot.overall_status ?? deriveOverall(tl),
      traffic_lights:   tl,
      key_metrics: {
        pagespeed_mobile:  snapshot.pagespeed_mobile,
        uptime_pct_30d:    snapshot.uptime_pct_30d,
        pending_updates:   snapshot.pending_updates,
        backup_status:     snapshot.backup_status,
        critical_vulns:    snapshot.critical_vulns,
        blocked_attacks:   snapshot.blocked_attacks_24h,
        deploy_count_7d:   snapshot.deploy_count_7d,
        failed_builds_7d:  snapshot.failed_builds_7d,
      },
      category_scores: {
        performance:  snapshot.performance_score,
        security:     snapshot.security_score,
        reliability:  snapshot.reliability_score,
        updates:      snapshot.updates_score,
        backups:      snapshot.backups_score,
        engineering:  snapshot.engineering_score,
      },
      recent_incidents:   recentIncidents.rows,
      recent_deployments: recentDeploys.rows,
    });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: 'Failed to fetch dashboard' });
  }
});

// ── Report archive ─────────────────────────────────────────────────────────
sitesRouter.get('/:id/reports', async (req: Request, res: Response) => {
  try {
    const { id } = req.params;
    const reports = await db.query(`
      SELECT id, report_month, health_score, health_score_prev, overall_status, generated_at
      FROM monthly_reports
      WHERE website_id = $1
      ORDER BY report_month DESC
    `, [id]);
    res.json({ reports: reports.rows });
  } catch (err) {
    res.status(500).json({ error: 'Failed to fetch reports' });
  }
});

// ── Full monthly report ────────────────────────────────────────────────────
sitesRouter.get('/:id/reports/:month', async (req: Request, res: Response) => {
  try {
    const { id, month } = req.params;

    if (!/^\d{4}-\d{2}$/.test(month)) {
      return res.status(400).json({ error: 'Invalid month format. Use YYYY-MM.' });
    }

    const result = await db.query(`
      SELECT mr.*, w.client_name, w.domain, w.logo_url
      FROM monthly_reports mr
      JOIN websites w ON w.id = mr.website_id
      WHERE mr.website_id = $1 AND mr.report_month = $2
    `, [id, month]);

    if (!result.rows[0]) {
      return res.status(404).json({ error: `Report for ${month} not found` });
    }

    res.json({ report: result.rows[0] });
  } catch (err) {
    res.status(500).json({ error: 'Failed to fetch report' });
  }
});

// ── Incidents ──────────────────────────────────────────────────────────────
sitesRouter.get('/:id/incidents', async (req: Request, res: Response) => {
  try {
    const { id } = req.params;
    const { limit = '20', offset = '0' } = req.query;
    const incidents = await db.query(`
      SELECT * FROM incidents
      WHERE website_id = $1
      ORDER BY started_at DESC
      LIMIT $2 OFFSET $3
    `, [id, parseInt(limit as string), parseInt(offset as string)]);
    res.json({ incidents: incidents.rows });
  } catch (err) {
    res.status(500).json({ error: 'Failed to fetch incidents' });
  }
});

// ── Deployments ────────────────────────────────────────────────────────────
sitesRouter.get('/:id/deployments', async (req: Request, res: Response) => {
  try {
    const { id } = req.params;
    const { limit = '20', offset = '0' } = req.query;
    const deployments = await db.query(`
      SELECT * FROM deployments
      WHERE website_id = $1
      ORDER BY deployed_at DESC
      LIMIT $2 OFFSET $3
    `, [id, parseInt(limit as string), parseInt(offset as string)]);
    res.json({ deployments: deployments.rows });
  } catch (err) {
    res.status(500).json({ error: 'Failed to fetch deployments' });
  }
});

// ── Force snapshot refresh (admin) ────────────────────────────────────────
sitesRouter.post('/:id/snapshot/trigger', async (req: Request, res: Response) => {
  try {
    const { id } = req.params;
    const site = await db.query(`SELECT * FROM websites WHERE id = $1`, [id]);
    if (!site.rows[0]) return res.status(404).json({ error: 'Site not found' });

    collectSnapshot(site.rows[0]).catch(console.error);
    res.json({ message: 'Snapshot collection triggered', site_id: id });
  } catch (err) {
    res.status(500).json({ error: 'Failed to trigger snapshot' });
  }
});

// ── Trend data (30-day sparkline) ─────────────────────────────────────────
sitesRouter.get('/:id/trend', async (req: Request, res: Response) => {
  try {
    const { id } = req.params;
    const trend = await db.query(`
      SELECT
        snapshot_date,
        health_score,
        performance_score,
        security_score,
        reliability_score,
        updates_score,
        backups_score,
        pagespeed_mobile,
        uptime_pct_30d
      FROM metrics_snapshots
      WHERE website_id = $1
        AND snapshot_date >= CURRENT_DATE - INTERVAL '30 days'
      ORDER BY snapshot_date ASC
    `, [id]);
    res.json({ trend: trend.rows });
  } catch (err) {
    res.status(500).json({ error: 'Failed to fetch trend data' });
  }
});

function deriveOverall(tl: Record<string, string>): string {
  const values = Object.values(tl);
  if (values.includes('RED')) return 'RED';
  if (values.includes('AMBER')) return 'AMBER';
  return 'GREEN';
}
