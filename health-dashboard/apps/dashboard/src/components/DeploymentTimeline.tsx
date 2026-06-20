import clsx from 'clsx';
import { format, parseISO } from 'date-fns';
import type { Deployment } from '@/lib/types';

interface Props {
  deployments: Deployment[];
}

const statusDot: Record<string, string> = {
  SUCCESS:     'bg-emerald-500',
  FAILED:      'bg-red-500',
  ROLLED_BACK: 'bg-amber-500',
};

const statusLabel: Record<string, string> = {
  SUCCESS:     'Deployed',
  FAILED:      'Failed',
  ROLLED_BACK: 'Rolled back',
};

export function DeploymentTimeline({ deployments }: Props) {
  if (!deployments.length) {
    return <p className="text-sm text-gray-400 font-body py-4 text-center">No deployments this period</p>;
  }

  return (
    <div className="space-y-3">
      {deployments.map((d, i) => (
        <div key={d.id} className="flex gap-3 items-start">
          <div className="flex flex-col items-center flex-shrink-0 mt-1">
            <span className={clsx('w-3 h-3 rounded-full', statusDot[d.status])} />
            {i < deployments.length - 1 && (
              <span className="w-px flex-1 bg-gray-200 mt-1" style={{ minHeight: 20 }} />
            )}
          </div>
          <div className="flex-1 min-w-0 pb-2">
            <div className="flex items-start justify-between gap-2">
              <div className="min-w-0">
                <p className="text-sm font-semibold text-wordie-dark-teal truncate font-heading">
                  {d.pr_title ?? d.commit_message ?? 'Deployment'}
                </p>
                <div className="flex items-center gap-2 mt-0.5 flex-wrap">
                  {d.commit_sha && (
                    <code className="text-xs bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded">
                      {d.commit_sha}
                    </code>
                  )}
                  {d.is_hotfix && (
                    <span className="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-semibold border border-red-200">
                      Hotfix
                    </span>
                  )}
                </div>
              </div>
              <div className="text-right flex-shrink-0">
                <span className={clsx(
                  'text-xs font-semibold',
                  d.status === 'SUCCESS' ? 'text-emerald-600' :
                  d.status === 'FAILED' ? 'text-red-600' : 'text-amber-600',
                )}>
                  {statusLabel[d.status]}
                </span>
                <p className="text-xs text-gray-400 mt-0.5">
                  {format(parseISO(d.deployed_at), 'd MMM, HH:mm')}
                </p>
              </div>
            </div>
          </div>
        </div>
      ))}
    </div>
  );
}
