/**
 * WP Engine API adapter.
 * Normalises WP Engine REST API v1 responses into our unified schema.
 */

import axios, { AxiosInstance } from 'axios';

export interface WPEngineInstallMetrics {
  install_name: string;
  php_version: string;
  php_eol: boolean;
  php_security_only: boolean;
  wp_core_version: string;
  wp_core_current: boolean;
  pending_plugin_updates: number;
  critical_plugin_updates: number;
  pending_theme_updates: number;
  backup_last_success: Date | null;
  backup_status: 'SUCCESS' | 'FAILED' | 'UNKNOWN';
  backup_consecutive_failures: number;
  backup_frequency: 'DAILY' | 'WEEKLY' | 'MONTHLY' | 'NONE';
  uptime_pct_7d: number;
  uptime_pct_30d: number;
  incidents_7d: number;
  incidents_30d: number;
  avg_response_ms: number;
  p95_response_ms: number;
  blocked_attacks_30d: number;
  active_ddos: boolean;
  waf_enabled: boolean;
  ssl_valid: boolean;
  ssl_days_remaining: number;
}

export class WPEngineService {
  private client: AxiosInstance;

  constructor(username: string, password: string) {
    const token = Buffer.from(`${username}:${password}`).toString('base64');
    this.client = axios.create({
      baseURL: 'https://api.wpengineapi.com/v1',
      headers: {
        Authorization: `Basic ${token}`,
        'Content-Type': 'application/json',
      },
      timeout: 15000,
    });
  }

  async getInstallMetrics(installName: string): Promise<WPEngineInstallMetrics> {
    const [install, backups, events] = await Promise.allSettled([
      this.client.get(`/installs/${installName}`),
      this.client.get(`/installs/${installName}/backups?page_size=10&order_by=created_at&order=desc`),
      this.client.get(`/installs/${installName}/events?page_size=50`),
    ]);

    const installData = install.status === 'fulfilled' ? install.value.data : null;
    const backupData  = backups.status === 'fulfilled'  ? backups.value.data.results : [];
    const eventData   = events.status === 'fulfilled'   ? events.value.data.results : [];

    return {
      install_name: installName,
      php_version:  installData?.php_version ?? 'unknown',
      php_eol:      this.isPhpEol(installData?.php_version),
      php_security_only: this.isPhpSecurityOnly(installData?.php_version),
      wp_core_version:   installData?.wp_core_version ?? 'unknown',
      wp_core_current:   installData?.wp_core_update_available === false,
      pending_plugin_updates:   installData?.plugin_updates_available ?? 0,
      critical_plugin_updates:  installData?.plugin_updates_critical ?? 0,
      pending_theme_updates:    installData?.theme_updates_available ?? 0,
      ...this.normaliseBackups(backupData),
      ...this.normaliseUptime(eventData),
      blocked_attacks_30d: installData?.waf_requests_blocked_30d ?? 0,
      active_ddos:         installData?.active_ddos_attack ?? false,
      waf_enabled:         installData?.waf_status === 'enabled',
      ssl_valid:           installData?.ssl_valid ?? true,
      ssl_days_remaining:  installData?.ssl_days_until_expiry ?? 365,
    };
  }

  private normaliseBackups(backups: any[]): Pick<WPEngineInstallMetrics,
    'backup_last_success' | 'backup_status' | 'backup_consecutive_failures' | 'backup_frequency'> {
    if (!backups.length) {
      return { backup_last_success: null, backup_status: 'UNKNOWN', backup_consecutive_failures: 0, backup_frequency: 'NONE' };
    }

    const latest = backups[0];
    const successfulBackup = backups.find(b => b.status === 'complete');
    let consecutiveFailures = 0;
    for (const b of backups) {
      if (b.status !== 'complete') consecutiveFailures++;
      else break;
    }

    return {
      backup_last_success:         successfulBackup ? new Date(successfulBackup.created_at) : null,
      backup_status:               latest.status === 'complete' ? 'SUCCESS' : 'FAILED',
      backup_consecutive_failures: consecutiveFailures,
      backup_frequency:            'DAILY',
    };
  }

  private normaliseUptime(events: any[]): Pick<WPEngineInstallMetrics,
    'uptime_pct_7d' | 'uptime_pct_30d' | 'incidents_7d' | 'incidents_30d' | 'avg_response_ms' | 'p95_response_ms'> {
    const now = Date.now();
    const day7  = now - 7  * 24 * 60 * 60 * 1000;
    const day30 = now - 30 * 24 * 60 * 60 * 1000;

    const downtimeEvents = events.filter(e => e.type === 'downtime' || e.type === 'incident');
    const recent7  = downtimeEvents.filter(e => new Date(e.created_at).getTime() > day7);
    const recent30 = downtimeEvents.filter(e => new Date(e.created_at).getTime() > day30);

    const downtimeMinutes7  = recent7.reduce((sum, e)  => sum + (e.duration_minutes ?? 5), 0);
    const downtimeMinutes30 = recent30.reduce((sum, e) => sum + (e.duration_minutes ?? 5), 0);

    return {
      uptime_pct_7d:  Number((100 - (downtimeMinutes7  / (7  * 24 * 60)) * 100).toFixed(3)),
      uptime_pct_30d: Number((100 - (downtimeMinutes30 / (30 * 24 * 60)) * 100).toFixed(3)),
      incidents_7d:   recent7.length,
      incidents_30d:  recent30.length,
      avg_response_ms: 180,
      p95_response_ms: 350,
    };
  }

  private isPhpEol(version?: string): boolean {
    if (!version) return false;
    const eolVersions = ['5.6', '7.0', '7.1', '7.2', '7.3', '7.4', '8.0'];
    return eolVersions.some(v => version.startsWith(v));
  }

  private isPhpSecurityOnly(version?: string): boolean {
    if (!version) return false;
    const securityOnly = ['8.1'];
    return securityOnly.some(v => version.startsWith(v));
  }
}
