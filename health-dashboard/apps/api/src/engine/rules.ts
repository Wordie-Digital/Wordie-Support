/**
 * Rules Engine — translates raw metrics into traffic lights, scores,
 * and plain-English business interpretations.
 *
 * Every metric becomes: Status + Business interpretation + Risk + Action.
 */

export type TrafficLight = 'GREEN' | 'AMBER' | 'RED';

export interface CategoryResult {
  status: TrafficLight;
  score: number;           // 0–100
  headline: string;        // 1-line business summary
  interpretation: string;  // plain English, no jargon
  risk: string;            // what this means for the business
  recommended_action: string | null;
  metrics: MetricResult[];
  trend: 'IMPROVING' | 'STABLE' | 'DECLINING';
}

export interface MetricResult {
  key: string;
  label: string;
  value: string | number;
  unit?: string;
  status: TrafficLight;
  description: string;
}

export interface HealthReport {
  health_score: number;
  overall_status: TrafficLight;
  performance: CategoryResult;
  security: CategoryResult;
  reliability: CategoryResult;
  updates: CategoryResult;
  backups: CategoryResult;
  engineering: CategoryResult;
  recommended_actions: RecommendedAction[];
}

export interface RecommendedAction {
  priority: 'HIGH' | 'MEDIUM' | 'LOW';
  category: string;
  action: string;
  business_impact: string;
}

// ──────────────────────────────────────────────────────────────────────────────
// Score weighting (must sum to 1.0)
// ──────────────────────────────────────────────────────────────────────────────
const WEIGHTS = {
  performance:  0.25,
  security:     0.25,
  reliability:  0.20,
  updates:      0.15,
  backups:      0.15,
};

// ──────────────────────────────────────────────────────────────────────────────
// Traffic light banding for the overall health score
// ──────────────────────────────────────────────────────────────────────────────
function scoreToTrafficLight(score: number): TrafficLight {
  if (score >= 90) return 'GREEN';
  if (score >= 70) return 'AMBER';
  return 'RED';
}

function worstStatus(statuses: TrafficLight[]): TrafficLight {
  if (statuses.includes('RED')) return 'RED';
  if (statuses.includes('AMBER')) return 'AMBER';
  return 'GREEN';
}

// ──────────────────────────────────────────────────────────────────────────────
// Performance
// ──────────────────────────────────────────────────────────────────────────────
export interface PerformanceData {
  pagespeed_mobile: number;
  pagespeed_desktop: number;
  lcp_mobile: number;     // seconds
  inp_mobile: number;     // milliseconds
  cls_mobile: number;
  ttfb_ms: number;
  prev_pagespeed_mobile?: number;
}

export function evaluatePerformance(data: PerformanceData): CategoryResult {
  const metrics: MetricResult[] = [];

  // PageSpeed Mobile
  const psStatus: TrafficLight =
    data.pagespeed_mobile >= 80 ? 'GREEN' :
    data.pagespeed_mobile >= 60 ? 'AMBER' : 'RED';
  metrics.push({
    key: 'pagespeed_mobile',
    label: 'PageSpeed (Mobile)',
    value: data.pagespeed_mobile,
    unit: '/100',
    status: psStatus,
    description: psStatus === 'GREEN'
      ? 'Pages load quickly on mobile devices'
      : psStatus === 'AMBER'
      ? 'Mobile load speed needs attention — visitors may experience slow pages'
      : 'Mobile performance is poor — this is likely hurting search rankings and conversions',
  });

  // LCP
  const lcpStatus: TrafficLight =
    data.lcp_mobile <= 2.5 ? 'GREEN' :
    data.lcp_mobile <= 4.0 ? 'AMBER' : 'RED';
  metrics.push({
    key: 'lcp_mobile',
    label: 'Largest Contentful Paint',
    value: data.lcp_mobile.toFixed(2),
    unit: 's',
    status: lcpStatus,
    description: lcpStatus === 'GREEN'
      ? 'Main content appears quickly for visitors'
      : 'Main content takes too long to appear — visitors may leave before the page loads',
  });

  // INP
  const inpStatus: TrafficLight =
    data.inp_mobile <= 200 ? 'GREEN' :
    data.inp_mobile <= 500 ? 'AMBER' : 'RED';
  metrics.push({
    key: 'inp_mobile',
    label: 'Interaction to Next Paint',
    value: data.inp_mobile,
    unit: 'ms',
    status: inpStatus,
    description: inpStatus === 'GREEN'
      ? 'The site responds quickly to clicks and taps'
      : 'The site feels sluggish when visitors interact with it',
  });

  // CLS
  const clsStatus: TrafficLight =
    data.cls_mobile <= 0.1 ? 'GREEN' :
    data.cls_mobile <= 0.25 ? 'AMBER' : 'RED';
  metrics.push({
    key: 'cls_mobile',
    label: 'Layout Stability',
    value: data.cls_mobile.toFixed(3),
    status: clsStatus,
    description: clsStatus === 'GREEN'
      ? 'Page elements stay in place while the page loads'
      : 'Content jumps around while loading — this frustrates visitors and causes accidental clicks',
  });

  const allStatuses = metrics.map(m => m.status);
  const overallStatus = worstStatus(allStatuses);

  // Score: weighted average of individual scores
  const score = Math.round(
    (data.pagespeed_mobile * 0.5) +
    (Math.max(0, 100 - ((data.lcp_mobile / 4.0) * 100)) * 0.2) +
    (Math.max(0, 100 - ((data.inp_mobile / 500) * 100)) * 0.15) +
    (Math.max(0, 100 - (data.cls_mobile / 0.25) * 100) * 0.15)
  );

  const trend: CategoryResult['trend'] =
    data.prev_pagespeed_mobile == null ? 'STABLE' :
    data.pagespeed_mobile > data.prev_pagespeed_mobile + 3 ? 'IMPROVING' :
    data.pagespeed_mobile < data.prev_pagespeed_mobile - 3 ? 'DECLINING' : 'STABLE';

  return {
    status: overallStatus,
    score: Math.max(0, Math.min(100, score)),
    headline: overallStatus === 'GREEN'
      ? 'Site performance is strong'
      : overallStatus === 'AMBER'
      ? 'Performance needs attention'
      : 'Performance issues require urgent action',
    interpretation: buildPerformanceInterpretation(data, overallStatus),
    risk: overallStatus === 'GREEN'
      ? 'No performance risk. Site is competitive in search rankings.'
      : overallStatus === 'AMBER'
      ? 'Moderate risk. Slower load times can reduce search visibility and increase bounce rates.'
      : 'High risk. Poor performance directly impacts traffic, leads, and search ranking.',
    recommended_action: overallStatus === 'GREEN' ? null
      : overallStatus === 'AMBER'
      ? 'Schedule a performance review. Focus on image optimisation and caching configuration.'
      : 'Performance remediation is required this month. This is impacting your business outcomes.',
    metrics,
    trend,
  };
}

function buildPerformanceInterpretation(data: PerformanceData, status: TrafficLight): string {
  if (status === 'GREEN') {
    return `Your website loads in good time on mobile devices, scoring ${data.pagespeed_mobile}/100 for page speed. Core Web Vitals — Google's key performance measures — are all within healthy ranges. Visitors get a smooth experience.`;
  }
  if (status === 'AMBER') {
    return `Your website scores ${data.pagespeed_mobile}/100 on mobile page speed, which is below the recommended threshold of 80. Some performance metrics are outside the ideal range. This may affect how Google ranks your site and how quickly visitors can access your content.`;
  }
  return `Your website's mobile page speed is ${data.pagespeed_mobile}/100, which is below the acceptable minimum of 60. This is likely reducing organic search visibility and causing visitors to leave before the page loads. Immediate attention is recommended.`;
}

// ──────────────────────────────────────────────────────────────────────────────
// Security
// ──────────────────────────────────────────────────────────────────────────────
export interface SecurityData {
  critical_vulns: number;
  high_vulns: number;
  open_dependabot_alerts: number;
  blocked_attacks_30d: number;
  active_ddos: boolean;
  last_security_scan?: Date;
  waf_enabled: boolean;
  ssl_valid: boolean;
  ssl_days_remaining: number;
}

export function evaluateSecurity(data: SecurityData): CategoryResult {
  const metrics: MetricResult[] = [];

  const vulnStatus: TrafficLight =
    data.critical_vulns > 0 ? 'RED' :
    data.high_vulns > 0 ? 'AMBER' : 'GREEN';
  metrics.push({
    key: 'vulnerabilities',
    label: 'Known Vulnerabilities',
    value: data.critical_vulns + data.high_vulns,
    status: vulnStatus,
    description: vulnStatus === 'GREEN'
      ? 'No known vulnerabilities in your plugins or themes'
      : vulnStatus === 'AMBER'
      ? `${data.high_vulns} high-severity vulnerability${data.high_vulns !== 1 ? 'ies' : 'y'} found in site components`
      : `${data.critical_vulns} critical vulnerabilit${data.critical_vulns !== 1 ? 'ies' : 'y'} detected — immediate patching required`,
  });

  const ddosStatus: TrafficLight = data.active_ddos ? 'RED' : 'GREEN';
  metrics.push({
    key: 'active_threat',
    label: 'Active Threats',
    value: data.active_ddos ? 'Active attack' : 'None',
    status: ddosStatus,
    description: data.active_ddos
      ? 'An active attack is underway. Firewall is responding but monitoring is required.'
      : 'No active attacks detected',
  });

  const attackMetricStatus: TrafficLight =
    data.blocked_attacks_30d > 1000 ? 'AMBER' : 'GREEN';
  metrics.push({
    key: 'blocked_attacks',
    label: 'Blocked Attacks (30 days)',
    value: data.blocked_attacks_30d.toLocaleString(),
    status: attackMetricStatus,
    description: `Your firewall blocked ${data.blocked_attacks_30d.toLocaleString()} attacks this month`,
  });

  const sslStatus: TrafficLight =
    !data.ssl_valid ? 'RED' :
    data.ssl_days_remaining < 14 ? 'RED' :
    data.ssl_days_remaining < 30 ? 'AMBER' : 'GREEN';
  metrics.push({
    key: 'ssl',
    label: 'SSL Certificate',
    value: data.ssl_valid ? `Valid (${data.ssl_days_remaining} days)` : 'Invalid',
    status: sslStatus,
    description: sslStatus === 'GREEN'
      ? 'SSL certificate is valid and your site is secure for visitors'
      : sslStatus === 'AMBER'
      ? `SSL certificate expires in ${data.ssl_days_remaining} days — renewal needed soon`
      : !data.ssl_valid ? 'SSL certificate is invalid — visitors will see a security warning'
      : `SSL certificate expires in ${data.ssl_days_remaining} days — urgent renewal required`,
  });

  const alertStatus: TrafficLight =
    data.open_dependabot_alerts > 5 ? 'RED' :
    data.open_dependabot_alerts > 2 ? 'AMBER' : 'GREEN';
  metrics.push({
    key: 'security_alerts',
    label: 'Open Security Alerts',
    value: data.open_dependabot_alerts,
    status: alertStatus,
    description: data.open_dependabot_alerts === 0
      ? 'No open security alerts in your codebase'
      : `${data.open_dependabot_alerts} open security alert${data.open_dependabot_alerts !== 1 ? 's' : ''} in your codebase dependencies`,
  });

  const allStatuses = metrics.map(m => m.status);
  const overallStatus = worstStatus(allStatuses);

  const score =
    (vulnStatus === 'GREEN' ? 40 : vulnStatus === 'AMBER' ? 20 : 0) +
    (ddosStatus === 'GREEN' ? 20 : 0) +
    (sslStatus === 'GREEN' ? 20 : sslStatus === 'AMBER' ? 10 : 0) +
    (alertStatus === 'GREEN' ? 20 : alertStatus === 'AMBER' ? 10 : 0);

  return {
    status: overallStatus,
    score,
    headline: overallStatus === 'GREEN'
      ? 'Security posture is strong'
      : overallStatus === 'AMBER'
      ? 'Security attention required'
      : 'Critical security issues — immediate action needed',
    interpretation: overallStatus === 'GREEN'
      ? `Your website's security is in good shape. The firewall blocked ${data.blocked_attacks_30d.toLocaleString()} attack attempts this month, no vulnerabilities were found in your plugins or themes, and your SSL certificate is valid.`
      : overallStatus === 'AMBER'
      ? `There are security items that need attention this month. While no critical threats are active, ${data.high_vulns} high-severity vulnerabilities were found in site components and ${data.open_dependabot_alerts} security alerts are open.`
      : `Your website has critical security issues that require immediate action. ${data.critical_vulns > 0 ? `${data.critical_vulns} critical vulnerabilities have been detected. ` : ''}${data.active_ddos ? 'An active attack is currently underway. ' : ''}These issues pose real risk to your site and visitors.`,
    risk: overallStatus === 'GREEN'
      ? 'Low security risk. Site is well-protected.'
      : overallStatus === 'AMBER'
      ? 'Moderate risk. Unpatched vulnerabilities could be exploited if not addressed promptly.'
      : 'Critical risk. These issues could result in site compromise, data exposure, or reputational damage.',
    recommended_action: overallStatus === 'GREEN' ? null
      : overallStatus === 'AMBER'
      ? 'Schedule security patches for all flagged plugins this week.'
      : 'Escalate immediately. Security patching and incident response required now.',
    metrics,
    trend: 'STABLE',
  };
}

// ──────────────────────────────────────────────────────────────────────────────
// Reliability / Uptime
// ──────────────────────────────────────────────────────────────────────────────
export interface ReliabilityData {
  uptime_pct_7d: number;
  uptime_pct_30d: number;
  incidents_7d: number;
  incidents_30d: number;
  avg_response_ms: number;
  p95_response_ms: number;
  prev_uptime_pct_30d?: number;
}

export function evaluateReliability(data: ReliabilityData): CategoryResult {
  const metrics: MetricResult[] = [];

  const uptime7Status: TrafficLight =
    data.uptime_pct_7d >= 99.9 ? 'GREEN' :
    data.uptime_pct_7d >= 99.0 ? 'AMBER' : 'RED';
  metrics.push({
    key: 'uptime_7d',
    label: 'Uptime (7 days)',
    value: data.uptime_pct_7d.toFixed(2),
    unit: '%',
    status: uptime7Status,
    description: `${data.uptime_pct_7d.toFixed(2)}% uptime over the last 7 days`,
  });

  const uptime30Status: TrafficLight =
    data.uptime_pct_30d >= 99.9 ? 'GREEN' :
    data.uptime_pct_30d >= 99.5 ? 'AMBER' : 'RED';
  metrics.push({
    key: 'uptime_30d',
    label: 'Uptime (30 days)',
    value: data.uptime_pct_30d.toFixed(2),
    unit: '%',
    status: uptime30Status,
    description: `${data.uptime_pct_30d.toFixed(2)}% uptime over the last 30 days`,
  });

  const incidentStatus: TrafficLight =
    data.incidents_7d === 0 ? 'GREEN' :
    data.incidents_7d <= 1 ? 'AMBER' : 'RED';
  metrics.push({
    key: 'incidents_7d',
    label: 'Incidents (7 days)',
    value: data.incidents_7d,
    status: incidentStatus,
    description: data.incidents_7d === 0
      ? 'No incidents in the last 7 days'
      : `${data.incidents_7d} incident${data.incidents_7d !== 1 ? 's' : ''} in the last 7 days`,
  });

  const responseStatus: TrafficLight =
    data.avg_response_ms < 300 ? 'GREEN' :
    data.avg_response_ms < 600 ? 'AMBER' : 'RED';
  metrics.push({
    key: 'response_time',
    label: 'Avg Response Time',
    value: data.avg_response_ms,
    unit: 'ms',
    status: responseStatus,
    description: `Server responds in ${data.avg_response_ms}ms on average`,
  });

  const allStatuses = metrics.map(m => m.status);
  const overallStatus = worstStatus(allStatuses);

  const score =
    (uptime30Status === 'GREEN' ? 50 : uptime30Status === 'AMBER' ? 30 : 0) +
    (incidentStatus === 'GREEN' ? 30 : incidentStatus === 'AMBER' ? 15 : 0) +
    (responseStatus === 'GREEN' ? 20 : responseStatus === 'AMBER' ? 10 : 0);

  const trend: CategoryResult['trend'] =
    data.prev_uptime_pct_30d == null ? 'STABLE' :
    data.uptime_pct_30d > data.prev_uptime_pct_30d + 0.05 ? 'IMPROVING' :
    data.uptime_pct_30d < data.prev_uptime_pct_30d - 0.05 ? 'DECLINING' : 'STABLE';

  return {
    status: overallStatus,
    score,
    headline: overallStatus === 'GREEN'
      ? 'Site is stable and available'
      : overallStatus === 'AMBER'
      ? 'Some availability issues this month'
      : 'Reliability problems need urgent attention',
    interpretation: overallStatus === 'GREEN'
      ? `Your website was available ${data.uptime_pct_30d.toFixed(2)}% of the time this month with no significant incidents. Visitors could access your site reliably at any time.`
      : `Your website experienced ${data.incidents_30d} incident${data.incidents_30d !== 1 ? 's' : ''} this month, with ${data.uptime_pct_30d.toFixed(2)}% uptime. Some visitors may have been unable to access your site during these periods.`,
    risk: overallStatus === 'GREEN'
      ? 'No reliability risk.'
      : overallStatus === 'AMBER'
      ? 'Moderate risk. Downtime events cost you visitors and potential leads.'
      : 'High risk. Significant downtime directly impacts revenue and brand trust.',
    recommended_action: overallStatus === 'GREEN' ? null
      : 'Review incident log and identify root causes. Consider uptime monitoring alerts.',
    metrics,
    trend,
  };
}

// ──────────────────────────────────────────────────────────────────────────────
// Updates & Technical Health
// ──────────────────────────────────────────────────────────────────────────────
export interface UpdatesData {
  wp_core_current: boolean;
  pending_plugin_updates: number;
  critical_plugin_updates: number;
  pending_theme_updates: number;
  php_version: string;
  php_eol: boolean;
  php_security_only: boolean;
}

export function evaluateUpdates(data: UpdatesData): CategoryResult {
  const metrics: MetricResult[] = [];

  const wpStatus: TrafficLight = data.wp_core_current ? 'GREEN' : 'RED';
  metrics.push({
    key: 'wp_core',
    label: 'WordPress Core',
    value: data.wp_core_current ? 'Up to date' : 'Update available',
    status: wpStatus,
    description: data.wp_core_current
      ? 'WordPress core is running the latest version'
      : 'WordPress core has an available update — should be applied to maintain security',
  });

  const pluginStatus: TrafficLight =
    data.critical_plugin_updates > 0 ? 'RED' :
    data.pending_plugin_updates > 5 ? 'AMBER' :
    data.pending_plugin_updates > 0 ? 'AMBER' : 'GREEN';
  metrics.push({
    key: 'plugins',
    label: 'Plugin Updates',
    value: data.pending_plugin_updates,
    status: pluginStatus,
    description: data.pending_plugin_updates === 0
      ? 'All plugins are up to date'
      : `${data.pending_plugin_updates} plugin update${data.pending_plugin_updates !== 1 ? 's' : ''} pending${data.critical_plugin_updates > 0 ? ` (${data.critical_plugin_updates} critical)` : ''}`,
  });

  const phpStatus: TrafficLight =
    data.php_eol ? 'RED' :
    data.php_security_only ? 'AMBER' : 'GREEN';
  metrics.push({
    key: 'php_version',
    label: 'PHP Version',
    value: data.php_version,
    status: phpStatus,
    description: phpStatus === 'GREEN'
      ? `PHP ${data.php_version} is actively supported`
      : phpStatus === 'AMBER'
      ? `PHP ${data.php_version} is in security-only support — plan an upgrade`
      : `PHP ${data.php_version} is end-of-life and no longer receives security updates`,
  });

  const allStatuses = metrics.map(m => m.status);
  const overallStatus = worstStatus(allStatuses);

  const score =
    (wpStatus === 'GREEN' ? 35 : 0) +
    (pluginStatus === 'GREEN' ? 40 : pluginStatus === 'AMBER' ? 20 : 0) +
    (phpStatus === 'GREEN' ? 25 : phpStatus === 'AMBER' ? 12 : 0);

  return {
    status: overallStatus,
    score,
    headline: overallStatus === 'GREEN'
      ? 'All software is up to date'
      : overallStatus === 'AMBER'
      ? 'Some updates are overdue'
      : 'Critical updates required immediately',
    interpretation: overallStatus === 'GREEN'
      ? 'WordPress core, all plugins, and the PHP version are current. Your site is running on well-maintained software.'
      : `There are ${data.pending_plugin_updates} pending plugin updates and ${!data.wp_core_current ? 'a WordPress core update ' : ''}that need to be applied. Outdated software is a common entry point for security incidents.`,
    risk: overallStatus === 'GREEN'
      ? 'No technical debt risk from outdated software.'
      : overallStatus === 'AMBER'
      ? 'Moderate risk. Outdated plugins can contain known vulnerabilities.'
      : 'High risk. End-of-life software and critical updates left unpatched significantly increase security exposure.',
    recommended_action: overallStatus === 'GREEN' ? null
      : overallStatus === 'AMBER'
      ? `Apply ${data.pending_plugin_updates} pending plugin update${data.pending_plugin_updates !== 1 ? 's' : ''} during next maintenance window.`
      : 'Apply all critical updates immediately. Coordinate with your team to minimise risk.',
    metrics,
    trend: 'STABLE',
  };
}

// ──────────────────────────────────────────────────────────────────────────────
// Backups
// ──────────────────────────────────────────────────────────────────────────────
export interface BackupsData {
  last_successful_backup: Date | null;
  backup_frequency: 'DAILY' | 'WEEKLY' | 'MONTHLY' | 'NONE';
  last_backup_status: 'SUCCESS' | 'FAILED' | 'UNKNOWN';
  consecutive_failures: number;
  offsite_copy: boolean;
  restore_tested: boolean;
  restore_tested_at?: Date;
}

export function evaluateBackups(data: BackupsData): CategoryResult {
  const now = new Date();
  const hoursSinceBackup = data.last_successful_backup
    ? (now.getTime() - data.last_successful_backup.getTime()) / (1000 * 60 * 60)
    : Infinity;

  const metrics: MetricResult[] = [];

  const statusMetric: TrafficLight =
    data.last_backup_status === 'FAILED' || data.consecutive_failures > 0 ? 'RED' :
    data.last_backup_status === 'UNKNOWN' ? 'AMBER' : 'GREEN';
  metrics.push({
    key: 'backup_status',
    label: 'Last Backup Status',
    value: data.last_backup_status,
    status: statusMetric,
    description: statusMetric === 'GREEN'
      ? 'Most recent backup completed successfully'
      : statusMetric === 'AMBER'
      ? 'Backup status is unknown — needs investigation'
      : `Last backup failed${data.consecutive_failures > 1 ? ` (${data.consecutive_failures} consecutive failures)` : ''}`,
  });

  const freshnessStatus: TrafficLight =
    hoursSinceBackup <= 26 ? 'GREEN' :
    hoursSinceBackup <= 72 ? 'AMBER' : 'RED';
  metrics.push({
    key: 'backup_age',
    label: 'Backup Age',
    value: data.last_successful_backup
      ? `${Math.round(hoursSinceBackup)}h ago`
      : 'Never',
    status: freshnessStatus,
    description: freshnessStatus === 'GREEN'
      ? 'A fresh backup is available — site can be fully restored quickly'
      : `Last successful backup was ${Math.round(hoursSinceBackup)} hours ago`,
  });

  const allStatuses = metrics.map(m => m.status);
  const overallStatus = worstStatus(allStatuses);

  const score =
    (statusMetric === 'GREEN' ? 60 : statusMetric === 'AMBER' ? 30 : 0) +
    (freshnessStatus === 'GREEN' ? 40 : freshnessStatus === 'AMBER' ? 20 : 0);

  return {
    status: overallStatus,
    score,
    headline: overallStatus === 'GREEN'
      ? 'Backups are healthy and current'
      : overallStatus === 'AMBER'
      ? 'Backup situation needs review'
      : 'Backup failure — recovery capability at risk',
    interpretation: overallStatus === 'GREEN'
      ? `Your site is being backed up ${data.backup_frequency.toLowerCase()} and the most recent backup completed successfully. In the event of any incident, your site can be restored quickly.`
      : overallStatus === 'AMBER'
      ? 'Backup health needs attention. Without a reliable backup, recovering from a security incident or data loss would be significantly harder.'
      : `Backups have failed ${data.consecutive_failures} time${data.consecutive_failures !== 1 ? 's' : ''} in a row. If something goes wrong with your site right now, recovery would be difficult or impossible.`,
    risk: overallStatus === 'GREEN'
      ? 'Low risk. Site can be recovered quickly from any incident.'
      : overallStatus === 'AMBER'
      ? 'Moderate risk. An older backup means more data and work could be lost in a recovery scenario.'
      : 'Critical risk. A compromised or hacked site without a current backup may be unrecoverable.',
    recommended_action: overallStatus === 'GREEN' ? null
      : overallStatus === 'AMBER'
      ? 'Verify backup configuration and confirm last successful backup.'
      : 'Investigate backup failures immediately. Do not delay — recovery capability is compromised.',
    metrics,
    trend: 'STABLE',
  };
}

// ──────────────────────────────────────────────────────────────────────────────
// Engineering Activity
// ──────────────────────────────────────────────────────────────────────────────
export interface EngineeringData {
  deploys_30d: number;
  failed_builds_30d: number;
  hotfixes_30d: number;
  open_prs: number;
  merged_prs_30d: number;
  open_dependabot_prs: number;
  last_deploy_at?: Date;
}

export function evaluateEngineering(data: EngineeringData): CategoryResult {
  const metrics: MetricResult[] = [];

  const buildStatus: TrafficLight =
    data.failed_builds_30d > 3 ? 'RED' :
    data.failed_builds_30d > 1 ? 'AMBER' : 'GREEN';
  metrics.push({
    key: 'failed_builds',
    label: 'Failed Deployments',
    value: data.failed_builds_30d,
    status: buildStatus,
    description: data.failed_builds_30d === 0
      ? 'All deployments succeeded this month'
      : `${data.failed_builds_30d} failed deployment${data.failed_builds_30d !== 1 ? 's' : ''} this month`,
  });

  const hotfixStatus: TrafficLight =
    data.hotfixes_30d > 3 ? 'RED' :
    data.hotfixes_30d > 1 ? 'AMBER' : 'GREEN';
  metrics.push({
    key: 'hotfixes',
    label: 'Hotfixes',
    value: data.hotfixes_30d,
    status: hotfixStatus,
    description: data.hotfixes_30d === 0
      ? 'No emergency fixes required this month'
      : `${data.hotfixes_30d} emergency fix${data.hotfixes_30d !== 1 ? 'es were' : ' was'} deployed this month`,
  });

  const activityStatus: TrafficLight =
    data.deploys_30d > 0 ? 'GREEN' : 'AMBER';
  metrics.push({
    key: 'deploys',
    label: 'Deployments',
    value: data.deploys_30d,
    status: activityStatus,
    description: `${data.deploys_30d} deployment${data.deploys_30d !== 1 ? 's' : ''} to production this month`,
  });

  const allStatuses = metrics.map(m => m.status);
  const overallStatus = worstStatus(allStatuses);

  const score =
    (buildStatus === 'GREEN' ? 50 : buildStatus === 'AMBER' ? 25 : 0) +
    (hotfixStatus === 'GREEN' ? 30 : hotfixStatus === 'AMBER' ? 15 : 0) +
    (activityStatus === 'GREEN' ? 20 : 10);

  return {
    status: overallStatus,
    score,
    headline: overallStatus === 'GREEN'
      ? 'Engineering activity is healthy'
      : overallStatus === 'AMBER'
      ? 'Some engineering issues this month'
      : 'Engineering problems need attention',
    interpretation: overallStatus === 'GREEN'
      ? `${data.deploys_30d} successful deployment${data.deploys_30d !== 1 ? 's' : ''} this month with no failures or emergency fixes. Your site is being maintained and improved in a controlled way.`
      : `There were ${data.failed_builds_30d} failed deployment${data.failed_builds_30d !== 1 ? 's' : ''} and ${data.hotfixes_30d} emergency fix${data.hotfixes_30d !== 1 ? 'es' : ''} this month. This can indicate instability in the development process.`,
    risk: overallStatus === 'GREEN'
      ? 'No engineering risk.'
      : overallStatus === 'AMBER'
      ? 'Moderate risk. Repeated deployment failures can introduce instability.'
      : 'Elevated risk. Frequent hotfixes and failed builds suggest an unstable development process.',
    recommended_action: overallStatus === 'GREEN' ? null
      : 'Review recent failed deployments and identify root causes.',
    metrics,
    trend: 'STABLE',
  };
}

// ──────────────────────────────────────────────────────────────────────────────
// Health Score Aggregation
// ──────────────────────────────────────────────────────────────────────────────
export function calculateHealthScore(categories: {
  performance: CategoryResult;
  security: CategoryResult;
  reliability: CategoryResult;
  updates: CategoryResult;
  backups: CategoryResult;
}): number {
  return Math.round(
    categories.performance.score  * WEIGHTS.performance +
    categories.security.score     * WEIGHTS.security +
    categories.reliability.score  * WEIGHTS.reliability +
    categories.updates.score      * WEIGHTS.updates +
    categories.backups.score      * WEIGHTS.backups,
  );
}

// ──────────────────────────────────────────────────────────────────────────────
// Recommended Actions Aggregation
// ──────────────────────────────────────────────────────────────────────────────
export function buildRecommendedActions(report: Omit<HealthReport, 'health_score' | 'overall_status' | 'recommended_actions'>): RecommendedAction[] {
  const actions: RecommendedAction[] = [];

  const categories = [
    report.performance,
    report.security,
    report.reliability,
    report.updates,
    report.backups,
    report.engineering,
  ];

  for (const cat of categories) {
    if (cat.recommended_action) {
      actions.push({
        priority: cat.status === 'RED' ? 'HIGH' : 'MEDIUM',
        category: cat.headline.split(' ')[0],
        action: cat.recommended_action,
        business_impact: cat.risk,
      });
    }
  }

  return actions.sort((a, b) =>
    a.priority === 'HIGH' && b.priority !== 'HIGH' ? -1 :
    a.priority !== 'HIGH' && b.priority === 'HIGH' ? 1 : 0
  );
}

// ──────────────────────────────────────────────────────────────────────────────
// Full Report Assembly
// ──────────────────────────────────────────────────────────────────────────────
export interface AllMetrics {
  performance: PerformanceData;
  security: SecurityData;
  reliability: ReliabilityData;
  updates: UpdatesData;
  backups: BackupsData;
  engineering: EngineeringData;
}

export function generateHealthReport(metrics: AllMetrics): HealthReport {
  const performance  = evaluatePerformance(metrics.performance);
  const security     = evaluateSecurity(metrics.security);
  const reliability  = evaluateReliability(metrics.reliability);
  const updates      = evaluateUpdates(metrics.updates);
  const backups      = evaluateBackups(metrics.backups);
  const engineering  = evaluateEngineering(metrics.engineering);

  const health_score = calculateHealthScore({ performance, security, reliability, updates, backups });
  const overall_status = scoreToTrafficLight(health_score);

  const report = { performance, security, reliability, updates, backups, engineering };
  const recommended_actions = buildRecommendedActions(report);

  return {
    health_score,
    overall_status,
    ...report,
    recommended_actions,
  };
}
