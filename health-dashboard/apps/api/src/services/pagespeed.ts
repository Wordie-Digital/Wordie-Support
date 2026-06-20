/**
 * Google PageSpeed Insights API adapter.
 * Tests both mobile and desktop, returns normalised CWV metrics.
 */

import axios from 'axios';

export interface PageSpeedMetrics {
  pagespeed_mobile:  number;
  pagespeed_desktop: number;
  lcp_mobile:        number;   // seconds
  lcp_desktop:       number;
  inp_mobile:        number;   // milliseconds
  inp_desktop:       number;
  cls_mobile:        number;
  cls_desktop:       number;
  ttfb_ms:           number;
  fcp_mobile:        number;   // seconds
  raw_mobile:        Record<string, unknown>;
  raw_desktop:       Record<string, unknown>;
}

export class PageSpeedService {
  private apiKey: string;
  private baseUrl = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed';

  constructor(apiKey: string) {
    this.apiKey = apiKey;
  }

  async getMetrics(url: string): Promise<PageSpeedMetrics> {
    const [mobile, desktop] = await Promise.all([
      this.runTest(url, 'mobile'),
      this.runTest(url, 'desktop'),
    ]);

    const mAudit = mobile.lighthouseResult?.audits ?? {};
    const dAudit = desktop.lighthouseResult?.audits ?? {};

    return {
      pagespeed_mobile:  Math.round((mobile.lighthouseResult?.categories?.performance?.score ?? 0) * 100),
      pagespeed_desktop: Math.round((desktop.lighthouseResult?.categories?.performance?.score ?? 0) * 100),
      lcp_mobile:        this.extractNumericValue(mAudit['largest-contentful-paint'], 's'),
      lcp_desktop:       this.extractNumericValue(dAudit['largest-contentful-paint'], 's'),
      inp_mobile:        this.extractNumericValue(mAudit['interaction-to-next-paint'], 'ms'),
      inp_desktop:       this.extractNumericValue(dAudit['interaction-to-next-paint'], 'ms'),
      cls_mobile:        this.extractNumericValue(mAudit['cumulative-layout-shift']),
      cls_desktop:       this.extractNumericValue(dAudit['cumulative-layout-shift']),
      ttfb_ms:           this.extractNumericValue(mAudit['server-response-time'], 'ms'),
      fcp_mobile:        this.extractNumericValue(mAudit['first-contentful-paint'], 's'),
      raw_mobile:        mobile,
      raw_desktop:       desktop,
    };
  }

  private async runTest(url: string, strategy: 'mobile' | 'desktop'): Promise<any> {
    const res = await axios.get(this.baseUrl, {
      params: {
        url,
        strategy,
        key: this.apiKey,
        category: 'performance',
      },
      timeout: 60000,
    });
    return res.data;
  }

  private extractNumericValue(audit: any, unit?: string): number {
    if (!audit) return 0;
    const raw = audit.numericValue ?? audit.displayValue;
    if (typeof raw === 'number') {
      if (unit === 's' && raw > 100) return raw / 1000;
      return raw;
    }
    if (typeof raw === 'string') {
      const parsed = parseFloat(raw.replace(/[^0-9.]/g, ''));
      if (unit === 's' && raw.includes('ms')) return parsed / 1000;
      return isNaN(parsed) ? 0 : parsed;
    }
    return 0;
  }
}
