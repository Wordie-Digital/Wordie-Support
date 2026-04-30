# Meadan Homes — WordPress Theme

Custom WordPress theme for Meadan Homes, built to Figma spec.  
**Stack:** WordPress 6.x · PHP 8.2 · ACF PRO · Gravity Forms · Swiper.js  
**Hosting:** WP Engine (Development / Staging / Production)

---

## Branch Strategy

```
main          →  WP Engine Production
staging       →  WP Engine Staging
development   →  WP Engine Development
```

| Branch | Purpose | WP Engine env |
|---|---|---|
| `main` | Production-ready code. Never commit directly — merge from `staging` only. | Production |
| `staging` | QA / client review. Merge from `development` after testing. | Staging |
| `development` | Active development and integration. Feature branches merge here first. | Development |

### Day-to-day workflow

```
feature/your-task  →  development  →  staging  →  main
```

1. **Branch off `development`** for all new work: `git checkout -b feature/HS-1234-description development`
2. **Open a PR into `development`** when ready — CI runs lint + smoke test automatically.
3. **Merge `development` → `staging`** to deploy to the staging WP Engine environment for QA.
4. **Merge `staging` → `main`** to deploy to production.

> Pushing to `main`, `staging`, or `development` automatically triggers the deploy pipeline (see below).

---

## CI/CD Pipeline

Defined in `.github/workflows/deploy.yml`.

```
Push to branch
    │
    ├── Lint (PHP CodeSniffer + ESLint)
    ├── Smoke Test (required files + PHP syntax + ACF JSON)
    └── Deploy → WP Engine via SSH Git push
```

**Secrets required in GitHub → Settings → Secrets and variables → Actions:**

| Secret | Description |
|---|---|
| `WPE_SSH_KEY` | WP Engine SSH private key (PEM format — must not have a passphrase) |
| `WPE_ENV_PRODUCTION` | WP Engine environment name for production |
| `WPE_ENV_STAGING` | WP Engine environment name for staging |
| `WPE_ENV_DEVELOPMENT` | WP Engine environment name for development |

> **Note:** If deploys are failing with `error in libcrypto`, the `WPE_SSH_KEY` secret is malformed. Re-copy the private key from WP Engine dashboard → SSH Keys, ensuring no trailing whitespace and no passphrase.

---

## Theme Structure

The theme lives in the `meadan/` directory at the repo root. WP Engine deploys the entire repo and the theme activates as `meadan`.

```
meadan/
├── functions.php          # Bootstrap: requires, enqueues, template routing
├── style.css              # Theme declaration
├── index.php / single.php / 404.php / page.php
├── header.php / footer.php
├── templates/             # Custom page/CPT templates
│   ├── single-post.php    # Blog single post (Figma 4520:33598)
│   ├── single-design.php  # Home Design CPT
│   └── ...
├── template-parts/        # Sub-sections included via get_template_part()
│   ├── post-hero.php
│   ├── post-content.php
│   ├── post-featured-design.php
│   └── post-related.php
├── blocks/                # ACF Gutenberg blocks
│   └── {block-name}/
│       ├── block.json
│       └── template.php
├── inc/                   # PHP includes
│   ├── theme-setup.php
│   ├── block-registration.php
│   ├── acf-options.php
│   ├── seed-blog-posts.php  # One-time content seeder
│   └── posts/
│       └── group-single-post.php
├── acf-fields/            # ACF local JSON (auto-synced)
└── assets/
    ├── css/
    │   ├── main.css        # Global styles + design tokens
    │   └── blocks/         # Per-block/template stylesheets
    └── js/
```

---

## Local Development

```bash
# Clone
git clone git@github.com:wordieau/Wordie-Support.git
cd Wordie-Support
git checkout development

# The meadan/ directory is the WordPress theme.
# Point your local WordPress install's wp-content/themes/meadan/
# at this directory (symlink or copy), then activate the theme.
```

---

## Key Notes for Developers

- **Never push directly to `main`** — always go through `staging` first.
- **ACF field groups** live as JSON in `meadan/acf-fields/`. After editing fields in WP Admin, commit the updated JSON.
- **CSS tokens** are in `meadan/assets/css/main.css` (`:root` variables). Use these tokens in all block CSS — no hardcoded colours.
- **Blog post seeder** (`inc/seed-blog-posts.php`) runs once on `init` and creates 3 sample posts + the `/blog/` archive page. It version-gates via `meadan_blog_seeded` in `wp_options`. To re-run: delete that option via WP Admin → Tools → Delete `meadan_blog_seeded`.
- **Deploy failures** — check GitHub Actions first. The most common cause is an expired `WPE_SSH_KEY` secret (see CI/CD section above).

---

## Archived

| Branch | Notes |
|---|---|
| `archive/resource-planner` | Previous Node.js / HubSpot resource planner app — no longer active |
