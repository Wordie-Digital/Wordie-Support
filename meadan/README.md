# Meadan WordPress Theme

Custom ACF + Gutenberg block theme for **Meadan** luxury home builders.
Built by [Wordie](https://wordie.com.au).

---

## Requirements

| Dependency | Version |
|---|---|
| WordPress | 6.4+ |
| PHP | 8.1+ |
| Advanced Custom Fields PRO | 6.x |
| Contact Form 7 | 5.x |

---

## Setup Instructions

### 1. Install plugins

Install and activate the following before activating the theme:

- **Advanced Custom Fields PRO** (required for all blocks)
- **Contact Form 7** (required for `contact-section` block)

### 2. Activate theme

Upload `/meadan` to `wp-content/themes/` and activate via **Appearance → Themes**.

### 3. Sync ACF field groups

ACF will automatically detect and load all field groups from `/acf-fields/` on theme activation.

Go to **Custom Fields → Sync** to confirm all 9 field groups are imported.

### 4. Register CPT content

Two Custom Post Types are registered automatically:
- **Projects** (slug: `project`, archive: `/projects/`)
- **Designs** (slug: `design`, archive: `/designs/`)

Add sample entries before publishing the home page so the dynamic blocks render correctly.

### 5. Build the home page

1. Create a new **Page** titled `Home`
2. Go to **Settings → Reading** and set it as the static front page
3. In the Gutenberg editor, add blocks in order from the **Meadan** category:
   - Hero Video
   - Intro / About
   - Services Section
   - Image Carousel
   - Projects Section
   - Display Home
   - Designs Section
   - News Section
   - Contact Section

### 6. Set up menus

Go to **Appearance → Menus**:
- Create a **Primary Navigation** menu (assign to "Primary Navigation" location)
- Create a **Footer Navigation** menu (assign to "Footer Navigation" location)

### 7. Configure social links

Go to **Appearance → Customize → Social Links** to set Instagram, Facebook, and LinkedIn URLs.

### 8. Upload logo

Go to **Appearance → Customize → Site Identity** and upload the Meadan logo.

---

## Branch Strategy

| Branch | Environment | WP Engine Env |
|---|---|---|
| `development` | Local / Dev | `WPE_ENV_DEVELOPMENT` |
| `staging` | Client review | `WPE_ENV_STAGING` |
| `main` | Production | `WPE_ENV_PRODUCTION` |

Pushes to any of these branches trigger the **Deploy to WP Engine** workflow automatically.

### Required GitHub Secrets

| Secret | Description |
|---|---|
| `WPE_SSH_KEY` | Contents of `~/.ssh/wpengine_ed25519` private key |
| `WPE_ENV_PRODUCTION` | WP Engine production environment name |
| `WPE_ENV_STAGING` | WP Engine staging environment name |
| `WPE_ENV_DEVELOPMENT` | WP Engine development environment name |

---

## Block Reference

| Block | Slug | Dynamic | Notes |
|---|---|---|---|
| Hero Video | `acf/hero-video` | No | MP4 video + overlay. Requires video URL |
| Intro / About | `acf/intro-about` | No | Centred text section |
| Services Section | `acf/services-section` | No | Up to 3 service cards (repeater) |
| Image Carousel | `acf/image-carousel` | No | 2–8 slides, dot pagination, autoplay optional |
| Projects Section | `acf/projects-section` | Yes | Queries `project` CPT |
| Display Home | `acf/display-home` | No | Two-column: content + 3:2 image |
| Designs Section | `acf/designs-section` | Yes | Queries `design` CPT |
| News Section | `acf/news-section` | Yes | Queries WordPress Posts |
| Contact Section | `acf/contact-section` | No | Renders CF7 shortcode |

---

## Content Editor Guide

### Hero Video
- Upload the background video to the Media Library and paste the file URL into **Background Video URL**
- Upload a **Video Poster Image** as the fallback for mobile/slow connections
- Primary CTA = solid dark button; Secondary CTA = outlined button

### Services Section
- Minimum 1, maximum 3 cards
- Each card supports an image, title, description, and an optional link

### Image Carousel
- Minimum 2 slides required
- Toggle **Autoplay** on and set speed in milliseconds (default: 4000ms)
- Always fill in **Alt Text** for accessibility

### Projects / Designs / News Sections
- These blocks pull content automatically from their respective CPTs / Posts
- Set **Number to Show** to control how many cards appear per page
- Use **View All CTA** to link to the archive page

### Contact Section
- Install Contact Form 7, create a form, and paste its shortcode into the block field

---

## Performance Notes

- Images use `loading="lazy"` except the hero poster (`loading="eager"`)
- Video autoplays muted + silent — no layout shift
- All JS is vanilla, loaded via ACF block `enqueue_script` (only on pages where the block is used)
- Design tokens use CSS custom properties — no build step required
- Fonts (Geist, Freight Neo) must be loaded via Google Fonts / Typekit in `<head>` — add to `header.php` once licence is confirmed

---

## Plugin Dependencies (manual install required)

| Plugin | Block | Why |
|---|---|---|
| Advanced Custom Fields PRO | All blocks | Field group rendering |
| Contact Form 7 | Contact Section | Form shortcode |

> 🚩 **ENGINEER REVIEW REQUIRED** — Freight Neo is a paid web font (FontSpring/Typekit). Confirm licence and add `@font-face` or embed link to `header.php` before launch.

> 🚩 **ENGINEER REVIEW REQUIRED** — The carousel and slider JS uses `card.hidden` to hide/show cards. Verify this does not conflict with any Gutenberg block wrapper elements in production.

> 🚩 **ENGINEER REVIEW REQUIRED** — `display-home` and hero video sections assume full-bleed layout with no `max-width` constraint. Confirm this matches the client's page template (no sidebar, no WP default container).

> 🚩 **ENGINEER REVIEW REQUIRED** — Gravity Forms can replace Contact Form 7 if preferred. Update the `form_shortcode` field instructions and CF7 CSS overrides in `contact-section.css`.

> 🚩 **ENGINEER REVIEW REQUIRED** — Add `add_theme_support( 'wp-block-styles' )` only if the client wants core block styles (currently omitted to keep CSS lean).
