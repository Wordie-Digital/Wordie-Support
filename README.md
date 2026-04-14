# meaust-wp — Mitsubishi Electric Australia

WordPress theme and custom plugin source for [mitsubishielectric.com.au](https://www.mitsubishielectric.com.au), hosted on WP Engine.

---

## Environments

| Environment | WP Engine Install | URL | Deploys from |
|---|---|---|---|
| Production | `meaust` | mitsubishielectric.com.au | `main` (manual PR only) |
| Staging | `meauststg` | meauststg.wpengine.com | `staging` (auto on push) |
| Dev | `meaustdev` | meaustdev.wpenginepowered.com | `develop` (auto on push) |

---

## Branch Workflow

```
feature/* or bugfix/*
       ↓
    develop  →  auto-deploys to Dev
       ↓  (PR + review)
    staging  →  auto-deploys to Staging
       ↓  (PR + 2 approvals)
      main   →  manual deploy to Production
```

**Hotfixes** branch from `main`, fix applied, then merged into both `main` and `develop`.

---

## Deploying

Deployment is automated via GitHub Actions (`.github/workflows/deploy.yml`):

- Push to `develop` → deploys theme + custom plugins to Dev via rsync over SSH
- Push to `staging` → deploys to Staging
- Production → manual `workflow_dispatch` only

> **Note:** WP Engine Git push (`git.wpengine.com`) is not available for this install. Deployment uses rsync over SSH to WP Engine SFTP endpoints.

### Required GitHub Secrets

| Secret | Description |
|---|---|
| `WPE_SSHG_KEY_PRIVATE` | SSH private key for WP Engine SFTP access |
| `WPE_ENV_DEV` | WP Engine install name for dev (`meaustdev`) |
| `WPE_ENV_STAGING` | WP Engine install name for staging (`meauststg`) |
| `WPE_ENV_PROD` | WP Engine install name for production (`meaust`) |

---

## What's in this repo

| Path | Description |
|---|---|
| `wp-content/themes/mitsubishi/` | Custom theme |
| `wp-content/plugins/ajax-load-more-custom/` | Licensed plugin |
| `wp-content/plugins/ajax-load-more-filters/` | Licensed plugin |
| `wp-content/plugins/ajax-load-more-preloaded/` | Licensed plugin |

Uploads, WordPress core, and free plugins (installed via WP Engine) are not tracked.

---

## Dev Environment Setup

When refreshing the dev database from production, run the following after the copy:

```bash
ssh meaustdev@meaustdev.ssh.wpengine.net

# 1. Update wp_blogs table to use dev domain
php -r '
$c = new mysqli("127.0.0.1","meaustdev","<DB_PASS>","wp_meaustdev");
$c->query("UPDATE wp_blogs SET domain=\"meaustdev.wpenginepowered.com\"");
$c->close();
'

# 2. Search-replace all remaining production URLs
wp --allow-root \
  --url="https://meaustdev.wpenginepowered.com" \
  --skip-plugins --skip-themes \
  search-replace \
  'www.mitsubishielectric.com.au' \
  'meaustdev.wpenginepowered.com' \
  --all-tables --network --skip-columns=guid

# 3. Flush cache
wp --allow-root --url="https://meaustdev.wpenginepowered.com" cache flush
```

> wp-config.php must have `$table_prefix = 'wp_';` (not `wp_2_` — WordPress multisite handles the subsite prefix internally).

---

## Incident Log

| Date | Incident | Branch | Status |
|---|---|---|---|
| 2026-04-14 | HTTP 500 on all Transportation/Rolling Stock product pages | `hotfix/rolling-stock-500-error` | Resolved |

Full incident reports: [`docs/incidents/`](docs/incidents/)
