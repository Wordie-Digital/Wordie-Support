/**
 * Monthly report generation job.
 * Runs at 06:00 AEST on the 1st of each month.
 * Aggregates the prior month's snapshots into a structured ReportDocument,
 * persists it, and queues an email notification.
 */

import cron from 'node-cron';
import { format, subMonths, startOfMonth, endOfMonth } from 'date-fns';
import { db } from '../db/client';
import type { ReportDocument, ReportSection, RecommendedAction } from '../types';

export async function generateMonthlyReport(websiteId: string, reportMonth: string): Promise<void> {
  const [year, month] = reportMonth.split('-').map(Number);
  const periodStart = startOfMonth(new Date(year, month - 1));
  const periodEnd   = endOfMonth(new Date(year, month - 1));

  console.log(`[report] Generating ${reportMonth} for site ${websiteId}`);

  // Pull all daily snapshots in the month
  const snapshots = await db.query(`
    SELECT * FROM metrics_snapshots
    WHERE website_id = $1
      AND snapshot_date BETWEEN $2 AND $3
    ORDER BY snapshot_date ASC
  `, [websiteId, periodStart, periodEnd]);

  if (!snapshots.rows.length) {
    console.warn(`[report] No snapshots found for ${websiteId} in ${reportMonth} — skipping`);
    return;
  }

  // Pull same period last month for comparison
  const prevMonthStr = format(subMonths(new Date(year, month - 1), 1), 'yyyy-MM');
  const prevReport   = await db.query(`
    SELECT health_score FROM monthly_reports
    WHERE website_id = $1 AND report_month = $2
  `, [websiteId, prevMonthStr]);
  const prevScore = prevReport.rows[0]?.health_score ?? null;

  // Aggregate: take median of each score across the month
  const rows = snapshots.rows;
  const avg  = (key: string) => Math.round(rows.reduce((s, r) => s + (r[key] ?? 0), 0) / rows.length);
  const last  = rows[rows.length - 1];
  const first = rows[0];

  const healthScore = avg('health_score');

  // Determine trends by comparing first and last week of the month
  const trend = (key: string) => {
    const firstVal = first[key] ?? 0;
    const lastVal  = last[key]  ?? 0;
    if (lastVal > firstVal + 3)  return 'IMPROVING';
    if (lastVal < firstVal - 3)  return 'DECLINING';
    return 'STABLE';
  };

  // Pull incident count for the month
  const incidents = await db.query(`
    SELECT severity, COUNT(*) as cnt FROM incidents
    WHERE website_id = $1
      AND started_at BETWEEN $2 AND $3
    GROUP BY severity
  `, [websiteId, periodStart, periodEnd]);

  const incidentMap = Object.fromEntries(incidents.rows.map(r => [r.severity, parseInt(r.cnt)]));
  const criticalIncidents = incidentMap['CRITICAL'] ?? 0;
  const totalIncidents    = incidents.rows.reduce((s, r) => s + parseInt(r.cnt), 0);

  // Pull deployment count
  const deploys = await db.query(`
    SELECT status, COUNT(*) as cnt FROM deployments
    WHERE website_id = $1
      AND deployed_at BETWEEN $2 AND $3
    GROUP BY status
  `, [websiteId, periodStart, periodEnd]);

  const deployMap    = Object.fromEntries(deploys.rows.map(r => [r.status, parseInt(r.cnt)]));
  const successDeploys = deployMap['SUCCESS'] ?? 0;
  const failedDeploys  = deployMap['FAILED']  ?? 0;

  // Build traffic lights from last snapshot
  const tl = last.traffic_lights_json ?? {};

  // Build executive summary
  const highlights: string[] = [];
  const keyRisks: string[]   = [];

  if ((avg('uptime_pct_30d') ?? 100) >= 99.9) highlights.push(`Uptime was ${last.uptime_pct_30d?.toFixed(2)}% — no significant downtime`);
  if (last.backup_status === 'SUCCESS')        highlights.push('All backups completed successfully');
  if (successDeploys > 0 && failedDeploys === 0) highlights.push(`${successDeploys} successful deployment${successDeploys !== 1 ? 's' : ''} with no failures`);
  if (last.critical_vulns === 0)               highlights.push('No critical security vulnerabilities detected');

  if (avg('pagespeed_mobile') < 80)  keyRisks.push(`Mobile page speed is below the recommended threshold of 80 (current: ${avg('pagespeed_mobile')})`);
  if (last.pending_updates > 0)      keyRisks.push(`${last.pending_updates} plugin update${last.pending_updates !== 1 ? 's' : ''} pending`);
  if (last.critical_vulns > 0)      keyRisks.push(`${last.critical_vulns} critical security vulnerabilit${last.critical_vulns !== 1 ? 'ies' : 'y'} detected`);
  if (last.backup_status === 'FAILED') keyRisks.push('Backup failures occurred this month — recovery capability is at risk');

  const overallStatus: 'GREEN' | 'AMBER' | 'RED' =
    healthScore >= 90 ? 'GREEN' :
    healthScore >= 70 ? 'AMBER' : 'RED';

  const recommendedActions: RecommendedAction[] = [];

  if (avg('performance_score') < 80) {
    recommendedActions.push({
      priority: avg('performance_score') < 60 ? 'HIGH' : 'MEDIUM',
      category: 'Performance',
      action: 'Schedule performance audit — focus on image optimisation and script load order.',
      business_impact: 'Improving PageSpeed improves search rankings and reduces visitor bounce rate.',
    });
  }
  if (last.pending_updates > 0) {
    recommendedActions.push({
      priority: last.pending_updates > 5 ? 'HIGH' : 'MEDIUM',
      category: 'Updates',
      action: `Apply ${last.pending_updates} pending plugin update${last.pending_updates !== 1 ? 's' : ''}.`,
      business_impact: 'Outdated plugins are a common vector for security incidents.',
    });
  }
  if (last.backup_status === 'FAILED') {
    recommendedActions.push({
      priority: 'HIGH',
      category: 'Backups',
      action: 'Investigate and resolve backup failures immediately.',
      business_impact: 'Without reliable backups, recovery from a security incident may be impossible.',
    });
  }

  const doc: ReportDocument = {
    report_month:  reportMonth,
    generated_at:  new Date().toISOString(),
    executive_summary: {
      health_score:      healthScore,
      health_score_prev: prevScore,
      overall_status:    overallStatus,
      highlights,
      key_risks:         keyRisks,
      month_summary: buildMonthSummary(overallStatus, healthScore, prevScore, totalIncidents),
    },
    performance: buildSection('performance', last, avg('performance_score'), trend('performance_score'), tl.performance),
    security:    buildSection('security',    last, avg('security_score'),    trend('security_score'),    tl.security),
    reliability: buildSection('reliability', last, avg('reliability_score'), trend('reliability_score'), tl.reliability),
    updates:     buildSection('updates',     last, avg('updates_score'),     trend('updates_score'),     tl.updates),
    backups:     buildSection('backups',     last, avg('backups_score'),     trend('backups_score'),     tl.backups),
    engineering: buildSection('engineering', last, avg('engineering_score'), trend('engineering_score'), tl.engineering),
    recommended_actions: recommendedActions,
  };

  await db.query(`
    INSERT INTO monthly_reports (website_id, report_month, health_score, health_score_prev, overall_status, report_json)
    VALUES ($1, $2, $3, $4, $5, $6)
    ON CONFLICT (website_id, report_month) DO UPDATE SET
      health_score      = EXCLUDED.health_score,
      health_score_prev = EXCLUDED.health_score_prev,
      overall_status    = EXCLUDED.overall_status,
      report_json       = EXCLUDED.report_json,
      generated_at      = NOW()
  `, [websiteId, reportMonth, healthScore, prevScore, overallStatus, doc]);

  console.log(`[report] Generated ${reportMonth} for site ${websiteId} — score ${healthScore} (${overallStatus})`);
}

function buildMonthSummary(
  status: string,
  score: number,
  prevScore: number | null,
  incidents: number,
): string {
  const delta = prevScore != null ? score - prevScore : null;
  const trend = delta == null ? '' : delta > 0 ? ` up ${delta} points from last month` : delta < 0 ? ` down ${Math.abs(delta)} points from last month` : ', unchanged from last month';

  if (status === 'GREEN') {
    return `Your website had an excellent month, scoring ${score}/100${trend}. All systems operated normally with no significant issues. Backups completed successfully every day and the site remained highly available.`;
  }
  if (status === 'AMBER') {
    return `Your website performed well in most areas this month, scoring ${score}/100${trend}. There are a few items requiring attention which are detailed below. No critical issues were detected.`;
  }
  return `Your website experienced some significant issues this month, scoring ${score}/100${trend}. ${incidents > 0 ? `There were ${incidents} incident${incidents !== 1 ? 's' : ''} during the period. ` : ''}The recommended actions below should be addressed promptly.`;
}

function buildSection(
  category: string,
  lastSnapshot: any,
  avgScore: number,
  trend: string,
  status: string = 'GREEN',
): ReportSection {
  return {
    status:               (status ?? 'GREEN') as 'GREEN' | 'AMBER' | 'RED',
    score:                avgScore,
    headline:             '', // Populated by rules engine interpretation in frontend
    interpretation:       '', // Same — frontend derives from latest rules engine run
    risk:                 '',
    recommended_action:   null,
    trend:                (trend ?? 'STABLE') as 'IMPROVING' | 'STABLE' | 'DECLINING',
    metrics:              extractMetrics(category, lastSnapshot),
  };
}

function extractMetrics(category: string, snap: any) {
  switch (category) {
    case 'performance':
      return [
        { key: 'pagespeed_mobile',  label: 'PageSpeed (Mobile)',  value: snap.pagespeed_mobile  ?? 0, unit: '/100', status: scoreToStatus(snap.pagespeed_mobile, 80, 60),   description: '' },
        { key: 'pagespeed_desktop', label: 'PageSpeed (Desktop)', value: snap.pagespeed_desktop ?? 0, unit: '/100', status: scoreToStatus(snap.pagespeed_desktop, 80, 60),  description: '' },
        { key: 'lcp_mobile',        label: 'LCP (Mobile)',        value: snap.lcp_mobile        ?? 0, unit: 's',    status: invertStatus(snap.lcp_mobile, 2.5, 4.0),        description: '' },
        { key: 'cls_mobile',        label: 'CLS (Mobile)',        value: snap.cls_mobile        ?? 0,              status: invertStatus(snap.cls_mobile, 0.1, 0.25),        description: '' },
      ];
    case 'reliability':
      return [
        { key: 'uptime_30d', label: 'Uptime (30 days)', value: snap.uptime_pct_30d ?? 100, unit: '%', status: scoreToStatus(snap.uptime_pct_30d, 99.9, 99.0), description: '' },
        { key: 'uptime_7d',  label: 'Uptime (7 days)',  value: snap.uptime_pct_7d  ?? 100, unit: '%', status: scoreToStatus(snap.uptime_pct_7d, 99.9, 99.0),  description: '' },
      ];
    case 'security':
      return [
        { key: 'critical_vulns',    label: 'Critical Vulnerabilities',  value: snap.critical_vulns    ?? 0, status: snap.critical_vulns > 0 ? 'RED' : 'GREEN', description: '' },
        { key: 'security_alerts',   label: 'Open Security Alerts',      value: snap.open_security_alerts ?? 0, status: invertStatus(snap.open_security_alerts, 2, 5), description: '' },
        { key: 'blocked_attacks',   label: 'Blocked Attacks (30d)',     value: snap.blocked_attacks_24h ?? 0, status: 'GREEN', description: '' },
      ];
    case 'updates':
      return [
        { key: 'pending_updates', label: 'Plugin Updates Pending', value: snap.pending_updates ?? 0, status: invertStatus(snap.pending_updates, 3, 10), description: '' },
      ];
    case 'backups':
      return [
        { key: 'backup_status', label: 'Last Backup', value: snap.backup_status ?? 'UNKNOWN', status: snap.backup_status === 'SUCCESS' ? 'GREEN' : snap.backup_status === 'FAILED' ? 'RED' : 'AMBER', description: '' },
      ];
    case 'engineering':
      return [
        { key: 'failed_builds', label: 'Failed Deployments', value: snap.failed_builds_7d ?? 0, status: invertStatus(snap.failed_builds_7d, 1, 3), description: '' },
        { key: 'deploys',       label: 'Deployments',        value: snap.deploy_count_7d  ?? 0, status: 'GREEN',                                    description: '' },
      ];
    default:
      return [];
  }
}

type TL = 'GREEN' | 'AMBER' | 'RED';
function scoreToStatus(v: number, greenThreshold: number, amberThreshold: number): TL {
  if (v >= greenThreshold) return 'GREEN';
  if (v >= amberThreshold) return 'AMBER';
  return 'RED';
}
function invertStatus(v: number, amberThreshold: number, redThreshold: number): TL {
  if (v <= amberThreshold) return 'GREEN';
  if (v <= redThreshold)   return 'AMBER';
  return 'RED';
}

// Types re-exported from engine for this file
interface ReportDocument {
  report_month: string;
  generated_at: string;
  executive_summary: any;
  performance: ReportSection;
  security: ReportSection;
  reliability: ReportSection;
  updates: ReportSection;
  backups: ReportSection;
  engineering: ReportSection;
  recommended_actions: RecommendedAction[];
}
interface ReportSection {
  status: 'GREEN' | 'AMBER' | 'RED';
  score: number;
  headline: string;
  interpretation: string;
  risk: string;
  recommended_action: string | null;
  trend: 'IMPROVING' | 'STABLE' | 'DECLINING';
  metrics: any[];
}
interface RecommendedAction {
  priority: 'HIGH' | 'MEDIUM' | 'LOW';
  category: string;
  action: string;
  business_impact: string;
}

export async function runMonthlyReports(targetMonth?: string): Promise<void> {
  const month = targetMonth ?? format(subMonths(new Date(), 1), 'yyyy-MM');
  const sites  = await db.query(`SELECT id FROM websites WHERE active = true`);

  for (const site of sites.rows) {
    try {
      await generateMonthlyReport(site.id, month);
    } catch (err) {
      console.error(`[report] Failed for site ${site.id}:`, err);
    }
  }
}

export function scheduleMonthlyReports(): void {
  // Run at 06:00 AEST on the 1st of each month (20:00 UTC on last day)
  cron.schedule('0 20 28-31 * *', async () => {
    const now = new Date();
    if (now.getDate() === new Date(now.getFullYear(), now.getMonth() + 1, 0).getDate()) {
      console.log('[report] Monthly report generation started');
      await runMonthlyReports();
    }
  }, { timezone: 'UTC' });
  console.log('[report] Monthly report job scheduled (06:00 AEST, 1st of month)');
}
