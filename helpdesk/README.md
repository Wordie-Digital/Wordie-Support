# Wordie Helpdesk Knowledge Base

Support playbooks distilled from resolved HubSpot tickets. Each playbook is a canonical, merged record for a recurring issue pattern — covering diagnosis, fix steps, and prevention.

## Playbooks

| Playbook | Category | Confidence | Pattern Frequency |
|---|---|---|---|
| [WooCommerce Order Flow Failure](woocommerce/order-flow-failure.md) | WooCommerce / WP-Cron | 10/10 | High |
| [WooCommerce Bulk Import Attribute Overwrite](woocommerce/bulk-import-attribute-overwrite.md) | WooCommerce / Data | 8/10 | Medium |
| [WordPress Media Upload Error](wordpress/media-upload-error.md) | WordPress / Server | 8/10 | Medium |
| [Member Login Blocked](wordpress/member-login-blocked.md) | WordPress / Security | 8/10 | High |
| [GTM Container Replaced](analytics/gtm-container-replaced.md) | Analytics / GTM | 10/10 | Medium |
| [Debug Code on Production](wordpress/debug-code-on-production.md) | WordPress / Dev | 9/10 | Low |

## Triage Framework

| Priority | Label | Criteria | Response SLA |
|---|---|---|---|
| P1 | CRITICAL | Site down, checkout broken, data loss, security breach | < 1 hour |
| P2 | HIGH | Core feature broken for active users, revenue impact | < 4 hours |
| P3 | MEDIUM | Non-critical feature degraded, workaround exists | < 24 hours |
| P4 | LOW | Cosmetic, informational, low traffic page | < 72 hours |

## Routing

| Route | When |
|---|---|
| **Developer** | P1/P2 technical issues, server errors, broken functionality |
| **Support Agent** | P3/P4, content updates, guidance requests |
| **Escalation Team** | P1 with data loss or security breach |
| **Chatbot** | P4 FAQs, known self-serve patterns |

## Contributing

1. Confirm the pattern appears in at least 2 resolved tickets before creating a playbook.
2. Use the [playbook template](#template) below.
3. Link to relevant HubSpot ticket IDs in the Sources section.
4. Set confidence score based on: 10 = direct email chain confirmation, 5 = internal notes only, 1 = inference.

## Template

```markdown
# [Issue Title]

**Category**: [WooCommerce / WordPress / Analytics / etc.]  
**Confidence**: X/10  
**Pattern frequency**: High / Medium / Low  
**Last seen**: YYYY-MM-DD  

## Symptom
What the client reports.

## Root cause
The technical reason this happens.

## Diagnosis steps
1. Step one
2. Step two

## Fix
1. Fix step one
2. Fix step two

## Prevention
What to do so it doesn't happen again.

## Agent script
What to say to the client.

## Chatbot response
Short self-serve version.

## Sources
- [TICKET-ID](HubSpot link) — brief note
```
