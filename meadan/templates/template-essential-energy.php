<?php
/**
 * Template Name: Essential Energy Training Academy
 *
 * Fully isolated page template — does not inherit meadan header/footer or styles.
 * ACF Flexible Content renders sections defined in acf-fields/eeta-homepage.json.
 * Run meadan/inc/eeta-seed.php via WP-CLI to populate all content:
 *   wp eval-file wp-content/themes/meadan/inc/eeta-seed.php
 */

defined( 'ABSPATH' ) || exit;

$eeta_logo = get_field( 'eeta_header_logo', 'option' );
$nav_links = get_field( 'eeta_header_nav_links', 'option' );
$cta_label = get_field( 'eeta_header_cta_label', 'option' );
$cta_url   = get_field( 'eeta_header_cta_url', 'option' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php wp_title( '|', true, 'right' ); ?><?php bloginfo( 'name' ); ?></title>
    <?php wp_head(); ?>
</head>
<body class="eeta-page">

<!-- ── Navigation ── -->
<header class="eeta-nav" role="banner">
    <div class="eeta-nav__inner">

        <a class="eeta-nav__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Essential Energy Training Academy — Home">
            <?php if ( $eeta_logo ) : ?>
                <img
                    src="<?php echo esc_url( $eeta_logo['url'] ); ?>"
                    alt="<?php echo esc_attr( $eeta_logo['alt'] ) ?: 'Essential Energy Training Academy'; ?>"
                    width="<?php echo esc_attr( $eeta_logo['width'] ); ?>"
                    height="<?php echo esc_attr( $eeta_logo['height'] ); ?>"
                />
            <?php else : ?>
                <span class="eeta-nav__logo-svg" aria-label="Essential Energy Training Academy" role="img">
                    <?php
                    $logo_file = get_template_directory() . '/assets/images/eeta/logo.svg';
                    if ( file_exists( $logo_file ) ) {
                        echo file_get_contents( $logo_file ); // phpcs:ignore WordPress.Security.EscapeOutput
                    }
                    ?>
                </span>
            <?php endif; ?>
        </a>

        <?php if ( $nav_links ) : ?>
            <ul class="eeta-nav__links" role="list">
                <?php foreach ( $nav_links as $link ) :
                    if ( empty( $link['label'] ) ) continue; ?>
                    <li>
                        <a href="<?php echo esc_url( $link['url'] ?? '#' ); ?>">
                            <?php echo esc_html( $link['label'] ); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if ( $cta_label && $cta_url ) : ?>
            <a class="eeta-nav__cta" href="<?php echo esc_url( $cta_url ); ?>">
                <?php echo esc_html( $cta_label ); ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
        <?php endif; ?>

        <button class="eeta-nav__hamburger" aria-label="Open menu" aria-expanded="false" aria-controls="eeta-mobile-menu">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 6h18M3 12h18M3 18h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        </button>
    </div>

    <nav class="eeta-nav__mobile-menu" id="eeta-mobile-menu" aria-hidden="true">
        <?php if ( $nav_links ) :
            foreach ( $nav_links as $link ) :
                if ( empty( $link['label'] ) ) continue; ?>
                <a href="<?php echo esc_url( $link['url'] ?? '#' ); ?>">
                    <?php echo esc_html( $link['label'] ); ?>
                </a>
            <?php endforeach;
        endif;
        if ( $cta_label && $cta_url ) : ?>
            <a class="eeta-nav__mobile-cta" href="<?php echo esc_url( $cta_url ); ?>">
                <?php echo esc_html( $cta_label ); ?>
            </a>
        <?php endif; ?>
    </nav>
</header>

<!-- ── Sections ── -->
<main id="enquire-anchor">
<?php
if ( have_rows( 'eeta_page_sections' ) ) :
    while ( have_rows( 'eeta_page_sections' ) ) :
        the_row();
        $layout = get_row_layout();
        get_template_part( 'template-parts/eeta/' . str_replace( '_', '-', $layout ) );
    endwhile;
endif;
?>
</main>

<!-- ── Footer ── -->
<?php
$footer_logo = get_field( 'eeta_footer_logo', 'option' );
$footer_email   = get_field( 'eeta_footer_email', 'option' );
$footer_phone   = get_field( 'eeta_footer_phone', 'option' );
$footer_address = get_field( 'eeta_footer_address', 'option' );
$ack_image      = get_field( 'eeta_footer_acknowledgement_image', 'option' );
$ack_text       = get_field( 'eeta_footer_acknowledgement_text', 'option' );
$copyright      = get_field( 'eeta_footer_copyright', 'option' );
$footer_links   = get_field( 'eeta_footer_links', 'option' );
?>
<footer class="eeta-footer" role="contentinfo">

    <div class="eeta-footer__info">
        <div class="eeta-footer__info-left">
            <?php if ( $footer_logo ) : ?>
                <img
                    src="<?php echo esc_url( $footer_logo['url'] ); ?>"
                    alt="<?php echo esc_attr( $footer_logo['alt'] ) ?: 'Essential Energy Training Academy'; ?>"
                    width="<?php echo esc_attr( $footer_logo['width'] ); ?>"
                    height="<?php echo esc_attr( $footer_logo['height'] ); ?>"
                    loading="lazy"
                    style="height:60px;width:auto;margin-bottom:16px;"
                />
            <?php endif; ?>
            <h4>Contact Us</h4>
            <p>Talk to our training team today.</p>
        </div>

        <div class="eeta-footer__contacts">
            <?php if ( $footer_email ) : ?>
                <a class="eeta-footer__contact" href="mailto:<?php echo esc_attr( $footer_email ); ?>">
                    <span class="eeta-footer__contact-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z" stroke="#002263" stroke-width="2"/><polyline points="22,6 12,13 2,6" stroke="#002263" stroke-width="2"/></svg>
                    </span>
                    <?php echo esc_html( $footer_email ); ?>
                </a>
            <?php endif; ?>
            <?php if ( $footer_address ) : ?>
                <span class="eeta-footer__contact">
                    <span class="eeta-footer__contact-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" stroke="#002263" stroke-width="2"/><circle cx="12" cy="9" r="2.5" stroke="#002263" stroke-width="2"/></svg>
                    </span>
                    <?php echo nl2br( esc_html( $footer_address ) ); ?>
                </span>
            <?php endif; ?>
            <?php if ( $footer_phone ) : ?>
                <a class="eeta-footer__contact" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $footer_phone ) ); ?>">
                    <span class="eeta-footer__contact-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M6.62 10.79a15.05 15.05 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.01-.24c1.12.37 2.33.57 3.58.57a1 1 0 0 1 1 1V20a1 1 0 0 1-1 1C9.61 21 3 14.39 3 6.5A1 1 0 0 1 4 5.5h3.5a1 1 0 0 1 1 1c0 1.25.2 2.45.57 3.58a1 1 0 0 1-.25 1.01l-2.2 2.2z" stroke="#002263" stroke-width="2"/></svg>
                    </span>
                    <?php echo esc_html( $footer_phone ); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if ( $ack_image || $ack_text ) : ?>
        <div class="eeta-footer__ack">
            <?php if ( $ack_image ) : ?>
                <img
                    class="eeta-footer__ack-image"
                    src="<?php echo esc_url( $ack_image['url'] ); ?>"
                    alt="<?php echo esc_attr( $ack_image['alt'] ); ?>"
                    width="101"
                    height="73"
                    loading="lazy"
                />
            <?php endif; ?>
            <?php if ( $ack_text ) : ?>
                <p class="eeta-footer__ack-text"><?php echo esc_html( $ack_text ); ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="eeta-footer__bottom">
        <?php if ( $copyright ) : ?>
            <p class="eeta-footer__copyright"><?php echo esc_html( $copyright ); ?></p>
        <?php endif; ?>
        <?php if ( $footer_links ) : ?>
            <ul class="eeta-footer__links" role="list">
                <?php foreach ( $footer_links as $link ) :
                    if ( empty( $link['label'] ) ) continue; ?>
                    <li><a href="<?php echo esc_url( $link['url'] ?? '#' ); ?>"><?php echo esc_html( $link['label'] ); ?></a></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

</footer>

<?php wp_footer(); ?>
</body>
</html>
