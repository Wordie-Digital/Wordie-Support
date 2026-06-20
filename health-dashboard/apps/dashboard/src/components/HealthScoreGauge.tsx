'use client';

import clsx from 'clsx';
import type { TrafficLight } from '@/lib/types';

interface Props {
  score: number;
  status: TrafficLight;
  delta?: number | null;
  size?: 'sm' | 'lg';
}

const statusGradient: Record<TrafficLight, string> = {
  GREEN: 'from-emerald-400 to-emerald-600',
  AMBER: 'from-amber-300 to-amber-500',
  RED:   'from-red-400 to-red-600',
};

const statusText: Record<TrafficLight, string> = {
  GREEN: 'text-emerald-600',
  AMBER: 'text-amber-600',
  RED:   'text-red-600',
};

const statusLabel: Record<TrafficLight, string> = {
  GREEN: 'Healthy',
  AMBER: 'Needs attention',
  RED:   'Action required',
};

export function HealthScoreGauge({ score, status, delta, size = 'lg' }: Props) {
  const radius = size === 'lg' ? 54 : 38;
  const stroke = size === 'lg' ? 10 : 7;
  const svgSize = (radius + stroke) * 2 + 4;
  const circumference = 2 * Math.PI * radius;
  const progress = circumference - (score / 100) * circumference;

  const strokeColor =
    status === 'GREEN' ? '#10b981' :
    status === 'AMBER' ? '#f59e0b' : '#ef4444';

  return (
    <div className="flex flex-col items-center gap-2">
      <div className="relative" style={{ width: svgSize, height: svgSize }}>
        <svg
          width={svgSize}
          height={svgSize}
          viewBox={`0 0 ${svgSize} ${svgSize}`}
          className="-rotate-90"
        >
          {/* Track */}
          <circle
            cx={svgSize / 2}
            cy={svgSize / 2}
            r={radius}
            fill="none"
            stroke="#e5e7eb"
            strokeWidth={stroke}
          />
          {/* Progress */}
          <circle
            cx={svgSize / 2}
            cy={svgSize / 2}
            r={radius}
            fill="none"
            stroke={strokeColor}
            strokeWidth={stroke}
            strokeDasharray={circumference}
            strokeDashoffset={progress}
            strokeLinecap="round"
            style={{ transition: 'stroke-dashoffset 0.8s ease-in-out' }}
          />
        </svg>

        {/* Score text */}
        <div className="absolute inset-0 flex flex-col items-center justify-center">
          <span className={clsx(
            'font-heading font-bold leading-none',
            size === 'lg' ? 'text-4xl' : 'text-2xl',
            statusText[status],
          )}>
            {score}
          </span>
          <span className="text-xs text-gray-400 font-body mt-0.5">/100</span>
        </div>
      </div>

      <div className="text-center">
        <p className={clsx('font-heading font-semibold text-sm', statusText[status])}>
          {statusLabel[status]}
        </p>
        {delta != null && (
          <p className={clsx(
            'text-xs font-body mt-0.5',
            delta > 0 ? 'text-emerald-600' : delta < 0 ? 'text-red-500' : 'text-gray-400',
          )}>
            {delta > 0 ? `+${delta}` : delta} vs last month
          </p>
        )}
      </div>
    </div>
  );
}
