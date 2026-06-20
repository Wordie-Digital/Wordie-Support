# Debug Code on Production

**Category**: WordPress / Development  
**Confidence**: 9/10  
**Pattern frequency**: Low  
**Last seen**: 2026-05-19  
**Triage priority**: P1 — treat as critical until confirmed otherwise

## Symptom

Client reports one or more of:
- PHP errors or warnings visible on the live site
- `WP_DEBUG` output appearing in the page source or on-screen
- Partial page renders with raw PHP stack traces
- A white screen on specific pages with no error message

Alternatively: internal discovery during a deploy audit where `WP_DEBUG true` is found in `wp-config.php` on production.

## Root cause

Development configuration (`WP_DEBUG`, `WP_DEBUG_DISPLAY`, `SCRIPT_DEBUG`, or similar) was left enabled, or debug/test code was deployed to production. This can happen when:

- A developer forgets to flip the `wp-config.php` flags before deploying
- The staging `wp-config.php` is used as a template and pushed directly
- A plugin or theme is deployed with `error_reporting(E_ALL)` in its bootstrap

This is a **P1 issue** because:
- PHP errors leaking to the browser expose file paths, database structure, and plugin/theme internals — a meaningful security risk
- Errors can break rendering for end users

## Diagnosis steps

1. Check `wp-config.php` on the live server for:
   ```php
   define( 'WP_DEBUG', true );
   define( 'WP_DEBUG_DISPLAY', true );
   ```
   Both should be `false` on production. `WP_DEBUG_LOG` may be `true` (logs to file only — acceptable).
2. Check whether errors are actively visible on the frontend: open an incognito window and load the homepage and a product/service page.
3. Check `debug.log` for the volume and recency of errors — this scopes what's been leaking.
4. Grep the deployed theme and active plugins for `error_reporting`, `var_dump`, `print_r` calls left in production code.

## Fix

**Immediate (within 1 hour):**
1. Set in `wp-config.php`:
   ```php
   define( 'WP_DEBUG', false );
   define( 'WP_DEBUG_DISPLAY', false );
   define( 'WP_DEBUG_LOG', false );
   ```
2. Deploy via GitHub → GitHub Actions → WP Engine.
3. Verify: reload the live site in incognito, confirm no PHP output visible. View source and confirm no error strings.

**Follow-up:**
4. Review `debug.log` for the window the debug mode was active — assess whether any sensitive data was exposed.
5. If the site is publicly indexed, submit for re-crawl in Google Search Console to ensure no error pages were cached.
6. Remove any `var_dump`, `print_r`, or `die()` debug calls found in theme/plugin code.

## Prevention

- Add a pre-deploy check to GitHub Actions: `grep -r "WP_DEBUG.*true" wp-config.php` and fail the build if found.
- Maintain separate `wp-config.php` files per environment (staging vs production), never copy production config from staging.
- Add to PR review checklist: confirm debug flags are off before merging a release branch.

## Agent script

> "We've identified a configuration issue on your live site — debug mode was left active, which can cause error messages to appear on the page and exposes some technical details to visitors. We've turned it off immediately and confirmed the site is displaying correctly. We're reviewing what was visible during that window and will update you if there's anything further to address."

## Chatbot response

> **Seeing PHP errors or unusual text on your site?**  
> This is typically caused by a development setting being left active on the live site. Please [submit an urgent support ticket](https://wordie.com.au/support) — include a screenshot if possible. Our team treats this as a priority and aims to resolve within 1 hour.

## Sources

- Internal audit ticket — debug code found on production during deploy review (2026-05-19 triage batch)
