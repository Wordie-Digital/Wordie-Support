import clsx from 'clsx';
import type { RecommendedAction } from '@/lib/types';

interface Props {
  actions: RecommendedAction[];
}

const priorityStyles: Record<string, string> = {
  HIGH:   'bg-red-50 border-red-200 text-red-700',
  MEDIUM: 'bg-amber-50 border-amber-200 text-amber-700',
  LOW:    'bg-gray-50 border-gray-200 text-gray-600',
};

const priorityDot: Record<string, string> = {
  HIGH:   'bg-red-500',
  MEDIUM: 'bg-amber-400',
  LOW:    'bg-gray-300',
};

export function RecommendedActions({ actions }: Props) {
  if (!actions.length) {
    return (
      <div className="flex items-center gap-3 p-4 bg-emerald-50 rounded-lg border border-emerald-200">
        <span className="text-2xl">✓</span>
        <div>
          <p className="font-semibold text-emerald-700 text-sm">No actions required</p>
          <p className="text-xs text-emerald-600 font-body">Everything is running well this month.</p>
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-3">
      {actions.map((action, i) => (
        <div key={i} className={clsx(
          'rounded-lg border p-4',
          priorityStyles[action.priority],
        )}>
          <div className="flex items-start gap-3">
            <div className="flex-shrink-0 mt-1 flex items-center gap-1.5">
              <span className={clsx('w-2.5 h-2.5 rounded-full', priorityDot[action.priority])} />
              <span className="text-xs font-semibold uppercase tracking-wide">{action.priority}</span>
            </div>
            <div className="flex-1">
              <p className="text-sm font-semibold font-heading mb-0.5">{action.action}</p>
              <p className="text-xs font-body opacity-80">{action.business_impact}</p>
            </div>
          </div>
        </div>
      ))}
    </div>
  );
}
