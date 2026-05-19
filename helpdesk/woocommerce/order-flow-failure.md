# WooCommerce Order Flow Failure

**Category**: WooCommerce / WP-Cron  
**Confidence**: 9/10  
**Pattern frequency**: High  
**Last seen**: 2026-05-19  

## Symptom

Client reports one or more of:
- Orders stuck in "Processing" indefinitely
- Customers not receiving order confirmation emails
- Order numbers not generated / orders not appearing in admin

## Root cause

WP-Cron event `automate_dispatching_recent_task` is silently cleared from the cron schedule. WooCommerce relies on WP-Cron to dispatch order confirmation emails and advance order status. When the event disappears, emails stop and orders stall — but no error is logged by default.

This commonly occurs after:
- Plugin updates (especially WooCommerce, WP Mail SMTP, or automation plugins)
- Server-side cron replacements or hosting migrations
- Manual `wp_clear_scheduled_hook` calls in update routines

## Diagnosis steps

1. Check `wp_options` table for `cron` key — confirm `automate_dispatching_recent_task` is absent or overdue.
2. Run `wp cron event list` via WP-CLI to list all scheduled events and their next run times.
3. Check email delivery logs (WP Mail SMTP or equivalent) for failures in the affected period.
4. Review recent plugin update history for anything touching order automation or cron.
5. Confirm orders are being created but stuck in "Processing" (rules out payment gateway issues).

## Fix

1. Re-register the missing cron event:
   ```php
   if ( ! wp_next_scheduled( 'automate_dispatching_recent_task' ) ) {
       wp_schedule_event( time(), 'hourly', 'automate_dispatching_recent_task' );
   }
   ```
2. Trigger a manual re-send for affected stuck orders via WooCommerce > Orders > [Order] > Resend order confirmation.
3. If orders are stuck in "Processing", manually advance status to "Completed" or use the bulk action.
4. Verify cron is firing: install WP Crontrol plugin temporarily, confirm next scheduled run appears.
5. Remove WP Crontrol after verification.

## Prevention

- Add a cron health check to site monitoring (e.g., Uptime Robot hitting a `/wp-cron.php` endpoint).
- After any major plugin update, run `wp cron event list` to confirm events are intact.
- Consider replacing WP-Cron with a real server cron (`*/5 * * * * wget -q -O - https://site.com/wp-cron.php?doing_wp_cron`) for high-volume WooCommerce sites.

## Agent script

> "Thanks for letting us know — we've identified the issue. A scheduled background task that WooCommerce uses to send order emails was cleared, likely during a recent plugin update. We've restored it and triggered a re-send for the affected orders. You should see the emails land within the next few minutes. We're also adding a monitoring check so we catch this automatically going forward."

## Chatbot response

> **Orders stuck or missing confirmation emails?**  
> This is usually caused by a background task being interrupted during a plugin update. Our team can fix this in under 30 minutes. Please [submit a support ticket](https://wordie.com.au/support) and include your site URL and affected order numbers.

## Sources

- GCS-423 — order confirmation emails not sending (WP-Cron cleared post-update)
- GCS-414 — orders stuck in Processing (same root cause, separate client)
- HubSpot ticket 255137028572 — Foodbank ticket in open queue matching this pattern (flagged 2026-05-19)
