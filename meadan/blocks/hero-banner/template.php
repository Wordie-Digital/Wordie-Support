<?php
/**
 * Block: Hero Banner
 * Template: blocks/hero-banner/template.php
 *
 * Fields:
 *  - heading         (text)    Required
 *  - subheading      (text)
 *  - background_image (image array)
 *  - cta_text        (text)
 *  - cta_link        (url)
 */

defined( 'ABSPATH' ) || exit;

// Retrieve fields
$heading    = get_field( 'heading' );
$subheading = get_field( 'subheading' );
$bg_image   = get_field( 'background_image' );
$cta_text   = get_field( 'cta_text' );
$cta_link   = get_field( 'cta_link' );

// Block attributes for editor ID / className support
$block_id    = ! empty( $block['anchor'] ) ? $block['anchor'] : 'hero-banner-' . $block['id'];
$extra_class = ! empty( $block['className'] ) ? ' ' . $block['className'] : '';

// Build inline background style
$bg_style   = '';
$has_image  = ! empty( $bg_image ) && ! empty( $bg_image['url'] );

if ( $has_image ) {
    $bg_url   = esc_url( $bg_image['url'] );
    $bg_style = ' style="background-image: url(\'' . $bg_url . '\');"';
}
?>

<section
    id="<?php echo esc_attr( $block_id ); ?>"
    class="hero-banner<?php echo esc_attr( $extra_class ); ?><?php echo $has_image ? '' : ' hero-banner--no-image'; ?>"
    <?php echo $bg_style; // Already escaped above ?>
    data-block-id="<?php echo esc_attr( $block['id'] ); ?>"
    aria-label="<?php echo $heading ? esc_attr( $heading ) : esc_attr__( 'Hero Banner', 'meadan' ); ?>"
>
    <!-- Overlay -->
    <div class="hero-banner__overlay" aria-hidden="true"></div>

    <!-- Content -->
    <div class="hero-banner__inner">

        <?php if ( $heading ) : ?>
            <h1 class="hero-banner__heading">
                <?php echo esc_html( $heading ); ?>
            </h1>
        <?php else : ?>
            <!-- Editor placeholder when no heading is set -->
            <p class="hero-banner__placeholder-label">
                <?php esc_html_e( 'Add a heading in the block settings panel.', 'meadan' ); ?>
            </p>
        <?php endif; ?>

        <?php if ( $subheading ) : ?>
            <p class="hero-banner__subheading">
                <?php echo esc_html( $subheading ); ?>
            </p>
        <?php endif; ?>

        <?php if ( $cta_text && $cta_link ) : ?>
            <a
                class="hero-banner__cta btn btn--primary"
                href="<?php echo esc_url( $cta_link ); ?>"
            >
                <?php echo esc_html( $cta_text ); ?>
            </a>
        <?php elseif ( $cta_text ) : ?>
            <!-- CTA text set but no link — render as span so editor can see it -->
            <span class="hero-banner__cta btn btn--primary btn--disabled">
                <?php echo esc_html( $cta_text ); ?>
            </span>
        <?php endif; ?>

    </div><!-- .hero-banner__inner -->

</section><!-- .hero-banner -->
