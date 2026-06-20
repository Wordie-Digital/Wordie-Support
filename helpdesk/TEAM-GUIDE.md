# Wordie Support Operations — Team Guide

How our AI-assisted support workflows operate, what runs automatically, and what the team needs to do manually.

---

## Overview

We have three interconnected systems:

| System | What it does | Runs |
|---|---|---|
| **HubSpot Auto-Triage** | Classifies new tickets, adds notes, sets priority + category | Every hour, automatically |
| **Helpdesk Knowledge Base** | Canonical playbooks for recurring issues | Static — updated by the team |
| **GitHub PR workflow** | All code changes deploy via GitHub Actions → WP Engine | On every PR merge |

---

## 1. HubSpot Auto-Triage

### What it does

Every hour, a Claude AI agent runs in the background and:

1. Pulls all open HubSpot tickets created in the last 2 hours
2. Reads the ticket subject and description
3. Classifies each ticket P1–P4 and routes it
4. Posts a structured note to the ticket in HubSpot
5. Updates the **Priority** and **Category** fields if they're blank

### What a triage note looks like

When you open a ticket, you'll see a note from `Wordie-Support` at the top:

```
🔍 AUTO-TRIAGE | 2026-05-19

INTENT: Client unable to upload product images in WordPress media library
PRIORITY: P3 | URGENCY: normal
ROUTED TO: Developer | ESCALATION: No

SUMMARY: The uploads directory has lost write permissions, preventing any media uploads. 
No revenue impact but blocks content publishing workflow.

RECOMMENDED ACTION: SSH/SFTP to correct permissions on wp-content/uploads (chmod 755). 
See playbook: wordpress/media-upload-error.md

REUSABILITY: 8/10 | KNOWN PATTERN ✓
TAGS: wordpress, uploads, permissions, p3, support-trouble-shooting
```

### Priority scale

| Priority | Label | What it means | Your SLA |
|---|---|---|---|
| P1 | URGENT | Site down, checkout broken, security breach, emails stopped | < 1 hour |
| P2 | HIGH | Major feature broken, login blocked, analytics gone | < 4 hours |
| P3 | MEDIUM | Partial issue, workaround exists | < 24 hours |
| P4 | LOW | Cosmetic, content update, general question | < 72 hours |

### Routing guide

| Routed To | Handle it |
|---|---|
| Developer | Pass to dev team. PHP bugs, JS issues, integrations, performance. |
| Support Agent | You can resolve this. Content updates, IP whitelists, redirects, access. |
| Chatbot | Close or auto-reply. Spam, misdirected email, FAQ. |
| Escalation Team | Flag to Stewart immediately. Data loss, active breach, payment system down. |

### What the agent skips

The agent will not triage:
- Tickets with "M&S" in the subject (ongoing retainer tasks, handled separately)
- Pure project delivery tickets (design briefs, UX reviews without bug language)
- "Inbound Call" records
- Government/system notifications (ABN Lookup, etc.)

### What you still need to do

The triage note is a recommendation — it's not an assignment. You still need to:
1. Read the triage note when you pick up a ticket
2. Agree or disagree with the priority (override it if needed)
3. Assign the ticket to the right person
4. Action the recommended next step

---

## 2. Helpdesk Knowledge Base

Located at `/helpdesk/` in this repository. Each file is a playbook for a recurring issue pattern.

### Current playbooks

| Playbook | Issue | Confidence |
|---|---|---|
| [woocommerce/order-flow-failure.md](woocommerce/order-flow-failure.md) | Orders stuck / confirmation emails not sending (WP-Cron) | 9/10 |
| [wordpress/media-upload-error.md](wordpress/media-upload-error.md) | "Upload directory isn't writable" | 8/10 |
| [wordpress/member-login-blocked.md](wordpress/member-login-blocked.md) | Client login blocked by Wordfence / IP block | 8/10 |
| [analytics/gtm-container-replaced.md](analytics/gtm-container-replaced.md) | GTM container ID replaced on deploy | 10/10 |
| [wordpress/debug-code-on-production.md](wordpress/debug-code-on-production.md) | Dev/debug code live on production (P1) | 9/10 |

### How to use a playbook

When the triage note says `KNOWN PATTERN ✓` and references a playbook filename, open that file. It contains:

- **Diagnosis steps** — exactly what to check before doing anything
- **Fix** — the step-by-step resolution
- **Agent script** — what to say to the client (copy/paste ready)
- **Chatbot response** — a shorter self-serve version

### How to add a new playbook

1. See the same issue resolved across at least 2 separate tickets
2. Copy the template from [helpdesk/README.md](README.md)
3. Create the file in the right subdirectory (`woocommerce/`, `wordpress/`, `analytics/`, etc.)
4. Open a PR — it'll get reviewed before merging
5. Tell Stewart so the auto-triage agent can be updated to flag it as a known pattern

---

## 3. What is Claude Code? (The scheduled task explained)

Claude Code is Anthropic's AI coding assistant — the same AI Stewart uses for dev work. It has a **Scheduled Tasks** feature that lets you define an AI agent prompt and run it on a timer, like a cron job, but using natural language instructions instead of code.

The auto-triage is built on this. Here's how it works end to end:

```
Every hour at :00
       ↓
Claude Code wakes up and reads the task file:
/Users/stewartlemalu/.claude/scheduled-tasks/hubspot-auto-triage/SKILL.md
       ↓
The AI agent follows the instructions in that file:
- Connects to HubSpot via MCP (Model Context Protocol — a secure API bridge)
- Searches for new tickets
- Classifies them
- Writes notes back to HubSpot
       ↓
Writes a run summary (tickets scanned, notes created, errors)
       ↓
Sleeps until next hour
```

### Where to find it

In the **Claude Code desktop app** (Mac):
1. Open Claude Code
2. Click the clock/schedule icon in the sidebar (or press `Cmd+,` > Scheduled Tasks)
3. You'll see `hubspot-auto-triage` listed
4. From there you can: **Run now**, pause, edit the prompt, or view run history

### Important: first-time approval

The first time the task runs, Claude Code will ask you to approve the HubSpot MCP tool permissions. Once you approve them once, they're remembered for all future runs. **Click "Run now" in the app to trigger this approval** — otherwise the first automated run at the top of the hour will pause waiting for you.

### What if a run fails?

Check the run history in the Claude Code app. Common causes:
- HubSpot MCP connection timeout (retry manually)
- Batch size error (already handled in the prompt — max 10 per batch)
- New ticket category value from HubSpot that isn't in our list (Stewart updates the prompt)

---

## 4. Deployment workflow (how code gets to WP Engine)

All code goes through GitHub — never push directly to WP Engine.

```
Local development
       ↓
git push → GitHub (branch)
       ↓
Open Pull Request
       ↓
Review + merge to main
       ↓
GitHub Actions runs automatically
       ↓
Deploys to WP Engine (staging or production)
```

This is enforced to prevent the GTM container regression and similar deploy accidents — GitHub Actions runs validation checks before deploying.

---

## 5. Quick reference

### Client says they can't log in
→ Check triage note. If P2/P3 → open [member-login-blocked.md](wordpress/member-login-blocked.md). Wordfence IP block is the most common cause.

### Orders stuck or confirmation emails not sending
→ Open [order-flow-failure.md](woocommerce/order-flow-failure.md). WP-Cron event was likely cleared. This is P1 if checkout is actively broken.

### Client can't upload images
→ Open [media-upload-error.md](wordpress/media-upload-error.md). Directory permissions fix takes ~15 minutes.

### Analytics dropped to zero
→ Open [gtm-container-replaced.md](analytics/gtm-container-replaced.md). Check the GTM container ID in page source. Correct ID is `GTM-WL7TVVS`.

### PHP errors showing on live site
→ **P1 immediately.** Open [debug-code-on-production.md](wordpress/debug-code-on-production.md). Escalate to developer now.

---

## Questions?

Ping Stewart on Slack or reply to the HubSpot ticket thread. The auto-triage system is maintained by Stewart — if you think a triage classification is wrong, let him know so the prompt logic can be updated.
