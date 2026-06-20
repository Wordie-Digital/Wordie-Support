/**
 * GitHub API adapter.
 * Collects deployment, PR, and security alert data via REST API.
 */

import axios, { AxiosInstance } from 'axios';

export interface GitHubMetrics {
  deploys_30d: number;
  failed_builds_30d: number;
  hotfixes_30d: number;
  open_prs: number;
  merged_prs_30d: number;
  open_dependabot_alerts: number;
  open_dependabot_prs: number;
  last_deploy_at: Date | null;
  recent_deployments: Array<{
    deployed_at: Date;
    environment: string;
    status: string;
    commit_sha: string;
    commit_message: string;
    branch: string;
    is_hotfix: boolean;
    run_id: string;
  }>;
}

export class GitHubService {
  private client: AxiosInstance;

  constructor(token: string) {
    this.client = axios.create({
      baseURL: 'https://api.github.com',
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: 'application/vnd.github+json',
        'X-GitHub-Api-Version': '2022-11-28',
      },
      timeout: 15000,
    });
  }

  async getRepoMetrics(repo: string): Promise<GitHubMetrics> {
    const [org, repoName] = repo.split('/');
    const since30d = new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString();

    const [runsRes, prsRes, alertsRes] = await Promise.allSettled([
      this.client.get(`/repos/${org}/${repoName}/actions/runs?per_page=100&created=>=${since30d}`),
      this.client.get(`/repos/${org}/${repoName}/pulls?state=all&per_page=100&sort=updated&direction=desc`),
      this.client.get(`/repos/${org}/${repoName}/vulnerability-alerts`),
    ]);

    const runs   = runsRes.status === 'fulfilled'  ? runsRes.value.data.workflow_runs ?? []  : [];
    const prs    = prsRes.status === 'fulfilled'   ? prsRes.value.data  ?? []                : [];
    const alerts = alertsRes.status === 'fulfilled' ? alertsRes.value.data ?? []             : [];

    const now = Date.now();
    const cutoff30d = now - 30 * 24 * 60 * 60 * 1000;

    const prodRuns = runs.filter((r: any) =>
      r.name?.toLowerCase().includes('deploy') ||
      r.name?.toLowerCase().includes('production') ||
      r.head_branch === 'main' || r.head_branch === 'master'
    );

    const recentProdRuns = prodRuns.filter((r: any) =>
      new Date(r.created_at).getTime() > cutoff30d
    );

    const failedRuns   = recentProdRuns.filter((r: any) => r.conclusion === 'failure');
    const successRuns  = recentProdRuns.filter((r: any) => r.conclusion === 'success');

    const recentPRs    = prs.filter((p: any) => new Date(p.updated_at).getTime() > cutoff30d);
    const mergedPRs    = recentPRs.filter((p: any) => p.merged_at);
    const openPRs      = prs.filter((p: any) => p.state === 'open');
    const dependabotPRs = openPRs.filter((p: any) => p.user?.login === 'dependabot[bot]');

    const hotfixRuns = successRuns.filter((r: any) =>
      r.head_branch?.toLowerCase().includes('hotfix') ||
      r.head_commit?.message?.toLowerCase().includes('hotfix') ||
      r.head_commit?.message?.toLowerCase().includes('urgent') ||
      r.head_commit?.message?.toLowerCase().includes('emergency')
    );

    const lastDeploy = successRuns[0];

    const recentDeployments = successRuns.slice(0, 20).map((r: any) => ({
      deployed_at:    new Date(r.created_at),
      environment:    'production',
      status:         r.conclusion === 'success' ? 'SUCCESS' : 'FAILED',
      commit_sha:     r.head_sha?.slice(0, 8) ?? '',
      commit_message: r.head_commit?.message?.split('\n')[0] ?? '',
      branch:         r.head_branch ?? '',
      is_hotfix:      hotfixRuns.some((h: any) => h.id === r.id),
      run_id:         String(r.id),
    }));

    return {
      deploys_30d:           recentProdRuns.length,
      failed_builds_30d:     failedRuns.length,
      hotfixes_30d:          hotfixRuns.length,
      open_prs:              openPRs.length - dependabotPRs.length,
      merged_prs_30d:        mergedPRs.length,
      open_dependabot_alerts: typeof alerts === 'number' ? alerts : (alerts as any[]).length,
      open_dependabot_prs:   dependabotPRs.length,
      last_deploy_at:        lastDeploy ? new Date(lastDeploy.created_at) : null,
      recent_deployments:    recentDeployments,
    };
  }
}
