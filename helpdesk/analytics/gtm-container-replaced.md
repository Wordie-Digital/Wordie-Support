# GTM Container Replaced

**Category**: Analytics / GTM  
**Confidence**: 10/10  
**Pattern frequency**: Medium  
**Last seen**: 2026-05-19  

## Symptom

Client reports analytics data has stopped appearing in GA4, or a third-party tracking tool (e.g., Meta Pixel, LinkedIn Insight) has stopped firing. Alternatively, the client noticed a different GTM container ID in the page source.

## Root cause

The correct GTM container ID (`GTM-WL7TVVS`) was replaced with an incorrect one (`GTM-MW2XWG3C`) during a theme update, plugin update, or code deploy. This silently breaks all tags managed within the correct GTM container — including GA4, conversion tracking, and remarketing pixels — without generating any visible error on the site.

Confirmed via email chain: Eyecare Plus client identified the regression themselves by inspecting the page source.

## Diagnosis steps

1. View page source on the live site (Ctrl+U / Cmd+U) and search for `GTM-`.
2. Confirm the container ID matches the expected value: **`GTM-WL7TVVS`**.
3. If the ID is different (e.g., `GTM-MW2XWG3C`), the container has been replaced.
4. Check git history or deployment logs for recent changes to `header.php`, `functions.php`, or any plugin/theme file that injects GTM.
5. Use GTM Preview mode on the correct container to confirm tags are present and configured.

## Fix

1. Locate the GTM snippet in the theme — typically in `header.php` immediately after the `<body>` tag, and a `<noscript>` version in `footer.php`.
2. Replace the incorrect container ID with `GTM-WL7TVVS`.
3. Deploy the fix via GitHub → GitHub Actions → WP Engine (never push directly to WP Engine).
4. Verify on the live site: view source, confirm `GTM-WL7TVVS` appears.
5. Use GTM Preview mode to confirm tags are firing as expected.
6. Notify client with confirmation and check GA4 realtime view to confirm data is flowing.

## Prevention

- Add GTM container ID to the post-deploy smoke test checklist: `grep GTM- <(curl -s https://site.com)`.
- Pin the GTM snippet in a dedicated `inc/gtm.php` file rather than inline in `header.php` so it's less likely to be overwritten during theme updates.
- Set up a GA4 alert for "0 sessions in last 24 hours" to catch silent tracking failures.

## Agent script

> "We've identified the issue — the Google Tag Manager container ID on your site was accidentally swapped to an incorrect one during a recent update. This would have paused all your tracking and analytics since that point. We've restored the correct ID and verified it's firing properly. You should see data returning to GA4 within a few hours. We're also adding this to our post-deploy checks so it gets caught automatically in future."

## Chatbot response

> **Analytics or tracking stopped working?**  
> If your GA4 data has dropped to zero or a tracking pixel stopped firing, the GTM container ID on your site may have been changed during an update. Please [submit a support ticket](https://wordie.com.au/support) and let us know approximately when the issue started — we'll investigate and fix within the hour.

## Sources

- Eyecare Plus ticket — client identified regression via page source inspection; confirmed via email chain; container `GTM-MW2XWG3C` (wrong) vs `GTM-WL7TVVS` (correct)
