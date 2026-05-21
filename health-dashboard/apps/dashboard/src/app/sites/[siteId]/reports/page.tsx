/**
 * Report archive — list of all monthly reports for a site.
 */

import Link from 'next/link';
import clsx from 'clsx';
import { TrafficLightBadge } from '@/components/TrafficLight';
import { MOCK_REPORT, BARNARDOS_REPORT } from '@/lib/mock-data';
import type { TrafficLight } from '@/lib/types';

const BARNARDOS_ARCHIVE = [
  { report_month: '2026-05', health_score: 62, health_score_prev: 60, overall_status: 'RED'  as TrafficLight, generated_at: '2026-05-07T10:00:00Z' },
  { report_month: '2026-04', health_score: 60, health_score_prev: 59, overall_status: 'RED'  as TrafficLight, generated_at: '2026-05-01T06:00:00Z' },
  { report_month: '2026-03', health_score: 59, health_score_prev: 58, overall_status: 'RED'  as TrafficLight, generated_at: '2026-04-01T06:00:00Z' },
];

const MOCK_ARCHIVE = [
  { report_month: '2026-04', health_score: 83, health_score_prev: 79, overall_status: 'AMBER' as TrafficLight, generated_at: '2026-05-01T06:00:00Z' },
  { report_month: '2026-03', health_score: 79, health_score_prev: 75, overall_status: 'AMBER' as TrafficLight, generated_at: '2026-04-01T06:00:00Z' },
  { report_month: '2026-02', health_score: 75, health_score_prev: 81, overall_status: 'AMBER' as TrafficLight, generated_at: '2026-03-01T06:00:00Z' },
  { report_month: '2026-01', health_score: 81, health_score_prev: 82, overall_status: 'GREEN' as TrafficLight, generated_at: '2026-02-01T06:00:00Z' },
  { report_month: '2025-12', health_score: 82, health_score_prev: 78, overall_status: 'GREEN' as TrafficLight, generated_at: '2026-01-01T06:00:00Z' },
];

function formatMonth(ym: string) {
  const [y, m] = ym.split('-');
  return new Date(parseInt(y), parseInt(m) - 1, 1).toLocaleDateString('en-AU', { month: 'long', year: 'numeric' });
}

export default function ReportsArchivePage({ params }: { params: { siteId: string } }) {
  const isBarnardos = params.siteId === 'barnardos';
  const archive = isBarnardos ? BARNARDOS_ARCHIVE : MOCK_ARCHIVE;
  const clientReport = isBarnardos ? BARNARDOS_REPORT : MOCK_REPORT;
  return (
    <div>
      <nav className="mb-6 text-sm font-body text-gray-400 flex items-center gap-2 no-print">
        <Link href="/" className="hover:text-wordie-accent-teal">All sites</Link>
        <span>/</span>
        <Link href={`/sites/${params.siteId}`} className="hover:text-wordie-accent-teal">
          {clientReport.client_name}
        </Link>
        <span>/</span>
        <span className="text-wordie-dark-teal font-semibold">Monthly reports</span>
      </nav>

      <div className="mb-8">
        <h1 className="font-heading font-bold text-h2 text-wordie-dark-teal mb-1">Monthly reports</h1>
        <p className="text-gray-500 font-body">{clientReport.client_name} — {clientReport.domain}</p>
      </div>

      <div className="space-y-3">
        {archive.map((report, i) => {
          const delta = report.health_score_prev != null
            ? report.health_score - report.health_score_prev
            : null;

          return (
            <Link
              key={report.report_month}
              href={`/sites/${params.siteId}/reports/${report.report_month}`}
              className="card block hover:shadow-md transition-all hover:-translate-y-0.5 group"
            >
              <div className="p-5 flex items-center justify-between gap-4">
                <div className="flex items-center gap-4">
                  {i === 0 && (
                    <span className="text-xs bg-wordie-accent-teal text-white px-2 py-0.5 rounded-full font-semibold font-heading hidden sm:block">
                      Latest
                    </span>
                  )}
                  <div>
                    <p className="font-heading font-semibold text-wordie-dark-teal group-hover:text-wordie-accent-teal transition-colors">
                      {formatMonth(report.report_month)}
                    </p>
                    <p className="text-xs text-gray-400 font-body mt-0.5">
                      Generated {new Date(report.generated_at).toLocaleDateString('en-AU', { day: 'numeric', month: 'short', year: 'numeric' })}
                    </p>
                  </div>
                </div>

                <div className="flex items-center gap-4">
                  <div className="text-right">
                    <p className="text-2xl font-heading font-bold text-wordie-dark-teal leading-none">
                      {report.health_score}
                    </p>
                    {delta != null && (
                      <p className={clsx(
                        'text-xs font-body',
                        delta > 0 ? 'text-emerald-600' : delta < 0 ? 'text-red-500' : 'text-gray-400',
                      )}>
                        {delta > 0 ? `+${delta}` : delta} vs prev
                      </p>
                    )}
                  </div>
                  <TrafficLightBadge status={report.overall_status} />
                  <span className="text-gray-300 group-hover:text-wordie-accent-teal transition-colors">→</span>
                </div>
              </div>
            </Link>
          );
        })}
      </div>
    </div>
  );
}
