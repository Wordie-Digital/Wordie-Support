import clsx from 'clsx';
import { format, parseISO } from 'date-fns';
import type { Incident } from '@/lib/types';

interface Props {
  incidents: Incident[];
}

const severityColor: Record<string, string> = {
  CRITICAL: 'bg-red-100 text-red-700 border-red-200',
  HIGH:     'bg-orange-100 text-orange-700 border-orange-200',
  MEDIUM:   'bg-amber-100 text-amber-700 border-amber-200',
  LOW:      'bg-gray-100 text-gray-600 border-gray-200',
};

const typeIcon: Record<string, string> = {
  DOWNTIME:       '⬇',
  SECURITY:       '🔒',
  PERFORMANCE:    '⚡',
  BACKUP_FAILURE: '💾',
};

export function AlertFeed({ incidents }: Props) {
  if (!incidents.length) {
    return (
      <div className="flex flex-col items-center justify-center py-8 text-center">
        <div className="w-10 h-10 bg-emerald-50 rounded-full flex items-center justify-center mb-3">
          <span className="text-emerald-500 text-lg">✓</span>
        </div>
        <p className="text-sm font-semibold text-emerald-700">No incidents this period</p>
        <p className="text-xs text-gray-400 mt-1">Your site has been running smoothly</p>
      </div>
    );
  }

  return (
    <div className="space-y-3">
      {incidents.map(incident => (
        <div key={incident.id} className="flex gap-3 p-3 bg-gray-50 rounded-lg">
          <div className="text-lg flex-shrink-0 mt-0.5">{typeIcon[incident.type] ?? '⚠'}</div>
          <div className="flex-1 min-w-0">
            <div className="flex items-start justify-between gap-2 mb-1">
              <p className="text-sm font-semibold text-wordie-dark-teal leading-snug">{incident.title}</p>
              <span className={clsx(
                'text-xs px-2 py-0.5 rounded-full border flex-shrink-0 font-semibold',
                severityColor[incident.severity],
              )}>
                {incident.severity}
              </span>
            </div>
            {incident.impact_summary && (
              <p className="text-xs text-gray-600 font-body leading-snug mb-1.5">{incident.impact_summary}</p>
            )}
            <div className="flex items-center gap-3 text-xs text-gray-400">
              <span>{format(parseISO(incident.started_at), 'd MMM yyyy, HH:mm')}</span>
              {incident.resolved_at ? (
                <>
                  <span>·</span>
                  <span className="text-emerald-600 font-semibold">Resolved</span>
                  {incident.duration_minutes && <span>· {incident.duration_minutes}m</span>}
                </>
              ) : (
                <span className="text-red-600 font-semibold animate-pulse">Active</span>
              )}
            </div>
          </div>
        </div>
      ))}
    </div>
  );
}
