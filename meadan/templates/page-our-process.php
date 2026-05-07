<?php
/**
 * Template Name: Our Process
 *
 * Meadan — templates/page-our-process.php
 *
 * Used by both:
 *   /our-process-build-to-your-plans/
 *   /our-process-design-and-build/
 *
 * Sections:
 *   1. Process hero    — stone bg, breadcrumbs, "OUR PROCESS" label, page title, intro
 *   2. Process steps   — numbered steps from ACF repeater field 'process_steps'
 *                        Falls back to block content (the_content) if no ACF steps set.
 *   3. Why Meadan      — two-col: text + image (ACF fields)
 *   4. Process CTA     — sand bg, heading + dual CTAs
 *
 * ACF fields expected (all optional — page gracefully degrades without them):
 *   - process_intro        (textarea)      Short intro paragraph under the title
 *   - process_steps        (repeater)
 *       → step_number      (text)          e.g. "01"
 *       → step_title       (text)
 *       → step_description (textarea)
 *       → step_image       (image)
 *   - why_heading          (text)
 *   - why_description      (textarea)
 *   - why_points           (repeater)
 *       → point            (text)
 *   - why_image            (image)
 *   - cta_heading          (text)
 *   - cta_subheading       (text)
 *   - cta_primary_label    (text)
 *   - cta_primary_url      (url)
 *   - cta_secondary_label  (text)
 *   - cta_secondary_url    (url)
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
    the_post();

    // ── ACF field pulls ───────────────────────────────────────────────
    $has_acf = function_exists( 'get_field' );

    $process_intro   = $has_acf ? get_field( 'process_intro' )  : '';
    $process_steps   = $has_acf ? get_field( 'process_steps' )  : [];
    $why_heading     = $has_acf ? get_field( 'why_heading' )     : '';
    $why_description = $has_acf ? get_field( 'why_description' ) : '';
    $why_points      = $has_acf ? get_field( 'why_points' )      : [];
    $why_image       = $has_acf ? get_field( 'why_image' )       : null;
    $cta_heading     = $has_acf ? get_field( 'cta_heading' )     : __( 'Ready to Start Building?', 'meadan' );
    $cta_subheading  = $has_acf ? get_field( 'cta_subheading' )  : '';
    $cta_primary_label   = $has_acf ? get_field( 'cta_primary_label' )   : __( 'Get in Touch', 'meadan' );
    $cta_primary_url     = $has_acf ? get_field( 'cta_primary_url' )     : home_url( '/contact' );
    $cta_secondary_label = $has_acf ? get_field( 'cta_secondary_label' ) : '';
    $cta_secondary_url   = $has_acf ? get_field( 'cta_secondary_url' )   : '';
?>

    <main class="site-main site-main--our-process" id="main" role="main">

        <!-- ── 1. Process hero ──────────────────────────────────────── -->
        <section class="process-hero">
            <div class="process-hero__inner">

                <!-- Breadcrumbs -->
                <nav class="process-hero__breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'meadan' ); ?>">
                    <ol class="breadcrumbs__list">
                        <li class="breadcrumbs__item">
                            <a class="breadcrumbs__link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
                                <?php esc_html_e( 'Home', 'meadan' ); ?>
                            </a>
                        </li>
                        <li class="breadcrumbs__separator" aria-hidden="true">/</li>
                        <li class="breadcrumbs__item breadcrumbs__item--current" aria-current="page">
                            <?php the_title(); ?>
                        </li>
                    </ol>
                </nav>

                <div class="process-hero__content">
                    <p class="process-hero__label"><?php esc_html_e( 'OUR PROCESS', 'meadan' ); ?></p>
                    <h1 class="process-hero__title"><?php the_title(); ?></h1>
                    <?php if ( $process_intro ) : ?>
                        <p class="process-hero__intro"><?php echo esc_html( $process_intro ); ?></p>
                    <?php elseif ( get_the_excerpt() ) : ?>
                        <p class="process-hero__intro"><?php echo esc_html( get_the_excerpt() ); ?></p>
                    <?php endif; ?>
                </div>

            </div><!-- .process-hero__inner -->
        </section><!-- .process-hero -->

        <!-- ── 2. Process steps ─────────────────────────────────────── -->
        <?php if ( ! empty( $process_steps ) ) : ?>

            <section class="process-steps" aria-label="<?php esc_attr_e( 'Our process steps', 'meadan' ); ?>">
                <div class="process-steps__inner">
                    <ol class="process-steps__list">
                        <?php foreach ( $process_steps as $step_index => $step ) :
                            $step_number      = ! empty( $step['step_number'] )      ? $step['step_number']      : str_pad( $step_index + 1, 2, '0', STR_PAD_LEFT );
                            $step_title       = ! empty( $step['step_title'] )       ? $step['step_title']       : '';
                            $step_description = ! empty( $step['step_description'] ) ? $step['step_description'] : '';
                            $step_image       = ! empty( $step['step_image'] )       ? $step['step_image']       : null;
                        ?>
                            <li class="process-step" id="step-<?php echo esc_attr( $step_index + 1 ); ?>">

                                <div class="process-step__number" aria-hidden="true">
                                    <?php echo esc_html( $step_number ); ?>
                                </div>

                                <div class="process-step__content">
                                    <?php if ( $step_title ) : ?>
                                        <h2 class="process-step__title"><?php echo esc_html( $step_title ); ?></h2>
                                    <?php endif; ?>
                                    <?php if ( $step_description ) : ?>
                                        <p class="process-step__description"><?php echo esc_html( $step_description ); ?></p>
                                    <?php endif; ?>
                                </div>

                                <?php if ( $step_image && is_array( $step_image ) ) : ?>
                                    <figure class="process-step__image-wrap">
                                        <img
                                            class="process-step__image"
                                            src="<?php echo esc_url( $step_image['url'] ); ?>"
                                            alt="<?php echo esc_attr( $step_image['alt'] ?: $step_title ); ?>"
                                            width="<?php echo esc_attr( $step_image['width'] ); ?>"
                                            height="<?php echo esc_attr( $step_image['height'] ); ?>"
                                            loading="<?php echo $step_index === 0 ? 'eager' : 'lazy'; ?>"
                                        >
                                    </figure>
                                <?php endif; ?>

                            </li><!-- .process-step -->
                        <?php endforeach; ?>
                    </ol><!-- .process-steps__list -->
                </div><!-- .process-steps__inner -->
            </section><!-- .process-steps -->

        <?php elseif ( have_blocks() ) : ?>
            <!-- Block content fallback (Gutenberg / ACF blocks added in editor) -->
            <div class="process-block-content">
                <?php the_content(); ?>
            </div>

        <?php else : ?>
            <!-- Plain content fallback -->
            <div class="process-block-content process-block-content--plain">
                <div class="process-block-content__inner">
                    <?php the_content(); ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- ── 3. Why Meadan ────────────────────────────────────────── -->
        <?php if ( $why_heading || $why_description || $why_image ) : ?>
            <section class="process-why">
                <div class="process-why__inner">

                    <div class="process-why__text">
                        <?php if ( $why_heading ) : ?>
                            <h2 class="process-why__heading"><?php echo esc_html( $why_heading ); ?></h2>
                        <?php endif; ?>
                        <?php if ( $why_description ) : ?>
                            <p class="process-why__description"><?php echo esc_html( $why_description ); ?></p>
                        <?php endif; ?>
                        <?php if ( ! empty( $why_points ) ) : ?>
                            <ul class="process-why__points">
                                <?php foreach ( $why_points as $item ) :
                                    if ( empty( $item['point'] ) ) continue;
                                ?>
                                    <li class="process-why__point">
                                        <span class="process-why__point-icon" aria-hidden="true">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                <path d="M3 8l4 4 6-7" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </span>
                                        <?php echo esc_html( $item['point'] ); ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div><!-- .process-why__text -->

                    <?php if ( $why_image && is_array( $why_image ) ) : ?>
                        <figure class="process-why__image-wrap">
                            <img
                                class="process-why__image"
                                src="<?php echo esc_url( $why_image['url'] ); ?>"
                                alt="<?php echo esc_attr( $why_image['alt'] ); ?>"
                                width="<?php echo esc_attr( $why_image['width'] ); ?>"
                                height="<?php echo esc_attr( $why_image['height'] ); ?>"
                                loading="lazy"
                            >
                        </figure>
                    <?php endif; ?>

                </div><!-- .process-why__inner -->
            </section><!-- .process-why -->
        <?php endif; ?>

        <!-- ── 4. Process CTA ────────────────────────────────────────── -->
        <section class="process-cta">
            <div class="process-cta__inner">
                <div class="process-cta__text">
                    <?php if ( $cta_heading ) : ?>
                        <h2 class="process-cta__heading"><?php echo esc_html( $cta_heading ); ?></h2>
                    <?php endif; ?>
                    <?php if ( $cta_subheading ) : ?>
                        <p class="process-cta__subheading"><?php echo esc_html( $cta_subheading ); ?></p>
                    <?php endif; ?>
                </div>
                <div class="process-cta__buttons">
                    <?php if ( $cta_primary_label && $cta_primary_url ) : ?>
                        <a class="btn btn--primary" href="<?php echo esc_url( $cta_primary_url ); ?>">
                            <?php echo esc_html( $cta_primary_label ); ?>
                        </a>
                    <?php endif; ?>
                    <?php if ( $cta_secondary_label && $cta_secondary_url ) : ?>
                        <a class="btn btn--outline" href="<?php echo esc_url( $cta_secondary_url ); ?>">
                            <?php echo esc_html( $cta_secondary_label ); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div><!-- .process-cta__inner -->
        </section><!-- .process-cta -->

    </main><!-- .site-main -->

<?php endwhile; ?>

<?php get_footer();
