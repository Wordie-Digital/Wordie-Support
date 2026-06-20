/**
 * Full monthly report — print-ready, client-facing.
 * Structured as an executive document with sections per category.
 */

'use client';

import Link from 'next/link';
import clsx from 'clsx';
import { HealthScoreGauge } from '@/components/HealthScoreGauge';
import { TrafficLightBadge, TrafficLightDot } from '@/components/TrafficLight';
import { RecommendedActions } from '@/components/RecommendedActions';
import { MOCK_REPORT, BARNARDOS_REPORT } from '@/lib/mock-data';
import type { ReportSection, TrafficLight, Trend } from '@/lib/types';

function formatMonth(ym: string) {
  const [y, m] = ym.split('-');
  return new Date(parseInt(y), parseInt(m) - 1, 1).toLocaleDateString('en-AU', { month: 'long', year: 'numeric' });
}

const trendLabel: Record<Trend, string> = {
  IMPROVING: '↑ Improving',
  STABLE:    '→ Stable',
  DECLINING: '↓ Declining',
};

const trendColor: Record<Trend, string> = {
  IMPROVING: 'text-emerald-600',
  STABLE:    'text-gray-500',
  DECLINING: 'text-red-500',
};

const metricStatusBg: Record<TrafficLight, string> = {
  GREEN: 'bg-emerald-50',
  AMBER: 'bg-amber-50',
  RED:   'bg-red-50',
};

const metricStatusText: Record<TrafficLight, string> = {
  GREEN: 'text-emerald-700',
  AMBER: 'text-amber-700',
  RED:   'text-red-700',
};

function ReportSectionBlock({
  title,
  icon,
  section,
}: {
  title: string;
  icon: string;
  section: ReportSection;
}) {
  return (
    <div className="card overflow-hidden">
      {/* Section header */}
      <div className={clsx(
        'px-6 py-4 flex items-center justify-between border-b',
        section.status === 'GREEN' ? 'bg-emerald-50 border-emerald-100' :
        section.status === 'AMBER' ? 'bg-amber-50 border-amber-100' :
        'bg-red-50 border-red-100',
      )}>
        <div className="flex items-center gap-3">
          <span className="text-xl">{icon}</span>
          <div>
            <h3 className="font-heading font-semibold text-wordie-dark-teal">{title}</h3>
            <p className={clsx('text-xs font-semibold', trendColor[section.trend])}>
              {trendLabel[section.trend]}
            </p>
          </div>
        </div>
        <div className="flex items-center gap-3">
          <div className="text-right hidden sm:block">
            <p className="text-xl font-heading font-bold text-wordie-dark-teal">{section.score}</p>
            <p className="text-xs text-gray-400">/100</p>
          </div>
          <TrafficLightBadge status={section.status} />
        </div>
      </div>

      <div className="p-6">
        {/* Headline */}
        <p className="font-heading font-semibold text-wordie-dark-teal mb-2">{section.headline}</p>

        {/* Plain English interpretation */}
        <p className="text-sm text-gray-600 font-body leading-relaxed mb-4">{section.interpretation}</p>

        {/* Metrics grid */}
        <div className="grid sm:grid-cols-2 gap-3 mb-4">
          {section.metrics.map(metric => (
            <div key={metric.key} className={clsx('rounded-lg p-3', metricStatusBg[metric.status])}>
              <div className="flex items-start justify-between gap-2 mb-1">
                <span className="text-xs font-semibold text-gray-500 font-heading uppercase tracking-wide">
                  {metric.label}
                </span>
                <TrafficLightDot status={metric.status} size="sm" />
              </div>
              <p className={clsx('text-lg font-heading font-bold leading-none', metricStatusText[metric.status])}>
                {metric.value}{metric.unit}
              </p>
              <p className="text-xs text-gray-500 font-body mt-1 leading-snug">{metric.description}</p>
            </div>
          ))}
        </div>

        {/* Risk */}
        <div className="bg-gray-50 rounded-lg p-3 mb-3">
          <p className="text-xs font-semibold text-gray-500 uppercase tracking-wide font-heading mb-1">Business risk</p>
          <p className="text-sm text-gray-700 font-body">{section.risk}</p>
        </div>

        {/* Action */}
        {section.recommended_action && (
          <div className={clsx(
            'rounded-lg p-3 border',
            section.status === 'RED' ? 'bg-red-50 border-red-200' : 'bg-amber-50 border-amber-200',
          )}>
            <p className={clsx(
              'text-xs font-semibold uppercase tracking-wide font-heading mb-1',
              section.status === 'RED' ? 'text-red-600' : 'text-amber-600',
            )}>
              Recommended action
            </p>
            <p className="text-sm font-body text-gray-700">{section.recommended_action}</p>
          </div>
        )}
      </div>
    </div>
  );
}

export default function MonthlyReportPage({ params }: { params: { siteId: string; month: string } }) {
  const report = params.siteId === 'barnardos' ? BARNARDOS_REPORT : MOCK_REPORT;
  const doc = report.report_json;
  const delta = report.health_score_prev != null ? report.health_score - report.health_score_prev : null;

  return (
    <div className="max-w-4xl mx-auto">
      {/* Navigation */}
      <nav className="mb-6 flex items-center justify-between no-print">
        <div className="flex items-center gap-2 text-sm font-body text-gray-400">
          <Link href="/" className="hover:text-wordie-accent-teal">All sites</Link>
          <span>/</span>
          <Link href={`/sites/${params.siteId}`} className="hover:text-wordie-accent-teal">{report.client_name}</Link>
          <span>/</span>
          <Link href={`/sites/${params.siteId}/reports`} className="hover:text-wordie-accent-teal">Reports</Link>
          <span>/</span>
          <span className="text-wordie-dark-teal font-semibold">{formatMonth(params.month)}</span>
        </div>
        <button
          onClick={() => window.print()}
          className="btn-secondary text-sm no-print"
        >
          Print / Export PDF
        </button>
      </nav>

      {/* Report cover */}
      <div className="bg-wordie-dark-teal rounded-2xl p-8 mb-6 text-white relative overflow-hidden">
        {/* Background diagonal shapes */}
        <div className="absolute inset-0 opacity-5">
          <div className="absolute top-0 right-0 w-64 h-64 bg-wordie-light-green rounded-full transform translate-x-20 -translate-y-20" />
          <div className="absolute bottom-0 left-0 w-48 h-48 bg-wordie-accent-teal rounded-full transform -translate-x-10 translate-y-10" />
        </div>

        <div className="relative flex flex-col sm:flex-row sm:items-start sm:justify-between gap-6">
          <div className="flex-1">
            <p className="text-wordie-light-green/60 text-xs font-heading font-semibold uppercase tracking-widest mb-2">
              Monthly Health Report
            </p>
            <h1 className="font-heading font-bold text-h2 text-wordie-off-white mb-1">
              {report.client_name}
            </h1>
            <p className="text-wordie-light-green/70 font-body text-sm mb-1">{report.domain}</p>
            <p className="text-wordie-light-green font-heading font-semibold text-lg">
              {formatMonth(params.month)}
            </p>

            <div className="mt-6 p-4 bg-white/10 rounded-xl max-w-md">
              <p className="text-wordie-off-white/90 text-sm font-body leading-relaxed">
                {doc.executive_summary.month_summary}
              </p>
            </div>
          </div>

          <div className="flex-shrink-0 flex flex-col items-center">
            <HealthScoreGauge
              score={report.health_score}
              status={report.overall_status}
              delta={delta}
              size="lg"
            />
          </div>
        </div>
      </div>

      {/* Executive summary */}
      <div className="card-padded mb-6">
        <h2 className="font-heading font-bold text-h4 text-wordie-dark-teal mb-4">Executive summary</h2>

        <div className="grid sm:grid-cols-2 gap-6">
          {doc.executive_summary.highlights.length > 0 && (
            <div>
              <p className="text-xs font-semibold text-emerald-600 uppercase tracking-wide font-heading mb-3">
                Highlights this month
              </p>
              <ul className="space-y-2">
                {doc.executive_summary.highlights.map((h, i) => (
                  <li key={i} className="flex items-start gap-2 text-sm text-gray-700 font-body">
                    <span className="text-emerald-500 flex-shrink-0 mt-0.5">✓</span>
                    {h}
                  </li>
                ))}
              </ul>
            </div>
          )}

          {doc.executive_summary.key_risks.length > 0 && (
            <div>
              <p className="text-xs font-semibold text-amber-600 uppercase tracking-wide font-heading mb-3">
                Areas to address
              </p>
              <ul className="space-y-2">
                {doc.executive_summary.key_risks.map((r, i) => (
                  <li key={i} className="flex items-start gap-2 text-sm text-gray-700 font-body">
                    <span className="text-amber-500 flex-shrink-0 mt-0.5">→</span>
                    {r}
                  </li>
                ))}
              </ul>
            </div>
          )}
        </div>
      </div>

      {/* Category sections */}
      <h2 className="font-heading font-bold text-h4 text-wordie-dark-teal mb-4">Detailed breakdown</h2>

      <div className="space-y-4 mb-8">
        <ReportSectionBlock title="Performance"  icon="⚡" section={doc.performance}  />
        <ReportSectionBlock title="Security"     icon="🔒" section={doc.security}     />
        <ReportSectionBlock title="Reliability"  icon="📡" section={doc.reliability}  />
        <ReportSectionBlock title="Updates"      icon="🔄" section={doc.updates}      />
        <ReportSectionBlock title="Backups"      icon="💾" section={doc.backups}      />
        <ReportSectionBlock title="Engineering"  icon="⚙️"  section={doc.engineering}  />
      </div>

      {/* Recommended actions */}
      <div className="card-padded mb-8">
        <h2 className="font-heading font-bold text-h4 text-wordie-dark-teal mb-1">Recommended actions</h2>
        <p className="text-sm text-gray-400 font-body mb-4">
          Prioritised list of actions for this month
        </p>
        <RecommendedActions actions={doc.recommended_actions} />
      </div>

      {/* Report footer */}
      <div className="text-center py-8 border-t border-gray-200">
        <p className="text-xs text-gray-400 font-body">
          This report was generated by Wordie on{' '}
          {new Date(report.generated_at).toLocaleDateString('en-AU', { day: 'numeric', month: 'long', year: 'numeric' })}.
        </p>
        <p className="text-xs text-gray-400 font-body mt-1">
          Questions? Contact us at{' '}
          <a href="mailto:support@wordie.com.au" className="text-wordie-accent-teal hover:underline">
            support@wordie.com.au
          </a>
        </p>
        <p className="text-xs text-wordie-dark-teal font-heading font-semibold mt-4">
          WordPress, done properly.
        </p>
      </div>
    </div>
  );
}
