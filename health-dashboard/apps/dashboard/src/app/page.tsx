/**
 * Agency overview — all client sites at a glance.
 * Traffic light status, health score, and key metrics per site.
 */

import Link from 'next/link';
import clsx from 'clsx';
import { TrafficLightBadge, TrafficLightDot } from '@/components/TrafficLight';
import { MOCK_SITES } from '@/lib/mock-data';
import type { Site } from '@/lib/types';

function SiteRow({ site }: { site: Site }) {
  const statusBg: Record<string, string> = {
    GREEN: 'bg-emerald-50',
    AMBER: 'bg-amber-50',
    RED:   'bg-red-50',
  };

  return (
    <Link
      href={`/sites/${site.id}`}
      className="block card hover:shadow-md transition-all duration-200 hover:-translate-y-0.5 group"
    >
      <div className="p-5">
        <div className="flex items-start justify-between gap-4">
          {/* Site info */}
          <div className="flex items-start gap-3 flex-1 min-w-0">
            <div className={clsx(
              'w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 text-sm font-bold font-heading text-wordie-dark-teal',
              statusBg[site.traffic_light],
            )}>
              {site.client_name.slice(0, 2).toUpperCase()}
            </div>
            <div className="min-w-0">
              <p className="font-heading font-semibold text-wordie-dark-teal group-hover:text-wordie-accent-teal transition-colors">
                {site.client_name}
              </p>
              <p className="text-sm text-gray-400 font-body truncate">{site.domain}</p>
            </div>
          </div>

          {/* Overall status */}
          <div className="flex items-center gap-3 flex-shrink-0">
            <div className="text-right hidden sm:block">
              <p className="text-2xl font-heading font-bold text-wordie-dark-teal leading-none">
                {site.health_score}
              </p>
              <p className="text-xs text-gray-400 font-body">/ 100</p>
            </div>
            <TrafficLightDot status={site.traffic_light} size="lg" pulse={site.traffic_light === 'RED'} />
          </div>
        </div>

        {/* Metrics row */}
        <div className="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-3">
          <MetricPill
            label="PageSpeed"
            value={`${site.pagespeed_mobile}/100`}
            status={site.pagespeed_mobile >= 80 ? 'GREEN' : site.pagespeed_mobile >= 60 ? 'AMBER' : 'RED'}
          />
          <MetricPill
            label="Uptime"
            value={`${site.uptime_pct_30d.toFixed(1)}%`}
            status={site.uptime_pct_30d >= 99.9 ? 'GREEN' : site.uptime_pct_30d >= 99 ? 'AMBER' : 'RED'}
          />
          <MetricPill
            label="Updates"
            value={site.pending_updates === 0 ? 'Current' : `${site.pending_updates} pending`}
            status={site.pending_updates === 0 ? 'GREEN' : site.pending_updates > 5 ? 'RED' : 'AMBER'}
          />
          <MetricPill
            label="Backups"
            value={site.backup_status === 'SUCCESS' ? 'Healthy' : site.backup_status === 'FAILED' ? 'Failed' : 'Unknown'}
            status={site.backup_status === 'SUCCESS' ? 'GREEN' : site.backup_status === 'FAILED' ? 'RED' : 'AMBER'}
          />
        </div>

        {site.critical_vulns > 0 && (
          <div className="mt-3 flex items-center gap-1.5 text-xs text-red-600 font-semibold bg-red-50 px-3 py-1.5 rounded-lg border border-red-200">
            <span>⚠</span>
            <span>{site.critical_vulns} critical vulnerabilit{site.critical_vulns !== 1 ? 'ies' : 'y'} detected</span>
          </div>
        )}
      </div>
    </Link>
  );
}

function MetricPill({
  label,
  value,
  status,
}: {
  label: string;
  value: string;
  status: 'GREEN' | 'AMBER' | 'RED';
}) {
  const textColor = {
    GREEN: 'text-emerald-700',
    AMBER: 'text-amber-700',
    RED:   'text-red-700',
  };
  return (
    <div className="bg-gray-50 rounded-lg px-3 py-2">
      <p className="text-xs text-gray-400 font-body mb-0.5">{label}</p>
      <p className={clsx('text-sm font-semibold font-heading', textColor[status])}>{value}</p>
    </div>
  );
}

function SummaryCard({ label, count, status }: { label: string; count: number; status: 'GREEN' | 'AMBER' | 'RED' }) {
  const bg = {
    GREEN: 'bg-emerald-50 border-emerald-200',
    AMBER: 'bg-amber-50 border-amber-200',
    RED:   'bg-red-50 border-red-200',
  };
  const text = {
    GREEN: 'text-emerald-700',
    AMBER: 'text-amber-700',
    RED:   'text-red-700',
  };
  return (
    <div className={clsx('rounded-xl border px-5 py-4 flex items-center gap-3', bg[status])}>
      <TrafficLightDot status={status} size="md" />
      <div>
        <p className={clsx('text-2xl font-heading font-bold leading-none', text[status])}>{count}</p>
        <p className={clsx('text-xs font-body mt-0.5', text[status])}>{label}</p>
      </div>
    </div>
  );
}

export default function HomePage() {
  const sites = MOCK_SITES;

  const greenCount = sites.filter(s => s.traffic_light === 'GREEN').length;
  const amberCount = sites.filter(s => s.traffic_light === 'AMBER').length;
  const redCount   = sites.filter(s => s.traffic_light === 'RED').length;

  const avgScore = Math.round(sites.reduce((sum, s) => sum + s.health_score, 0) / sites.length);

  return (
    <div>
      {/* Page header */}
      <div className="mb-8">
        <h1 className="font-heading font-bold text-h2 text-wordie-dark-teal mb-1">
          Client websites
        </h1>
        <p className="text-gray-500 font-body">
          Real-time health status across all active client sites.
          Last updated: {new Date().toLocaleDateString('en-AU', { day: 'numeric', month: 'long', year: 'numeric' })}
        </p>
      </div>

      {/* Summary row */}
      <div className="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <div className="card-padded flex items-center gap-3">
          <div className="w-10 h-10 bg-wordie-dark-teal/10 rounded-lg flex items-center justify-center">
            <span className="text-wordie-dark-teal font-heading font-bold text-lg">{sites.length}</span>
          </div>
          <div>
            <p className="text-2xl font-heading font-bold text-wordie-dark-teal leading-none">{avgScore}</p>
            <p className="text-xs text-gray-400 font-body">Avg health score</p>
          </div>
        </div>
        <SummaryCard label="Healthy" count={greenCount} status="GREEN" />
        <SummaryCard label="Needs attention" count={amberCount} status="AMBER" />
        <SummaryCard label="Action required" count={redCount} status="RED" />
      </div>

      {/* Site list */}
      <div className="space-y-4">
        {/* Sites requiring action first */}
        {redCount > 0 && (
          <div>
            <h2 className="font-heading font-semibold text-sm text-red-600 uppercase tracking-wide mb-3 flex items-center gap-2">
              <TrafficLightDot status="RED" size="sm" pulse />
              Action required
            </h2>
            <div className="space-y-3">
              {sites.filter(s => s.traffic_light === 'RED').map(s => <SiteRow key={s.id} site={s} />)}
            </div>
          </div>
        )}

        {amberCount > 0 && (
          <div>
            <h2 className="font-heading font-semibold text-sm text-amber-600 uppercase tracking-wide mb-3 mt-6 flex items-center gap-2">
              <TrafficLightDot status="AMBER" size="sm" />
              Needs attention
            </h2>
            <div className="space-y-3">
              {sites.filter(s => s.traffic_light === 'AMBER').map(s => <SiteRow key={s.id} site={s} />)}
            </div>
          </div>
        )}

        {greenCount > 0 && (
          <div>
            <h2 className="font-heading font-semibold text-sm text-emerald-600 uppercase tracking-wide mb-3 mt-6 flex items-center gap-2">
              <TrafficLightDot status="GREEN" size="sm" />
              Healthy
            </h2>
            <div className="space-y-3">
              {sites.filter(s => s.traffic_light === 'GREEN').map(s => <SiteRow key={s.id} site={s} />)}
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
