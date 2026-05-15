<?php
/**
 * Block: Display Home
 * Slug: acf/display-home
 *
 * Renders in one of two modes:
 *
 *   Card grid mode  — when selected_display_homes relationship field is populated.
 *                     Shows a grid of Display Home CPT cards (image, title, excerpt, CTA).
 *
 *   Promo mode      — default / fallback. The original two-column static promo layout
 *                     (manual title, description, CTA, and image). No CPT query.
 */

defined( 'ABSPATH' ) || exit;

$section_label         = get_field( 'section_label' );
$cta_label             = get_field( 'cta_label' );
$cta_url               = get_field( 'cta_url' );
$selected_display_homes = get_field( 'selected_display_homes' ); // WP_Post[] or []

$block_id = ! empty( $block['anchor'] ) ? ' id="' . esc_attr( $block['anchor'] ) . '"' : '';

// ── Card grid mode — selected Display Home CPT entries ─────────────────────
if ( ! empty( $selected_display_homes ) ) :
    $post_ids = wp_list_pluck( $selected_display_homes, 'ID' );
    $display_homes = new WP_Query( [
        'post_type'      => 'display-home',
        'post__in'       => $post_ids,
        'orderby'        => 'post__in',
        'posts_per_page' => count( $post_ids ),
        'post_status'    => 'publish',
        'no_found_rows'  => true,
    ] );
?>
<section class="display-home display-home--grid"<?php echo $block_id; ?>>
    <?php if ( $section_label ) : ?>
        <p class="display-home__label"><?php echo esc_html( $section_label ); ?></p>
    <?php endif; ?>

    <?php if ( $display_homes->have_posts() ) : ?>
        <div class="display-home__grid">
            <?php while ( $display_homes->have_posts() ) : $display_homes->the_post(); ?>
                <article class="display-home-card">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <a class="display-home-card__image-link" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
                            <?php the_post_thumbnail( 'meadan-card', [ 'class' => 'display-home-card__image', 'loading' => 'lazy' ] ); ?>
                        </a>
                    <?php endif; ?>
                    <div class="display-home-card__body">
                        <h3 class="display-home-card__title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        <?php if ( get_the_excerpt() ) : ?>
                            <p class="display-home-card__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
                        <?php endif; ?>
                        <a class="display-home-card__link" href="<?php the_permalink(); ?>">
                            <?php esc_html_e( 'View Home', 'meadan' ); ?>
                        </a>
                    </div>
                </article>
            <?php endwhile;
            wp_reset_postdata(); ?>
        </div>
    <?php endif; ?>

    <?php if ( $cta_label && $cta_url ) : ?>
        <div class="display-home__footer">
            <a class="btn btn--primary" href="<?php echo esc_url( $cta_url ); ?>">
                <?php echo esc_html( $cta_label ); ?>
            </a>
        </div>
    <?php endif; ?>
</section>

<?php
// ── Promo mode — static two-column layout (default / fallback) ─────────────
else :
    $title       = get_field( 'title' );
    $description = get_field( 'description' );
    $image       = get_field( 'image' );
?>
<section class="display-home"<?php echo $block_id; ?>>
    <div class="display-home__content">
        <?php if ( $section_label ) : ?>
            <p class="display-home__label"><?php echo esc_html( $section_label ); ?></p>
        <?php endif; ?>

        <?php if ( $title ) : ?>
            <h2 class="display-home__title"><?php echo esc_html( $title ); ?></h2>
        <?php endif; ?>

        <?php if ( $description ) : ?>
            <p class="display-home__description"><?php echo esc_html( $description ); ?></p>
        <?php endif; ?>

        <?php if ( $cta_label && $cta_url ) : ?>
            <a class="btn btn--primary" href="<?php echo esc_url( $cta_url ); ?>">
                <?php echo esc_html( $cta_label ); ?>
            </a>
        <?php endif; ?>
    </div>

    <?php if ( $image ) : ?>
        <figure class="display-home__image-wrap">
            <img
                class="display-home__image"
                src="<?php echo esc_url( $image['url'] ); ?>"
                alt="<?php echo esc_attr( $image['alt'] ); ?>"
                loading="lazy"
                width="<?php echo esc_attr( $image['width'] ); ?>"
                height="<?php echo esc_attr( $image['height'] ); ?>"
            >
        </figure>
    <?php endif; ?>
</section>

<?php endif; ?>
