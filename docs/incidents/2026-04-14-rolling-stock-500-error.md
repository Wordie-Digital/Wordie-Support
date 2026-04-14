# Incident: HTTP 500 on Transportation / Rolling Stock product pages

**Date:** 2026-04-14
**Severity:** High (SEO-impacting — pages returning 500 to Googlebot)
**Status:** Resolved
**Branch:** `hotfix/rolling-stock-500-error`
**Reported by:** SEO team (4 URLs flagged in crawl report)

---

## Affected URLs

All 10 Rolling Stock products under `/products/industrial/transportation-heavy-engineering/` were returning HTTP 500, including:

- `/product/australian-rolling-stock-86-class-8601-8650/`
- `/product/australian-rolling-stock-v-set/`
- `/product/australian-rolling-stock-80-class/`
- `/product/australian-rolling-stock-86-class-8601-8650-blue/`

---

## Root Cause

A PHP 8.x TypeError fatal in the custom Elementor widget `Custom_El_Products_Single_Tabs`.

**File:** `wp-content/themes/mitsubishi/inc/elementor/widgets/products-single/products-single-tabs.php`

**Line 135** — the Features tab builder called `array_map()` with the return value of `get_field('key_features')`, which returns `null` when the ACF field is not populated on a product:

```php
// BEFORE (broken)
$key_features = array_map( function ( $key_feature_id ) {
    return get_term( $key_feature_id, 'pa_key-features' );
}, get_field( 'key_features' ) );  // ← null on Rolling Stock products
```

PHP 8.x changed `array_map()` to throw a `TypeError` when the second argument is `null`. This worked on PHP 7.x (where it was silently ignored) but fatals on PHP 8.x.

The file was rewritten after 2026-03-03 (backup `products-single-tabs.php-3-3-2026` exists on the server). The null-safety guard was not carried over in the rewrite.

---

## Fix

```php
// AFTER (fixed)
$key_features = array_map( function ( $key_feature_id ) {
    return get_term( $key_feature_id, 'pa_key-features' );
}, get_field( 'key_features' ) ?: [] );  // ← fallback to empty array
```

The `?: []` null coalescing ensures `array_map()` always receives an array.

---

## Deploy path

1. Fix applied to `hotfix/rolling-stock-500-error` branch
2. Merged into `develop`
3. GitHub Actions deployed to `meaustdev` (Dev WP Engine) via rsync over SSH
4. Fix also applied directly to production server via SSH (emergency, pre-pipeline)
5. PR raised: `develop` → `main` to record fix in main branch history

---

## Verification

All 4 SEO-reported URLs returned HTTP 200 on both production and dev after fix.

```
200 → /product/australian-rolling-stock-86-class-8601-8650/
200 → /product/australian-rolling-stock-v-set/
200 → /product/australian-rolling-stock-80-class/
200 → /product/australian-rolling-stock-86-class-8601-8650-blue/
```

---

## Related findings (not yet actioned)

- `functions.php` contains `get_product_category_types()` and `get_product_category_benefits()` shortcodes added after 2026-03-03 that may have the same null-safety risk. Review recommended.
- Backup files on production server (`*.php-3-3-2026`, `*.bak`) should be cleaned up.

---

## Timeline

| Time (AEST) | Action |
|---|---|
| 2026-04-14 | SEO crawl report flags 4 URLs as 500 |
| 2026-04-14 | Root cause identified: `array_map()` + null in `products-single-tabs.php` |
| 2026-04-14 | Emergency fix applied to production via SSH |
| 2026-04-14 | All 10 Rolling Stock product pages restored to 200 |
| 2026-04-14 | Fix committed to `hotfix/rolling-stock-500-error`, merged to `develop` |
| 2026-04-14 | Dev environment DB domain issue resolved, deploy confirmed on dev |
