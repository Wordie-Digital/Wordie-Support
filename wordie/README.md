# Wordie — WordPress Theme

Custom ACF-first WordPress theme for [Wordie Digital](https://wordie.com.au).  
**Stack:** WordPress 6.x · PHP 8.2 · ACF Pro · Gutenberg (ACF blocks only) · No page builders  
**Hosting:** WP Engine (Development / Staging / Production)

---

## Branch Strategy

```
main          →  WP Engine Production   (protected — merge from staging only)
staging       →  WP Engine Staging      (auto-deploy on push)
development   →  WP Engine Development  (auto-deploy on push)
feature/*     →  PR into development
```

### Day-to-day workflow

```
feature/WD-####-description  →  development  →  staging  →  main
```

1. Branch off `development`: `git checkout -b feature/WD-1234-hero-block development`
2. Push + open PR into `development` — CI runs lint + smoke test automatically
3. Merge `development → staging` for client/QA review on WP Engine Staging
4. Merge `staging → main` to deploy to production

> **Never push directly to `main`.** Never push directly to WP Engine — all deploys go through GitHub Actions.

---

## CI/CD Pipeline

Defined in `.github/workflows/deploy-wordie.yml`.

Triggers only when files in `wordie/**` change.

```
Push to branch
    │
    ├── Lint (PHP CodeSniffer + PHP syntax + ACF JSON validation)
    ├── Smoke Test (required files present)
    └── Deploy → WP Engine via SSH Git push
        └── HTTP 200 smoke test post-deploy
```

### Required GitHub Secrets

| Secret                      | Description                                          |
|-----------------------------|------------------------------------------------------|
| `WPE_SSH_KEY_PRIVATE`       | WP Engine SSH private key (PEM, no passphrase)       |
| `WPE_WORDIE_ENV_PRODUCTION` | WP Engine environment name for production            |
| `WPE_WORDIE_ENV_STAGING`    | WP Engine environment name for staging               |
| `WPE_WORDIE_ENV_DEVELOPMENT`| WP Engine environment name for development           |

---

## Theme Structure

```
wordie/
├── style.css                     # Theme declaration
├── functions.php                 # Bootstrap (requires inc/ files, ACF sync, enqueue)
├── index.php                     # WordPress fallback template
├── front-page.php                # Homepage template
├── page.php                      # Generic page template
├── header.php                    # Site header + navigation
├── footer.php                    # Site footer (wp_footer)
├── 404.php                       # Not found page
├── inc/
│   ├── theme-setup.php           # Theme support, image sizes, nav menus
│   ├── block-registration.php    # ACF block registration + allowed blocks filter
│   ├── acf-options.php           # Navigation + Footer options pages
│   └── ai-endpoints.php          # /wp-json/ai/v1/* REST endpoints
├── blocks/
│   └── hero-banner/
│       └── template.php          # Hero block PHP template
├── acf-fields/
│   ├── hero-banner.json          # Import via ACF → Tools → Import JSON
│   └── global-options.json       # Navigation + Footer options field groups
├── assets/
│   ├── css/
│   │   ├── global.css            # All CSS tokens + base styles
│   │   ├── utilities.css         # Layout helpers
│   │   └── blocks/
│   │       └── hero-banner.css   # Hero-specific styles
│   └── js/
│       └── navigation.js         # Mobile nav toggle
└── llms.txt                      # AI/LLM content map
```

---

## Local Development Setup

### Prerequisites
- WordPress 6.x local install (Local by Flywheel, DDEV, or Lando)
- ACF Pro plugin activated
- PHP 8.2+

### Steps

1. Clone the repo and ensure you're on `development`:
   ```bash
   git clone git@github.com:wordie-digital/<repo>.git
   git checkout development
   ```

2. Symlink or copy the `wordie/` folder into your local WordPress install:
   ```
   wp-content/themes/wordie/
   ```

3. Activate the Wordie theme in WP Admin → Appearance → Themes.

4. Import ACF field groups:
   - WP Admin → Custom Fields → Tools → Import
   - Import `acf-fields/hero-banner.json`
   - Import `acf-fields/global-options.json`

5. Configure global options:
   - WP Admin → Theme Settings → Navigation → set logo + nav CTA
   - WP Admin → Theme Settings → Footer → set contact info

6. Create a Homepage page, set it as the static front page (Settings → Reading).

7. Add the **Hero Banner** ACF block to the homepage via the block editor.

---

## ACF Block System

Blocks are registered via `acf_register_block_type()` in `inc/block-registration.php`.  
**There is no `block.json` for layout blocks.** ACF handles all registration.

### Adding a new block

1. Register it in `inc/block-registration.php` (add to the `$blocks` array)
2. Create `blocks/{block-name}/template.php`
3. Create `assets/css/blocks/{block-name}.css`
4. Create `acf-fields/{block-name}.json` and import it via ACF → Tools → Import
5. Add the block slug to the `allowed_block_types_all` filter

---

## Design Tokens

All tokens are CSS custom properties in `assets/css/global.css`.

| Token                  | Value     | Usage                      |
|------------------------|-----------|----------------------------|
| `--colour-coral`       | `#F5634D` | Primary CTAs, accents      |
| `--colour-dark-teal`   | `#0A3542` | Dark section backgrounds   |
| `--colour-white`       | `#F6F9F9` | Light backgrounds, text    |
| `--colour-dark-grey`   | `#062028` | Heading text               |
| `--colour-text-body`   | `#505c5f` | Body copy                  |
| `--font-heading`       | Montserrat Bold | All headings + CTAs |
| `--font-body`          | Open Sans Regular | Body copy        |
| `--radius-cta`         | `12px`    | Button border-radius       |
| `--radius-image`       | `12px`    | Card/image border-radius   |
| `--margin-lr`          | `72px`    | Desktop side margin        |

---

## Content Editor Guide

### Homepage Hero

1. Open the Homepage in WP Admin → Pages → Edit
2. In the block editor, add an **ACF / Hero Banner** block
3. Fill in:
   - **Heading** — H1 text (max 100 chars, ~12 words)
   - **Subheading** — 1–2 sentence description (max 200 chars)
   - **Primary CTA** — Link URL + button label (e.g. "Start a project")
   - **Secondary CTA** — Optional second button (e.g. "Book a 30-min strategy session")
   - **Background Image** — Optional 1440×810px WebP/JPG

### Navigation

1. WP Admin → Theme Settings → Navigation
2. Upload logo and set the "Brief us" CTA link

### Menus

1. WP Admin → Appearance → Menus
2. Create a menu and assign it to the **Primary Navigation** location
3. Add pages: Services, Our Work, Why Wordie

---

## AI REST Endpoints

| Endpoint                          | Returns                                          |
|-----------------------------------|--------------------------------------------------|
| `GET /wp-json/ai/v1/site-map`     | All published pages + CPTs in flat JSON          |
| `GET /wp-json/ai/v1/content-model`| Block inventory + ACF field structure            |
| `GET /wp-json/ai/v1/page/{id}`    | Page title, URL + all ACF field values           |

> 🚩 `permission_callback` is currently open (`__return_true`). Review before go-live.

---

## Block Inventory (v1.0 — Hero only)

| Section          | Block Slug       | Status    |
|------------------|------------------|-----------|
| Hero             | `hero-banner`    | ✅ Built  |
| Client Logos     | `client-logos`   | ✅ Built  |
| Services Grid    | `services-grid`  | Planned   |
| Services Grid    | `services-grid`  | Planned   |
| Work Carousel    | `work-section`   | Planned   |
| Why Wordie Steps | `why-wordie`     | Planned   |
| Mid CTA Banner   | `cta-banner`     | Planned   |
| Tech Stack       | `tech-stack`     | Planned   |
| Process Steps    | `process-steps`  | Planned   |
| Testimonial      | `testimonial`    | Planned   |
| Footer CTA       | `bottom-cta`     | Planned   |
