#!/usr/bin/env node
/**
 * Wordie Monthly Report Generator
 * Usage: node generate-report.js --client barnardos --month 2026-05 --type full
 *        node generate-report.js --client barnardos --month 2026-05 --type lite
 *
 * full = all 6 sections including PageSpeed performance data
 * lite = 5 sections, no performance section (for clients without PageSpeed data)
 */

const fs   = require('fs');
const path = require('path');

// ── CLI args ──────────────────────────────────────────────────────────────────
const args = process.argv.slice(2);
const get  = (flag) => { const i = args.indexOf(flag); return i !== -1 ? args[i + 1] : null; };

const clientId = get('--client');
const month    = get('--month');
const type     = (get('--type') || 'full').toLowerCase(); // full | lite
const outDir   = get('--out') || path.join(require('os').homedir(), 'Desktop');

if (!clientId || !month) {
  console.error('Usage: node generate-report.js --client <id> --month <YYYY-MM> [--type full|lite] [--out <dir>]');
  process.exit(1);
}

if (!/^\d{4}-\d{2}$/.test(month)) {
  console.error('Month must be in YYYY-MM format, e.g. 2026-05');
  process.exit(1);
}

// ── Load client data ──────────────────────────────────────────────────────────
const clientFile = path.join(__dirname, 'clients', `${clientId}.json`);
if (!fs.existsSync(clientFile)) {
  console.error(`Client file not found: ${clientFile}`);
  process.exit(1);
}

const client = JSON.parse(fs.readFileSync(clientFile, 'utf8'));
const report = client.reports?.[month];
if (!report) {
  console.error(`No report data found for ${clientId} / ${month}`);
  console.error(`Available months: ${Object.keys(client.reports || {}).join(', ')}`);
  process.exit(1);
}

// ── Helpers ───────────────────────────────────────────────────────────────────
function formatMonth(ym) {
  const [y, m] = ym.split('-');
  return new Date(parseInt(y), parseInt(m) - 1, 1)
    .toLocaleDateString('en-AU', { month: 'long', year: 'numeric' });
}

function statusClass(s) {
  return s === 'GREEN' ? 'green' : s === 'AMBER' ? 'amber' : 'red';
}

function trafficDot(s) {
  const c = s === 'GREEN' ? '#10b981' : s === 'AMBER' ? '#f59e0b' : '#ef4444';
  return `<span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:${c};flex-shrink:0;margin-top:3px;"></span>`;
}

function trendLabel(t) {
  return t === 'IMPROVING' ? '↑ Improving' : t === 'DECLINING' ? '↓ Declining' : '→ Stable';
}

function trendColor(t) {
  return t === 'IMPROVING' ? '#10b981' : t === 'DECLINING' ? '#ef4444' : '#6b7280';
}

function priorityColor(p) {
  return p === 'HIGH' ? '#ef4444' : p === 'MEDIUM' ? '#f59e0b' : '#6b7280';
}

function priorityBg(p) {
  return p === 'HIGH' ? 'var(--red-bg)' : p === 'MEDIUM' ? 'var(--amber-bg)' : '#f9fafb';
}

function priorityBorder(p) {
  return p === 'HIGH' ? 'var(--red-border)' : p === 'MEDIUM' ? 'var(--amber-border)' : '#e5e7eb';
}

// ── Section renderer ──────────────────────────────────────────────────────────
function renderSection(title, icon, sec) {
  const sc  = statusClass(sec.status);
  const hdr = sc === 'green' ? 'background:#ecfdf5;border-color:#a7f3d0'
             : sc === 'amber' ? 'background:#fffbeb;border-color:#fde68a'
             : 'background:#fef2f2;border-color:#fecaca';

  const metrics = sec.metrics.map(m => `
    <div class="metric-tile ${statusClass(m.status)}">
      <div class="metric-tile-top">
        <span class="metric-tile-label">${m.label}</span>
        ${trafficDot(m.status)}
      </div>
      <div class="metric-tile-value">${m.value}</div>
      <div class="metric-tile-desc">${m.description}</div>
    </div>`).join('');

  const action = sec.recommended_action ? `
    <div class="action-box ${sc}">
      <div class="action-box-label">Recommended action</div>
      <p>${sec.recommended_action}</p>
    </div>` : '';

  return `
  <div class="card">
    <div class="section-header" style="${hdr}">
      <div style="display:flex;align-items:center;gap:10px;">
        <span style="font-size:18px;">${icon}</span>
        <div>
          <div class="section-title">${title}</div>
          <div style="font-size:11px;font-weight:700;color:${trendColor(sec.trend)}">${trendLabel(sec.trend)}</div>
        </div>
      </div>
      <div style="display:flex;align-items:center;gap:12px;">
        <div style="text-align:right;">
          <div style="font-family:'Montserrat',sans-serif;font-size:20px;font-weight:700;color:var(--dark-teal);line-height:1;">${sec.score}</div>
          <div style="font-size:11px;color:#9ca3af;">/100</div>
        </div>
        <div class="tl-badge ${sc}">${sec.status}</div>
      </div>
    </div>
    <div class="card-body">
      <div class="score-bar">
        <div class="score-bar-top"><span>Score</span><span>${sec.score}/100</span></div>
        <div class="score-bar-track">
          <div class="score-bar-fill" style="width:${sec.score}%;background:${sc === 'green' ? '#10b981' : sc === 'amber' ? '#f59e0b' : '#ef4444'};"></div>
        </div>
      </div>
      <div class="section-headline">${sec.headline}</div>
      <div class="section-interp">${sec.interpretation}</div>
      <div class="metric-grid">${metrics}</div>
      <div class="risk-box">
        <div class="risk-box-label">Business risk</div>
        <p>${sec.risk}</p>
      </div>
      ${action}
    </div>
  </div>`;
}

// ── Sections to include ───────────────────────────────────────────────────────
const SECTIONS = [
  { key: 'performance', title: 'Performance', icon: '⚡' },
  { key: 'security',    title: 'Security',    icon: '🔒' },
  { key: 'reliability', title: 'Reliability', icon: '📡' },
  { key: 'updates',     title: 'Updates',     icon: '🔄' },
  { key: 'backups',     title: 'Backups',     icon: '💾' },
  { key: 'engineering', title: 'Engineering', icon: '⚙️'  },
];

const sectionsToRender = type === 'lite'
  ? SECTIONS.filter(s => s.key !== 'performance')
  : SECTIONS;

const sectionBlocks = sectionsToRender
  .filter(s => report.sections[s.key])
  .map(s => renderSection(s.title, s.icon, report.sections[s.key]))
  .join('\n');

// ── Gauge SVG ─────────────────────────────────────────────────────────────────
const gaugeScore  = report.health_score;
const gaugeColor  = report.overall_status === 'GREEN' ? '#10b981' : report.overall_status === 'AMBER' ? '#f59e0b' : '#ef4444';
const circumference = 2 * Math.PI * 54; // r=54
const dashOffset  = circumference * (1 - gaugeScore / 100);
const gaugeSVG = `
<svg width="130" height="130" viewBox="0 0 130 130">
  <circle cx="65" cy="65" r="54" fill="none" stroke="#1d4e5f" stroke-width="10"/>
  <circle cx="65" cy="65" r="54" fill="none" stroke="${gaugeColor}" stroke-width="10"
    stroke-dasharray="${circumference.toFixed(1)}" stroke-dashoffset="${dashOffset.toFixed(1)}"
    stroke-linecap="round" transform="rotate(-90 65 65)"/>
  <text x="65" y="60" text-anchor="middle" font-family="Montserrat,sans-serif" font-size="30" font-weight="700" fill="${gaugeColor}">${gaugeScore}</text>
  <text x="65" y="76" text-anchor="middle" font-family="Source Sans 3,sans-serif" font-size="11" fill="rgba(185,221,221,0.6)">/100</text>
</svg>`;

// ── Recommended actions ───────────────────────────────────────────────────────
const recActions = report.recommended_actions
  .filter(a => type === 'lite' ? a.priority !== 'HIGH' || !report.sections.performance?.recommended_action?.includes(a.title) : true)
  .map(a => `
  <div class="rec-action" style="border-radius:10px;border:1px solid ${priorityBorder(a.priority)};background:${priorityBg(a.priority)};padding:14px 16px;margin-bottom:10px;">
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
      <span style="width:8px;height:8px;border-radius:50%;background:${priorityColor(a.priority)};flex-shrink:0;"></span>
      <span style="font-family:'Montserrat',sans-serif;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:${priorityColor(a.priority)};">${a.priority} PRIORITY</span>
    </div>
    <div style="font-family:'Montserrat',sans-serif;font-size:14px;font-weight:700;color:var(--dark-teal);margin-bottom:6px;">${a.title}</div>
    <div style="font-size:13px;color:#374151;line-height:1.5;">${a.impact}</div>
  </div>`).join('');

// ── Delta text ────────────────────────────────────────────────────────────────
const delta = report.health_score_prev != null ? report.health_score - report.health_score_prev : null;
const deltaText = delta == null ? 'First assessment'
  : delta > 0 ? `+${delta} vs last month`
  : delta < 0 ? `${delta} vs last month`
  : 'No change vs last month';

// ── Overall status ────────────────────────────────────────────────────────────
const statusLabel = report.overall_status === 'GREEN' ? 'Healthy' : report.overall_status === 'AMBER' ? 'Needs attention' : 'Action required';

// ── Support tickets ───────────────────────────────────────────────────────────
function renderTickets(tickets) {
  if (!tickets) return '';

  const { summary, portal_url } = tickets;
  const list = tickets.tickets || [];

  const statusMeta = {
    CLOSED:         { label: 'Closed',          bg: '#dcfce7', color: '#166534' },
    IN_PROGRESS:    { label: 'In progress',      bg: '#dbeafe', color: '#1e40af' },
    OPEN:           { label: 'Open',             bg: '#fef9c3', color: '#854d0e' },
    PENDING_CLIENT: { label: 'Pending client',   bg: '#f3f4f6', color: '#374151' },
  };

  const priorityDot = {
    HIGH:   '#ef4444',
    MEDIUM: '#f59e0b',
    LOW:    '#10b981',
  };

  const typeBg = {
    Bug:         '#fef2f2',
    Maintenance: '#f0fdf4',
    Content:     '#eff6ff',
    Development: '#faf5ff',
    Other:       '#f9fafb',
  };

  const typeColor = {
    Bug:         '#991b1b',
    Maintenance: '#166534',
    Content:     '#1e40af',
    Development: '#6b21a8',
    Other:       '#374151',
  };

  function formatDate(d) {
    if (!d) return '';
    const [y, m, day] = d.split('-');
    return `${parseInt(day)} ${['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'][parseInt(m)-1]}`;
  }

  const statTiles = [
    { label: 'Opened',         value: summary.opened,         color: '#374151', bg: '#f9fafb',   border: '#e5e7eb' },
    { label: 'Closed',         value: summary.closed,         color: '#166534', bg: '#dcfce7',   border: '#a7f3d0' },
    { label: 'In progress',    value: summary.in_progress,    color: '#1e40af', bg: '#dbeafe',   border: '#bfdbfe' },
    { label: 'Pending client', value: summary.pending_client, color: '#92400e', bg: '#fffbeb',   border: '#fde68a' },
  ].map(t => `
    <div style="background:${t.bg};border:1px solid ${t.border};border-radius:10px;padding:14px 16px;text-align:center;">
      <div style="font-family:'Montserrat',sans-serif;font-size:28px;font-weight:700;color:${t.color};line-height:1;">${t.value}</div>
      <div style="font-size:11px;color:#6b7280;margin-top:4px;">${t.label}</div>
    </div>`).join('');

  const ticketRows = list.map(t => {
    const sm = statusMeta[t.status] || statusMeta.OPEN;
    return `
    <div style="display:grid;grid-template-columns:80px 1fr auto;gap:12px;align-items:start;padding:12px 0;border-bottom:1px solid #f3f4f6;">
      <div>
        <div style="font-family:'Montserrat',sans-serif;font-size:11px;font-weight:700;color:var(--accent-teal);">${t.id}</div>
        <div style="font-size:11px;color:#9ca3af;margin-top:2px;">${formatDate(t.opened)}</div>
      </div>
      <div>
        <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-bottom:4px;">
          <span style="width:7px;height:7px;border-radius:50%;background:${priorityDot[t.priority] || '#9ca3af'};flex-shrink:0;"></span>
          <span style="font-family:'Montserrat',sans-serif;font-size:13px;font-weight:600;color:var(--dark-teal);">${t.subject}</span>
        </div>
        <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
          <span style="font-size:11px;background:${typeBg[t.type] || '#f9fafb'};color:${typeColor[t.type] || '#374151'};padding:1px 7px;border-radius:4px;font-weight:600;">${t.type}</span>
          <span style="font-size:11px;color:#9ca3af;">→ ${t.assigned_to}</span>
          ${t.notes ? `<span style="font-size:11px;color:#9ca3af;font-style:italic;">${t.notes}</span>` : ''}
        </div>
      </div>
      <div>
        <span style="font-size:11px;font-weight:700;font-family:'Montserrat',sans-serif;background:${sm.bg};color:${sm.color};padding:3px 9px;border-radius:99px;white-space:nowrap;">${sm.label}</span>
      </div>
    </div>`;
  }).join('');

  const resolutionRate = summary.opened > 0
    ? Math.round((summary.closed / (summary.closed + summary.in_progress + (summary.pending_client || 0))) * 100)
    : 100;

  return `
  <div class="exec-card" style="margin-bottom:16px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
      <h2 style="font-family:'Montserrat',sans-serif;font-size:18px;font-weight:700;color:var(--dark-teal);">Support tickets</h2>
      ${portal_url ? `<a href="${portal_url}" style="font-size:12px;color:var(--accent-teal);font-family:'Montserrat',sans-serif;font-weight:600;">View portal →</a>` : ''}
    </div>
    <p style="font-size:13px;color:#9ca3af;margin-bottom:16px;">Activity this month via support.wordie.com.au</p>

    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:20px;">
      ${statTiles}
    </div>

    <div style="background:#f9fafb;border-radius:8px;padding:10px 14px;margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;">
      <span style="font-size:13px;color:#374151;">Ticket resolution rate this month</span>
      <span style="font-family:'Montserrat',sans-serif;font-size:16px;font-weight:700;color:${resolutionRate >= 80 ? '#166534' : resolutionRate >= 50 ? '#854d0e' : '#991b1b'};">${resolutionRate}%</span>
    </div>

    <div>
      <div style="display:grid;grid-template-columns:80px 1fr auto;gap:12px;padding-bottom:8px;border-bottom:2px solid #e5e7eb;margin-bottom:2px;">
        <span style="font-family:'Montserrat',sans-serif;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:#9ca3af;">Ticket</span>
        <span style="font-family:'Montserrat',sans-serif;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:#9ca3af;">Subject</span>
        <span style="font-family:'Montserrat',sans-serif;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:#9ca3af;">Status</span>
      </div>
      ${ticketRows}
    </div>
  </div>`;
}

const ticketsBlock = renderTickets(report.support_tickets);

// ── Lite banner ───────────────────────────────────────────────────────────────
const liteBanner = type === 'lite' ? `
<div style="background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:10px 14px;margin-bottom:20px;font-size:13px;color:#92400e;font-family:'Montserrat',sans-serif;">
  ⚠️ <strong>Maintenance report</strong> — PageSpeed performance data not included in this version.
</div>` : '';

// ── HTML ──────────────────────────────────────────────────────────────────────
const html = `<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>${client.client_name} — Website Health Report — ${formatMonth(month)}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Source+Sans+3:wght@400;600&display=swap" rel="stylesheet"/>
  <style>
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
    :root{
      --dark-teal:#0A3542;--accent-teal:#116E6E;--light-green:#B9DDDD;
      --coral:#F5634D;--near-black:#062028;--off-white:#F6F9F9;
      --green:#10b981;--amber:#f59e0b;--red:#ef4444;
      --green-bg:#ecfdf5;--green-border:#a7f3d0;--green-text:#065f46;
      --amber-bg:#fffbeb;--amber-border:#fde68a;--amber-text:#92400e;
      --red-bg:#fef2f2;--red-border:#fecaca;--red-text:#991b1b;
    }
    body{font-family:'Source Sans 3',sans-serif;font-size:15px;line-height:1.6;color:var(--near-black);background:var(--off-white);}
    h1,h2,h3,h4,h5{font-family:'Montserrat',sans-serif;}
    a{color:var(--accent-teal);text-decoration:none;}
    .site-header{background:var(--dark-teal);padding:16px 24px;display:flex;align-items:center;justify-content:space-between;margin-bottom:32px;}
    .wordmark{font-family:'Montserrat',sans-serif;font-weight:700;font-size:22px;color:var(--off-white);letter-spacing:-0.5px;}
    .tagline{font-size:13px;color:rgba(185,221,221,0.7);}
    .page{max-width:780px;margin:0 auto;padding:0 20px 64px;}
    .card{background:#fff;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.06);overflow:hidden;margin-bottom:16px;}
    .card-body{padding:24px;}
    .cover{background:var(--dark-teal);border-radius:16px;padding:36px 32px;margin-bottom:24px;position:relative;overflow:hidden;color:#fff;}
    .cover::before{content:'';position:absolute;top:-60px;right:-60px;width:220px;height:220px;background:rgba(185,221,221,0.08);border-radius:50%;}
    .cover::after{content:'';position:absolute;bottom:-40px;left:-40px;width:160px;height:160px;background:rgba(17,110,110,0.15);border-radius:50%;}
    .cover-inner{position:relative;z-index:1;display:flex;align-items:flex-start;justify-content:space-between;gap:24px;flex-wrap:wrap;}
    .cover-label{font-family:'Montserrat',sans-serif;font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:rgba(185,221,221,0.6);margin-bottom:8px;}
    .cover h1{font-size:32px;font-weight:700;color:#fff;margin-bottom:4px;}
    .cover-domain{font-size:13px;color:rgba(185,221,221,0.6);margin-bottom:6px;}
    .cover-period{font-family:'Montserrat',sans-serif;font-size:18px;font-weight:600;color:var(--light-green);margin-bottom:20px;}
    .cover-summary{background:rgba(255,255,255,0.1);border-radius:10px;padding:16px 18px;font-size:14px;color:rgba(255,255,255,0.88);line-height:1.65;max-width:420px;}
    .gauge-wrap{display:flex;flex-direction:column;align-items:center;padding-top:8px;}
    .gauge-label{font-family:'Montserrat',sans-serif;font-size:13px;font-weight:700;color:${gaugeColor};margin-top:6px;}
    .gauge-sub{font-size:12px;color:rgba(185,221,221,0.6);margin-top:2px;}
    .exec-card{background:#fff;border-radius:12px;border:1px solid #e5e7eb;padding:24px;margin-bottom:24px;}
    .exec-card h2{font-size:18px;font-weight:700;color:var(--dark-teal);margin-bottom:16px;}
    .exec-cols{display:grid;grid-template-columns:1fr 1fr;gap:20px;}
    .exec-col-label{font-family:'Montserrat',sans-serif;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:10px;}
    .exec-col-label.green{color:#10b981;} .exec-col-label.amber{color:#f59e0b;}
    .exec-item{display:flex;align-items:flex-start;gap:8px;font-size:13px;color:#374151;margin-bottom:8px;}
    .exec-check{color:#10b981;flex-shrink:0;margin-top:1px;font-weight:700;}
    .exec-arrow{color:#f59e0b;flex-shrink:0;margin-top:1px;font-weight:700;}
    .section-header{padding:16px 20px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid;}
    .section-title{font-family:'Montserrat',sans-serif;font-size:15px;font-weight:700;color:var(--dark-teal);}
    .tl-badge{font-family:'Montserrat',sans-serif;font-size:10px;font-weight:700;padding:3px 10px;border-radius:99px;letter-spacing:0.5px;}
    .tl-badge.green{background:#dcfce7;color:#166534;} .tl-badge.amber{background:#fef9c3;color:#854d0e;} .tl-badge.red{background:#fee2e2;color:#991b1b;}
    .score-bar{margin-bottom:14px;}
    .score-bar-top{display:flex;justify-content:space-between;font-size:12px;color:#6b7280;margin-bottom:4px;}
    .score-bar-track{height:6px;background:#f3f4f6;border-radius:99px;overflow:hidden;}
    .score-bar-fill{height:100%;border-radius:99px;}
    .section-headline{font-family:'Montserrat',sans-serif;font-size:15px;font-weight:700;color:var(--dark-teal);margin-bottom:8px;}
    .section-interp{font-size:13px;color:#4b5563;line-height:1.65;margin-bottom:16px;}
    .metric-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px;}
    .metric-tile{border-radius:8px;padding:12px 14px;}
    .metric-tile.green{background:var(--green-bg);} .metric-tile.amber{background:var(--amber-bg);} .metric-tile.red{background:var(--red-bg);}
    .metric-tile-top{display:flex;align-items:flex-start;justify-content:space-between;gap:6px;margin-bottom:4px;}
    .metric-tile-label{font-family:'Montserrat',sans-serif;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:#6b7280;}
    .metric-tile-value{font-family:'Montserrat',sans-serif;font-size:20px;font-weight:700;line-height:1.1;margin-bottom:4px;}
    .metric-tile.green .metric-tile-value{color:var(--green-text);} .metric-tile.amber .metric-tile-value{color:var(--amber-text);} .metric-tile.red .metric-tile-value{color:var(--red-text);}
    .metric-tile-desc{font-size:11px;color:#6b7280;line-height:1.4;}
    .risk-box{background:#f9fafb;border-radius:8px;padding:12px 14px;margin-bottom:10px;}
    .risk-box-label{font-family:'Montserrat',sans-serif;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:#9ca3af;margin-bottom:4px;}
    .risk-box p{font-size:13px;color:#374151;line-height:1.5;}
    .action-box{border-radius:8px;padding:12px 14px;border:1px solid;}
    .action-box.red{background:var(--red-bg);border-color:var(--red-border);}
    .action-box.amber{background:var(--amber-bg);border-color:var(--amber-border);}
    .action-box.green{background:var(--green-bg);border-color:var(--green-border);}
    .action-box-label{font-family:'Montserrat',sans-serif;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:4px;}
    .action-box.red .action-box-label{color:var(--red-text);} .action-box.amber .action-box-label{color:var(--amber-text);} .action-box.green .action-box-label{color:var(--green-text);}
    .action-box p{font-size:13px;color:#374151;line-height:1.5;}
    .report-footer{text-align:center;padding:32px 0;border-top:1px solid #e5e7eb;margin-top:8px;}
    .report-footer p{font-size:12px;color:#9ca3af;margin-bottom:4px;}
    .wordmark-footer{font-family:'Montserrat',sans-serif;font-size:13px;font-weight:700;color:var(--dark-teal);margin-top:12px;}
    .print-btn{position:fixed;bottom:24px;right:24px;background:var(--dark-teal);color:#fff;border:none;border-radius:8px;padding:12px 20px;font-family:'Montserrat',sans-serif;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:8px;box-shadow:0 4px 12px rgba(10,53,66,0.3);}
    .print-btn:hover{background:var(--accent-teal);}
    @media print{
      .print-btn{display:none!important;}
      body{background:#fff;}
      .card,.exec-card{box-shadow:none;}
      .site-header{margin-bottom:16px;}
      .page{padding:0 16px 32px;}
    }
    @media(max-width:600px){
      .cover-inner{flex-direction:column;}
      .exec-cols{grid-template-columns:1fr;}
      .metric-grid{grid-template-columns:1fr;}
      .gauge-wrap{align-items:flex-start;}
    }
  </style>
</head>
<body>

<header class="site-header">
  <div class="wordmark">wordie</div>
  <div class="tagline">Website Health Dashboard</div>
</header>

<div class="page">

  ${liteBanner}

  <!-- COVER -->
  <div class="cover">
    <div class="cover-inner">
      <div>
        <div class="cover-label">Monthly Health Report${type === 'lite' ? ' — Maintenance Summary' : ''}</div>
        <h1>${client.client_name}</h1>
        <div class="cover-domain">${client.domain}</div>
        <div class="cover-period">${formatMonth(month)}</div>
        <div class="cover-summary">${report.executive_summary}</div>
      </div>
      <div class="gauge-wrap">
        ${gaugeSVG}
        <div class="gauge-label">${statusLabel}</div>
        <div class="gauge-sub">${deltaText}</div>
      </div>
    </div>
  </div>

  <!-- EXECUTIVE SUMMARY -->
  <div class="exec-card">
    <h2>Executive summary</h2>
    <div class="exec-cols">
      <div>
        <div class="exec-col-label green">Highlights this month</div>
        ${report.highlights.map(h => `<div class="exec-item"><span class="exec-check">✓</span><span>${h}</span></div>`).join('')}
      </div>
      <div>
        <div class="exec-col-label amber">Areas to address</div>
        ${report.key_risks.map(r => `<div class="exec-item"><span class="exec-arrow">→</span><span>${r}</span></div>`).join('')}
      </div>
    </div>
  </div>

  <!-- SECTIONS -->
  <h2 style="font-family:'Montserrat',sans-serif;font-size:18px;font-weight:700;color:var(--dark-teal);margin-bottom:14px;">Detailed breakdown</h2>
  ${sectionBlocks}

  <!-- RECOMMENDED ACTIONS -->
  <div class="exec-card">
    <h2>Recommended actions</h2>
    <p style="font-size:13px;color:#9ca3af;margin-bottom:16px;">Prioritised list of actions for this month</p>
    ${recActions}
  </div>

  <!-- SUPPORT TICKETS -->
  ${ticketsBlock}

  <!-- FOOTER -->
  <div class="report-footer">
    <p>This report was generated by Wordie on ${report.generated_at.replace(/-/g, ' ').replace(/(\d{4}) (\d{2}) (\d{2})/, (_, y, m, d) => `${parseInt(d)} ${['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'][parseInt(m)-1]} ${y}`)}.</p>
    <p>Questions? Contact us at <a href="mailto:support@wordie.com.au">support@wordie.com.au</a></p>
    <div class="wordmark-footer">wordie — WordPress, done properly.</div>
  </div>

</div>

<button class="print-btn" onclick="window.print()">
  <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
  Print / Export PDF
</button>

</body>
</html>`;

// ── Write output ──────────────────────────────────────────────────────────────
if (!fs.existsSync(outDir)) fs.mkdirSync(outDir, { recursive: true });

const filename = `${clientId}-report-${month}-${type}.html`;
const outPath  = path.join(outDir, filename);
fs.writeFileSync(outPath, html, 'utf8');

console.log(`✓ Report generated: ${outPath}`);
console.log(`  Client : ${client.client_name}`);
console.log(`  Month  : ${formatMonth(month)}`);
console.log(`  Type   : ${type === 'lite' ? 'Lite (no performance section)' : 'Full (all sections)'}`);
