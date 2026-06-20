export type TrafficLight = 'GREEN' | 'AMBER' | 'RED';
export type Trend = 'IMPROVING' | 'STABLE' | 'DECLINING';

export interface Site {
  id: string;
  slug: string;
  client_name: string;
  domain: string;
  logo_url?: string;
  active: boolean;
  health_score: number;
  traffic_light: TrafficLight;
  last_snapshot: string;
  pagespeed_mobile: number;
  uptime_pct_30d: number;
  pending_updates: number;
  backup_status: 'SUCCESS' | 'FAILED' | 'UNKNOWN';
  critical_vulns: number;
}

export interface DashboardData {
  site: Site;
  snapshot_date: string;
  health_score: number;
  health_score_delta: number | null;
  overall_status: TrafficLight;
  traffic_lights: Record<string, TrafficLight>;
  key_metrics: {
    pagespeed_mobile: number;
    uptime_pct_30d: number;
    pending_updates: number;
    backup_status: 'SUCCESS' | 'FAILED' | 'UNKNOWN';
    critical_vulns: number;
    blocked_attacks: number;
    deploy_count_7d: number;
    failed_builds_7d: number;
  };
  category_scores: {
    performance: number;
    security: number;
    reliability: number;
    updates: number;
    backups: number;
    engineering: number;
  };
  recent_incidents: Incident[];
  recent_deployments: Deployment[];
}

export interface Incident {
  id: string;
  type: 'DOWNTIME' | 'SECURITY' | 'PERFORMANCE' | 'BACKUP_FAILURE';
  severity: 'CRITICAL' | 'HIGH' | 'MEDIUM' | 'LOW';
  title: string;
  description?: string;
  started_at: string;
  resolved_at?: string;
  duration_minutes?: number;
  impact_summary?: string;
}

export interface Deployment {
  id: string;
  deployed_at: string;
  environment: 'PRODUCTION' | 'STAGING';
  status: 'SUCCESS' | 'FAILED' | 'ROLLED_BACK';
  commit_sha?: string;
  commit_message?: string;
  branch?: string;
  pr_title?: string;
  deployed_by?: string;
  is_hotfix: boolean;
}

export interface MonthlyReport {
  id: string;
  website_id: string;
  report_month: string;
  health_score: number;
  health_score_prev?: number;
  overall_status: TrafficLight;
  client_name: string;
  domain: string;
  logo_url?: string;
  report_json: ReportDocument;
  generated_at: string;
}

export interface ReportDocument {
  report_month: string;
  generated_at: string;
  executive_summary: ExecutiveSummary;
  performance: ReportSection;
  security: ReportSection;
  reliability: ReportSection;
  updates: ReportSection;
  backups: ReportSection;
  engineering: ReportSection;
  recommended_actions: RecommendedAction[];
}

export interface ExecutiveSummary {
  health_score: number;
  health_score_prev?: number;
  overall_status: TrafficLight;
  key_risks: string[];
  highlights: string[];
  month_summary: string;
}

export interface ReportSection {
  status: TrafficLight;
  score: number;
  headline: string;
  interpretation: string;
  risk: string;
  recommended_action: string | null;
  trend: Trend;
  metrics: ReportMetric[];
}

export interface ReportMetric {
  key: string;
  label: string;
  value: string | number;
  unit?: string;
  status: TrafficLight;
  description: string;
}

export interface RecommendedAction {
  priority: 'HIGH' | 'MEDIUM' | 'LOW';
  category: string;
  action: string;
  business_impact: string;
}

export interface TrendPoint {
  snapshot_date: string;
  health_score: number;
  performance_score: number;
  security_score: number;
  reliability_score: number;
  pagespeed_mobile: number;
  uptime_pct_30d: number;
}
