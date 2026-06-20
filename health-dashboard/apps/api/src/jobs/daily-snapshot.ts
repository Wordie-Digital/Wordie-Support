/**
 * Daily snapshot job.
 * Runs at 00:00 AEST every day.
 * Collects metrics from all APIs, runs the rules engine, persists the result.
 */

import cron from 'node-cron';
import { db } from '../db/client';
import { WPEngineService } from '../services/wpengine';
import { GitHubService } from '../services/github';
import { PageSpeedService } from '../services/pagespeed';
import { generateHealthReport } from '../engine/rules';

interface Website {
  id: string;
  slug: string;
  client_name: string;
  domain: string;
  wp_engine_install: string;
  github_repo: string;
  pagespeed_url: string;
}

export async function collectSnapshot(website: Website): Promise<void> {
  console.log(`[snapshot] Collecting metrics for ${website.client_name} (${website.domain})`);

  const wpengine  = new WPEngineService(
    process.env.WPENGINE_USER!,
    process.env.WPENGINE_PASS!,
  );
  const github    = new GitHubService(process.env.GITHUB_TOKEN!);
  const pagespeed = new PageSpeedService(process.env.PAGESPEED_API_KEY!);

  const [wpeData, ghData, psData] = await Promise.allSettled([
    website.wp_engine_install ? wpengine.getInstallMetrics(website.wp_engine_install) : Promise.resolve(null),
    website.github_repo       ? github.getRepoMetrics(website.github_repo)            : Promise.resolve(null),
    pagespeed.getMetrics(website.pagespeed_url ?? `https://${website.domain}`),
  ]);

  const wpe = wpeData.status === 'fulfilled' ? wpeData.value : null;
  const gh  = ghData.status  === 'fulfilled' ? ghData.value  : null;
  const ps  = psData.status  === 'fulfilled' ? psData.value  : null;

  const metrics = {
    performance: {
      pagespeed_mobile:  ps?.pagespeed_mobile  ?? 0,
      pagespeed_desktop: ps?.pagespeed_desktop ?? 0,
      lcp_mobile:        ps?.lcp_mobile  ?? 0,
      inp_mobile:        ps?.inp_mobile  ?? 0,
      cls_mobile:        ps?.cls_mobile  ?? 0,
      ttfb_ms:           ps?.ttfb_ms     ?? 0,
    },
    security: {
      critical_vulns:         wpe ? (wpe.critical_plugin_updates > 0 ? wpe.critical_plugin_updates : 0) : 0,
      high_vulns:             0,
      open_dependabot_alerts: gh?.open_dependabot_alerts ?? 0,
      blocked_attacks_30d:    wpe?.blocked_attacks_30d ?? 0,
      active_ddos:            wpe?.active_ddos ?? false,
      waf_enabled:            wpe?.waf_enabled ?? false,
      ssl_valid:              wpe?.ssl_valid ?? true,
      ssl_days_remaining:     wpe?.ssl_days_remaining ?? 365,
    },
    reliability: {
      uptime_pct_7d:    wpe?.uptime_pct_7d  ?? 100,
      uptime_pct_30d:   wpe?.uptime_pct_30d ?? 100,
      incidents_7d:     wpe?.incidents_7d  ?? 0,
      incidents_30d:    wpe?.incidents_30d ?? 0,
      avg_response_ms:  wpe?.avg_response_ms  ?? 200,
      p95_response_ms:  wpe?.p95_response_ms  ?? 400,
    },
    updates: {
      wp_core_current:         wpe?.wp_core_current ?? true,
      pending_plugin_updates:  wpe?.pending_plugin_updates  ?? 0,
      critical_plugin_updates: wpe?.critical_plugin_updates ?? 0,
      pending_theme_updates:   wpe?.pending_theme_updates   ?? 0,
      php_version:             wpe?.php_version    ?? '8.2',
      php_eol:                 wpe?.php_eol        ?? false,
      php_security_only:       wpe?.php_security_only ?? false,
    },
    backups: {
      last_successful_backup: wpe?.backup_last_success ?? null,
      backup_frequency:       wpe?.backup_frequency ?? 'DAILY',
      last_backup_status:     wpe?.backup_status ?? 'UNKNOWN',
      consecutive_failures:   wpe?.backup_consecutive_failures ?? 0,
      offsite_copy:           true,
      restore_tested:         false,
    },
    engineering: {
      deploys_30d:           gh?.deploys_30d           ?? 0,
      failed_builds_30d:     gh?.failed_builds_30d     ?? 0,
      hotfixes_30d:          gh?.hotfixes_30d          ?? 0,
      open_prs:              gh?.open_prs              ?? 0,
      merged_prs_30d:        gh?.merged_prs_30d        ?? 0,
      open_dependabot_prs:   gh?.open_dependabot_prs   ?? 0,
      last_deploy_at:        gh?.last_deploy_at        ?? null,
    },
  };

  const report = generateHealthReport(metrics);

  const trafficLights = {
    performance:  report.performance.status,
    security:     report.security.status,
    reliability:  report.reliability.status,
    updates:      report.updates.status,
    backups:      report.backups.status,
    engineering:  report.engineering.status,
  };

  await db.query(`
    INSERT INTO metrics_snapshots (
      website_id, snapshot_date,
      performance_raw, security_raw, reliability_raw,
      updates_raw, backups_raw, engineering_raw,
      performance_score, security_score, reliability_score,
      updates_score, backups_score, engineering_score,
      health_score, traffic_lights_json,
      pagespeed_mobile, pagespeed_desktop,
      lcp_mobile, inp_mobile, cls_mobile,
      uptime_pct_7d, uptime_pct_30d,
      blocked_attacks_24h, pending_updates, critical_vulns,
      backup_last_success, backup_status,
      deploy_count_7d, failed_builds_7d, open_security_alerts
    ) VALUES (
      $1, CURRENT_DATE,
      $2, $3, $4, $5, $6, $7,
      $8, $9, $10, $11, $12, $13,
      $14, $15,
      $16, $17, $18, $19, $20,
      $21, $22,
      $23, $24, $25, $26, $27,
      $28, $29, $30
    )
    ON CONFLICT (website_id, snapshot_date)
    DO UPDATE SET
      performance_raw   = EXCLUDED.performance_raw,
      security_raw      = EXCLUDED.security_raw,
      reliability_raw   = EXCLUDED.reliability_raw,
      updates_raw       = EXCLUDED.updates_raw,
      backups_raw       = EXCLUDED.backups_raw,
      engineering_raw   = EXCLUDED.engineering_raw,
      performance_score = EXCLUDED.performance_score,
      security_score    = EXCLUDED.security_score,
      reliability_score = EXCLUDED.reliability_score,
      updates_score     = EXCLUDED.updates_score,
      backups_score     = EXCLUDED.backups_score,
      engineering_score = EXCLUDED.engineering_score,
      health_score      = EXCLUDED.health_score,
      traffic_lights_json = EXCLUDED.traffic_lights_json,
      pagespeed_mobile  = EXCLUDED.pagespeed_mobile,
      pagespeed_desktop = EXCLUDED.pagespeed_desktop,
      lcp_mobile        = EXCLUDED.lcp_mobile,
      inp_mobile        = EXCLUDED.inp_mobile,
      cls_mobile        = EXCLUDED.cls_mobile,
      uptime_pct_7d     = EXCLUDED.uptime_pct_7d,
      uptime_pct_30d    = EXCLUDED.uptime_pct_30d,
      blocked_attacks_24h = EXCLUDED.blocked_attacks_24h,
      pending_updates   = EXCLUDED.pending_updates,
      critical_vulns    = EXCLUDED.critical_vulns,
      backup_last_success = EXCLUDED.backup_last_success,
      backup_status     = EXCLUDED.backup_status,
      deploy_count_7d   = EXCLUDED.deploy_count_7d,
      failed_builds_7d  = EXCLUDED.failed_builds_7d,
      open_security_alerts = EXCLUDED.open_security_alerts
  `, [
    website.id, // $1
    JSON.stringify(ps?.raw_mobile ?? {}),        // $2
    JSON.stringify(metrics.security),            // $3
    JSON.stringify(metrics.reliability),         // $4
    JSON.stringify(metrics.updates),             // $5
    JSON.stringify(metrics.backups),             // $6
    JSON.stringify(gh ?? {}),                    // $7
    report.performance.score,    // $8
    report.security.score,       // $9
    report.reliability.score,    // $10
    report.updates.score,        // $11
    report.backups.score,        // $12
    report.engineering.score,    // $13
    report.health_score,         // $14
    JSON.stringify(trafficLights), // $15
    ps?.pagespeed_mobile  ?? 0,  // $16
    ps?.pagespeed_desktop ?? 0,  // $17
    ps?.lcp_mobile  ?? 0,        // $18
    ps?.inp_mobile  ?? 0,        // $19
    ps?.cls_mobile  ?? 0,        // $20
    metrics.reliability.uptime_pct_7d,  // $21
    metrics.reliability.uptime_pct_30d, // $22
    metrics.security.blocked_attacks_30d, // $23 (using 30d as proxy)
    metrics.updates.pending_plugin_updates, // $24
    metrics.security.critical_vulns, // $25
    metrics.backups.last_successful_backup, // $26
    metrics.backups.last_backup_status, // $27
    metrics.engineering.deploys_30d,  // $28 (using 30d as proxy)
    metrics.engineering.failed_builds_30d, // $29
    metrics.security.open_dependabot_alerts, // $30
  ]);

  // Persist deployments
  if (gh?.recent_deployments?.length) {
    for (const deploy of gh.recent_deployments) {
      await db.query(`
        INSERT INTO deployments (
          website_id, deployed_at, environment, status,
          commit_sha, commit_message, branch, is_hotfix, github_run_id
        ) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9)
        ON CONFLICT DO NOTHING
      `, [
        website.id,
        deploy.deployed_at,
        deploy.environment,
        deploy.status,
        deploy.commit_sha,
        deploy.commit_message,
        deploy.branch,
        deploy.is_hotfix,
        deploy.run_id,
      ]);
    }
  }

  console.log(`[snapshot] Done: ${website.client_name} — score ${report.health_score} (${report.overall_status})`);
}

export async function runDailySnapshots(): Promise<void> {
  const sites = await db.query(`SELECT * FROM websites WHERE active = true`);
  for (const site of sites.rows) {
    try {
      await collectSnapshot(site);
    } catch (err) {
      console.error(`[snapshot] Failed for ${site.client_name}:`, err);
    }
  }
}

export function scheduleDailySnapshots(): void {
  // Run at midnight AEST (14:00 UTC)
  cron.schedule('0 14 * * *', async () => {
    console.log('[snapshot] Daily collection started');
    await runDailySnapshots();
    console.log('[snapshot] Daily collection complete');
  }, { timezone: 'Australia/Sydney' });
  console.log('[snapshot] Daily snapshot job scheduled (00:00 AEST)');
}
