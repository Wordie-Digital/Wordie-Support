import type { TrafficLight } from '@/lib/types';
import clsx from 'clsx';

interface Props {
  status: TrafficLight;
  size?: 'sm' | 'md' | 'lg';
  showLabel?: boolean;
  pulse?: boolean;
}

const sizeMap = {
  sm: 'w-3 h-3',
  md: 'w-4 h-4',
  lg: 'w-5 h-5',
};

const colorMap: Record<TrafficLight, string> = {
  GREEN: 'bg-emerald-500',
  AMBER: 'bg-amber-400',
  RED:   'bg-red-500',
};

const labelMap: Record<TrafficLight, string> = {
  GREEN: 'Good',
  AMBER: 'Attention needed',
  RED:   'Action required',
};

const textColorMap: Record<TrafficLight, string> = {
  GREEN: 'text-emerald-700',
  AMBER: 'text-amber-700',
  RED:   'text-red-700',
};

export function TrafficLightDot({ status, size = 'md', showLabel = false, pulse = false }: Props) {
  return (
    <span className="inline-flex items-center gap-2">
      <span className={clsx(
        'rounded-full flex-shrink-0',
        sizeMap[size],
        colorMap[status],
        pulse && status === 'RED' && 'animate-pulse',
      )} />
      {showLabel && (
        <span className={clsx('text-sm font-semibold', textColorMap[status])}>
          {labelMap[status]}
        </span>
      )}
    </span>
  );
}

interface BadgeProps {
  status: TrafficLight;
  label?: string;
}

const badgeClasses: Record<TrafficLight, string> = {
  GREEN: 'bg-emerald-50 text-emerald-700 border-emerald-200',
  AMBER: 'bg-amber-50  text-amber-700  border-amber-200',
  RED:   'bg-red-50    text-red-700    border-red-200',
};

export function TrafficLightBadge({ status, label }: BadgeProps) {
  return (
    <span className={clsx(
      'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border',
      badgeClasses[status],
    )}>
      <span className={clsx('w-2 h-2 rounded-full flex-shrink-0', colorMap[status])} />
      {label ?? labelMap[status]}
    </span>
  );
}
