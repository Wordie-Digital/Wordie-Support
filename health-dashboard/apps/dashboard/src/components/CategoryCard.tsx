import clsx from 'clsx';
import type { TrafficLight, Trend } from '@/lib/types';
import { TrafficLightDot } from './TrafficLight';

interface Props {
  title: string;
  status: TrafficLight;
  score: number;
  headline: string;
  trend?: Trend;
  weight?: string;
  icon?: string;
}

const trendIcon: Record<Trend, string> = {
  IMPROVING: '↑',
  STABLE:    '→',
  DECLINING: '↓',
};

const trendColor: Record<Trend, string> = {
  IMPROVING: 'text-emerald-600',
  STABLE:    'text-gray-400',
  DECLINING: 'text-red-500',
};

const borderColor: Record<TrafficLight, string> = {
  GREEN: 'border-l-emerald-400',
  AMBER: 'border-l-amber-400',
  RED:   'border-l-red-500',
};

const barColor: Record<TrafficLight, string> = {
  GREEN: 'bg-emerald-400',
  AMBER: 'bg-amber-400',
  RED:   'bg-red-500',
};

export function CategoryCard({ title, status, score, headline, trend = 'STABLE', weight, icon }: Props) {
  return (
    <div className={clsx(
      'card border-l-4 p-5 hover:shadow-md transition-shadow',
      borderColor[status],
    )}>
      <div className="flex items-start justify-between mb-3">
        <div className="flex items-center gap-2">
          {icon && <span className="text-xl">{icon}</span>}
          <div>
            <h4 className="font-heading font-semibold text-sm text-wordie-dark-teal">{title}</h4>
            {weight && <p className="text-xs text-gray-400 font-body">{weight} of score</p>}
          </div>
        </div>
        <TrafficLightDot status={status} size="md" />
      </div>

      {/* Score bar */}
      <div className="mb-3">
        <div className="flex items-center justify-between mb-1">
          <span className="text-xs text-gray-500 font-body">Score</span>
          <span className="text-sm font-heading font-semibold text-wordie-dark-teal">{score}/100</span>
        </div>
        <div className="h-2 bg-gray-100 rounded-full overflow-hidden">
          <div
            className={clsx('h-full rounded-full transition-all duration-700', barColor[status])}
            style={{ width: `${score}%` }}
          />
        </div>
      </div>

      <p className="text-sm text-gray-600 font-body leading-snug">{headline}</p>

      {trend && (
        <div className={clsx('flex items-center gap-1 mt-2 text-xs font-semibold', trendColor[trend])}>
          <span>{trendIcon[trend]}</span>
          <span className="capitalize">{trend.toLowerCase()}</span>
        </div>
      )}
    </div>
  );
}
