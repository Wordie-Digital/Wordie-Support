'use client';

import {
  LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip,
  ResponsiveContainer, ReferenceLine,
} from 'recharts';
import { format, parseISO } from 'date-fns';
import type { TrendPoint } from '@/lib/types';

interface Props {
  data: TrendPoint[];
  metric?: 'health_score' | 'pagespeed_mobile' | 'performance_score' | 'security_score';
  height?: number;
}

const metricLabel: Record<string, string> = {
  health_score:       'Health Score',
  pagespeed_mobile:   'PageSpeed (Mobile)',
  performance_score:  'Performance',
  security_score:     'Security',
};

export function TrendChart({ data, metric = 'health_score', height = 180 }: Props) {
  const chartData = data.map(d => ({
    date:  format(parseISO(d.snapshot_date), 'd MMM'),
    value: d[metric],
  }));

  return (
    <div style={{ height }}>
      <ResponsiveContainer width="100%" height="100%">
        <LineChart data={chartData} margin={{ top: 8, right: 8, left: -20, bottom: 0 }}>
          <CartesianGrid strokeDasharray="3 3" stroke="#f1f5f9" />
          <XAxis
            dataKey="date"
            tick={{ fontSize: 11, fill: '#94a3b8', fontFamily: 'Source Sans Pro' }}
            tickLine={false}
            axisLine={false}
          />
          <YAxis
            domain={[0, 100]}
            tick={{ fontSize: 11, fill: '#94a3b8', fontFamily: 'Source Sans Pro' }}
            tickLine={false}
            axisLine={false}
            ticks={[0, 25, 50, 70, 90, 100]}
          />
          <Tooltip
            contentStyle={{
              background: '#0A3542',
              border: 'none',
              borderRadius: 8,
              color: '#F6F9F9',
              fontSize: 12,
              fontFamily: 'Source Sans Pro',
            }}
            labelStyle={{ color: '#B9DDDD', marginBottom: 4 }}
            formatter={(v: number) => [`${v}`, metricLabel[metric]]}
          />
          {/* Green zone line */}
          <ReferenceLine y={90} stroke="#10b981" strokeDasharray="4 4" strokeWidth={1} />
          {/* Amber zone line */}
          <ReferenceLine y={70} stroke="#f59e0b" strokeDasharray="4 4" strokeWidth={1} />
          <Line
            type="monotone"
            dataKey="value"
            stroke="#116E6E"
            strokeWidth={2.5}
            dot={{ fill: '#116E6E', r: 4, strokeWidth: 0 }}
            activeDot={{ r: 6, fill: '#F5634D', strokeWidth: 0 }}
          />
        </LineChart>
      </ResponsiveContainer>
    </div>
  );
}
