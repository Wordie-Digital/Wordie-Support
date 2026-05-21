/**
 * Live dashboard for a single client site.
 * Executive summary + traffic light grid + detailed category scores.
 */

'use client';

import Link from 'next/link';
import clsx from 'clsx';
import { HealthScoreGauge } from '@/components/HealthScoreGauge';
import { CategoryCard } from '@/components/CategoryCard';
import { AlertFeed } from '@/components/AlertFeed';
import { DeploymentTimeline } from '@/components/DeploymentTimeline';
import { RecommendedActions } from '@/components/RecommendedActions';
import { TrendChart } from '@/components/TrendChart';
import { TrafficLightBadge } from '@/components/TrafficLight';
import { MOCK_DASHBOARD, MOCK_TREND, BARNARDOS_DASHBOARD, BARNARDOS_TREND } from '@/lib/mock-data';
import type { DashboardData, TrafficLight } from '@/lib/types';

const CATEGORY_META: Array<{
  key: keyof DashboardData['traffic_lights'];
  label: string;
  icon: string;
  weight: string;
}> = [
  { key: 'performance',  label: 'Performance',        icon: '⚡', weight: '25%' },
  { key: 'security',     label: 'Security',           icon: '🔒', weight: '25%' },
  { key: 'reliability',  label: 'Reliability',        icon: '📡', weight: '20%' },
  { key: 'updates',      label: 'Updates',            icon: '🔄', weight: '15%' },
  { key: 'backups',      label: 'Backups',            icon: '💾', weight: '15%' },
  { key: 'engineering',  label: 'Engineering',        icon: '⚙️',  weight: '—'   },
];

const CATEGORY_HEADLINES: Record<string, Record<TrafficLight, string>> = {
  performance: {
    GREEN: 'Site loads quickly on all devices',
    AMBER: 'Mobile page speed needs attention',
    RED:   'Performance issues require urgent action',
  },
  security: {
    GREEN: 'No threats detected — firewall active',
    AMBER: 'Security items need attention this month',
    RED:   'Critical security issues detected',
  },
  reliability: {
    GREEN: 'Site is stable and fully available',
    AMBER: 'Some availability issues this period',
    RED:   'Downtime events need urgent attention',
  },
  updates: {
    GREEN: 'All software is current',
    AMBER: 'Plugin updates are pending',
    RED:   'Critical updates required now',
  },
  backups: {
    GREEN: 'Daily backups completing successfully',
    AMBER: 'Backup health needs review',
    RED:   'Backup failures detected — recovery at risk',
  },
  engineering: {
    GREEN: 'Clean deployment history this month',
    AMBER: 'Some deployment issues this month',
    RED:   'Deployment problems need attention',
  },
};

function KeyMetric({ label, value, sub, status }: {
  label: string;
  value: string;
  sub?: string;
  status: TrafficLight;
}) {
  const valueColor: Record<TrafficLight, string> = {
    GREEN: 'text-emerald-600',
    AMBER: 'text-amber-600',
    RED:   'text-red-600',
  };
  return (
    <div className="card-padded text-center">
      <p className={clsx('text-2xl font-heading font-bold leading-none', valueColor[status])}>{value}</p>
      {sub && <p className="text-xs text-gray-400 font-body mt-0.5">{sub}</p>}
      <p className="text-xs text-gray-500 font-body mt-2">{label}</p>
    </div>
  );
}

export default function SiteDashboardPage({ params }: { params: { siteId: string } }) {
  const data   = params.siteId === 'barnardos' ? BARNARDOS_DASHBOARD : MOCK_DASHBOARD;
  const trend  = params.siteId === 'barnardos' ? BARNARDOS_TREND     : MOCK_TREND;

  const psStatus: TrafficLight = data.key_metrics.pagespeed_mobile >= 80 ? 'GREEN' : data.key_metrics.pagespeed_mobile >= 60 ? 'AMBER' : 'RED';
  const uptimeStatus: TrafficLight = data.key_metrics.uptime_pct_30d >= 99.9 ? 'GREEN' : data.key_metrics.uptime_pct_30d >= 99 ? 'AMBER' : 'RED';
  const updateStatus: TrafficLight = data.key_metrics.pending_updates === 0 ? 'GREEN' : data.key_metrics.pending_updates > 5 ? 'RED' : 'AMBER';
  const backupStatus: TrafficLight = data.key_metrics.backup_status === 'SUCCESS' ? 'GREEN' : data.key_metrics.backup_status === 'FAILED' ? 'RED' : 'AMBER';

  return (
    <div>
      {/* Breadcrumb */}
      <nav className="mb-6 text-sm font-body text-gray-400 flex items-center gap-2 no-print">
        <Link href="/" className="hover:text-wordie-accent-teal transition-colors">All sites</Link>
        <span>/</span>
        <span className="text-wordie-dark-teal font-semibold">{data.site.client_name}</span>
      </nav>

      {/* Executive Summary Card */}
      <div className="bg-wordie-dark-teal rounded-2xl p-6 sm:p-8 mb-6 text-white">
        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
          <div className="flex-1">
            <div className="flex items-center gap-3 mb-2">
              <h1 className="font-heading font-bold text-h3 text-wordie-off-white">
                {data.site.client_name}
              </h1>
              <TrafficLightBadge
                status={data.overall_status}
                label={
                  data.overall_status === 'GREEN' ? 'Healthy' :
                  data.overall_status === 'AMBER' ? 'Needs attention' : 'Action required'
                }
              />
            </div>
            <p className="text-wordie-light-green/70 text-sm font-body mb-4">{data.site.domain}</p>

            <p className="text-wordie-off-white/80 font-body text-sm leading-relaxed max-w-lg">
              {data.overall_status === 'GREEN'
                ? `Your website is performing well across all areas. All systems are operating normally with no issues requiring attention.`
                : data.overall_status === 'AMBER'
                ? `Your website is running well in most areas. There are a few items that need attention — performance and pending updates — but no critical issues.`
                : `Your website has issues requiring immediate attention. See the recommended actions below.`
              }
            </p>

            <div className="mt-4 flex flex-wrap gap-3">
              <Link
                href={`/sites/${data.site.id}/reports`}
                className="inline-flex items-center gap-2 bg-wordie-accent-teal/20 text-wordie-light-green hover:bg-wordie-accent-teal/30 transition-colors px-4 py-2 rounded-lg text-sm font-semibold font-heading border border-wordie-light-green/20"
              >
                Monthly reports →
              </Link>
              <a
                href={`https://${data.site.domain}`}
                target="_blank"
                rel="noopener noreferrer"
                className="inline-flex items-center gap-2 text-wordie-light-green/60 hover:text-wordie-light-green transition-colors px-4 py-2 text-sm font-body"
              >
                Visit site ↗
              </a>
            </div>
          </div>

          <div className="flex-shrink-0">
            <HealthScoreGauge
              score={data.health_score}
              status={data.overall_status}
              delta={data.health_score_delta}
              size="lg"
            />
          </div>
        </div>
      </div>

      {/* Key metrics row */}
      <div className="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <KeyMetric label="PageSpeed mobile" value={`${data.key_metrics.pagespeed_mobile}`} sub="/100" status={psStatus} />
        <KeyMetric label="Uptime (30 days)" value={`${data.key_metrics.uptime_pct_30d.toFixed(1)}%`} status={uptimeStatus} />
        <KeyMetric
          label="Pending updates"
          value={`${data.key_metrics.pending_updates}`}
          sub={data.key_metrics.pending_updates === 0 ? 'all current' : 'plugins'}
          status={updateStatus}
        />
        <KeyMetric
          label="Backup status"
          value={data.key_metrics.backup_status === 'SUCCESS' ? 'Healthy' : data.key_metrics.backup_status === 'FAILED' ? 'Failed' : 'Unknown'}
          status={backupStatus}
        />
      </div>

      {/* Category grid */}
      <h2 className="font-heading font-bold text-h4 text-wordie-dark-teal mb-4">Health breakdown</h2>
      <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
        {CATEGORY_META.map(({ key, label, icon, weight }) => {
          const status = data.traffic_lights[key] as TrafficLight ?? 'GREEN';
          const score  = data.category_scores[key as keyof typeof data.category_scores] ?? 0;
          return (
            <CategoryCard
              key={key}
              title={label}
              status={status}
              score={score}
              headline={CATEGORY_HEADLINES[key]?.[status] ?? ''}
              icon={icon}
              weight={weight}
            />
          );
        })}
      </div>

      {/* Trend + Actions row */}
      <div className="grid lg:grid-cols-2 gap-6 mb-6">
        {/* 30-day trend */}
        <div className="card-padded">
          <h3 className="font-heading font-semibold text-wordie-dark-teal mb-1">Health score trend</h3>
          <p className="text-xs text-gray-400 font-body mb-4">Last 30 days</p>
          <TrendChart data={trend} metric="health_score" height={180} />
          <div className="flex items-center justify-center gap-6 mt-3 text-xs text-gray-400 font-body">
            <span className="flex items-center gap-1"><span className="w-6 h-0.5 bg-emerald-400 inline-block border-t border-dashed border-emerald-400" /> Healthy (90+)</span>
            <span className="flex items-center gap-1"><span className="w-6 h-0.5 bg-amber-400 inline-block border-t border-dashed border-amber-400" /> Amber (70+)</span>
          </div>
        </div>

        {/* Recommended actions */}
        <div className="card-padded">
          <h3 className="font-heading font-semibold text-wordie-dark-teal mb-1">Recommended actions</h3>
          <p className="text-xs text-gray-400 font-body mb-4">Based on current health data</p>
          <RecommendedActions
            actions={[
              {
                priority: 'MEDIUM',
                category: 'Performance',
                action: 'Schedule image optimisation and defer analytics script load.',
                business_impact: 'Expected +12–15 PageSpeed points. Improves search ranking and reduces bounce rate.',
              },
              {
                priority: 'MEDIUM',
                category: 'Updates',
                action: 'Apply 4 pending plugin updates in next maintenance window.',
                business_impact: 'Reduces security exposure from outdated plugin vulnerabilities.',
              },
            ]}
          />
        </div>
      </div>

      {/* Incidents + Deployments row */}
      <div className="grid lg:grid-cols-2 gap-6">
        <div className="card-padded">
          <h3 className="font-heading font-semibold text-wordie-dark-teal mb-1">Recent incidents</h3>
          <p className="text-xs text-gray-400 font-body mb-4">Last 30 days</p>
          <AlertFeed incidents={data.recent_incidents} />
        </div>

        <div className="card-padded">
          <h3 className="font-heading font-semibold text-wordie-dark-teal mb-1">Recent deployments</h3>
          <p className="text-xs text-gray-400 font-body mb-4">Production deployments</p>
          <DeploymentTimeline deployments={data.recent_deployments} />
        </div>
      </div>

      {/* Footer note */}
      <p className="mt-8 text-xs text-gray-400 font-body text-center no-print">
        Data last refreshed: {new Date(data.snapshot_date).toLocaleDateString('en-AU', { day: 'numeric', month: 'long', year: 'numeric' })}.
        This dashboard updates daily. Contact <a href="mailto:support@wordie.com.au" className="underline hover:text-wordie-accent-teal">support@wordie.com.au</a> with questions.
      </p>
    </div>
  );
}
