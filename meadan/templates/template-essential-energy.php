<?php
/**
 * Template Name: Essential Energy Training Academy
 *
 * Fully isolated page template — does not inherit meadan header/footer or styles.
 * ACF Flexible Content renders sections defined in acf-fields/eeta-homepage.json.
 */

defined( 'ABSPATH' ) || exit;

$eeta_logo    = get_field( 'eeta_header_logo', 'option' );
$nav_links    = get_field( 'eeta_header_nav_links', 'option' );
$cta_label    = get_field( 'eeta_header_cta_label', 'option' );
$cta_url      = get_field( 'eeta_header_cta_url', 'option' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php wp_title( '|', true, 'right' ); ?><?php bloginfo( 'name' ); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <?php wp_head(); ?>
</head>
<body class="eeta-page">

<!-- =====================================================================
     Navigation
     ===================================================================== -->
<header class="eeta-nav" role="banner">
    <div class="eeta-nav__inner">

        <?php if ( $eeta_logo ) : ?>
            <a class="eeta-nav__logo-link" href="<?php echo esc_url( get_permalink() ); ?>" aria-label="Essential Energy Training Academy — Home">
                <img
                    class="eeta-nav__logo"
                    src="<?php echo esc_url( $eeta_logo['url'] ); ?>"
                    alt="<?php echo esc_attr( $eeta_logo['alt'] ) ?: 'Essential Energy Training Academy'; ?>"
                    width="<?php echo esc_attr( $eeta_logo['width'] ); ?>"
                    height="<?php echo esc_attr( $eeta_logo['height'] ); ?>"
                />
            </a>
        <?php else : ?>
            <a class="eeta-nav__logo-link eeta-nav__logo-link--text" href="<?php echo esc_url( get_permalink() ); ?>">
                Essential Energy Training Academy
            </a>
        <?php endif; ?>

        <nav class="eeta-nav__links" aria-label="Primary navigation">
            <?php if ( $nav_links ) :
                foreach ( $nav_links as $link ) :
                    if ( empty( $link['label'] ) || empty( $link['url'] ) ) continue;
            ?>
                <a class="eeta-nav__link" href="<?php echo esc_url( $link['url'] ); ?>">
                    <?php echo esc_html( $link['label'] ); ?>
                </a>
            <?php
                endforeach;
            endif; ?>
        </nav>

        <?php if ( $cta_label && $cta_url ) : ?>
            <a class="eeta-btn eeta-btn--green eeta-nav__cta" href="<?php echo esc_url( $cta_url ); ?>">
                <?php echo esc_html( $cta_label ); ?>
            </a>
        <?php endif; ?>

        <button class="eeta-nav__hamburger" aria-label="Open menu" aria-expanded="false" aria-controls="eeta-mobile-menu">
            <span></span><span></span><span></span>
        </button>
    </div>

    <div class="eeta-nav__mobile-menu" id="eeta-mobile-menu" hidden>
        <?php if ( $nav_links ) :
            foreach ( $nav_links as $link ) :
                if ( empty( $link['label'] ) || empty( $link['url'] ) ) continue;
        ?>
            <a class="eeta-nav__mobile-link" href="<?php echo esc_url( $link['url'] ); ?>">
                <?php echo esc_html( $link['label'] ); ?>
            </a>
        <?php
            endforeach;
        endif;
        if ( $cta_label && $cta_url ) : ?>
            <a class="eeta-btn eeta-btn--green eeta-nav__mobile-cta" href="<?php echo esc_url( $cta_url ); ?>">
                <?php echo esc_html( $cta_label ); ?>
            </a>
        <?php endif; ?>
    </div>
</header>

<!-- =====================================================================
     Flexible Content Sections
     ===================================================================== -->
<main class="eeta-main" id="main">
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

<!-- =====================================================================
     Footer
     ===================================================================== -->
<?php
$footer_logo           = get_field( 'eeta_footer_logo', 'option' );
$footer_email          = get_field( 'eeta_footer_email', 'option' );
$footer_phone          = get_field( 'eeta_footer_phone', 'option' );
$footer_address        = get_field( 'eeta_footer_address', 'option' );
$ack_image             = get_field( 'eeta_footer_acknowledgement_image', 'option' );
$ack_text              = get_field( 'eeta_footer_acknowledgement_text', 'option' );
$copyright             = get_field( 'eeta_footer_copyright', 'option' );
$footer_links          = get_field( 'eeta_footer_links', 'option' );
?>

<footer class="eeta-footer" role="contentinfo">

    <div class="eeta-footer__company">
        <div class="eeta-footer__company-inner">

            <?php if ( $footer_logo ) : ?>
                <a class="eeta-footer__logo-link" href="<?php echo esc_url( get_permalink() ); ?>" aria-label="Essential Energy Training Academy">
                    <img class="eeta-footer__logo"
                        src="<?php echo esc_url( $footer_logo['url'] ); ?>"
                        alt="<?php echo esc_attr( $footer_logo['alt'] ) ?: 'Essential Energy Training Academy'; ?>"
                        width="<?php echo esc_attr( $footer_logo['width'] ); ?>"
                        height="<?php echo esc_attr( $footer_logo['height'] ); ?>"
                        loading="lazy" />
                </a>
            <?php endif; ?>

            <div class="eeta-footer__contact">
                <?php if ( $footer_email ) : ?>
                    <a class="eeta-footer__contact-item" href="mailto:<?php echo esc_attr( $footer_email ); ?>">
                        <svg class="eeta-footer__icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M2 3h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z" stroke="currentColor" stroke-width="1.5"/><path d="M1 4l7 5 7-5" stroke="currentColor" stroke-width="1.5"/></svg>
                        <?php echo esc_html( $footer_email ); ?>
                    </a>
                <?php endif; ?>
                <?php if ( $footer_address ) : ?>
                    <span class="eeta-footer__contact-item">
                        <svg class="eeta-footer__icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M8 1a5 5 0 0 1 5 5c0 3.5-5 9-5 9S3 9.5 3 6a5 5 0 0 1 5-5z" stroke="currentColor" stroke-width="1.5"/><circle cx="8" cy="6" r="1.5" stroke="currentColor" stroke-width="1.5"/></svg>
                        <?php echo nl2br( esc_html( $footer_address ) ); ?>
                    </span>
                <?php endif; ?>
                <?php if ( $footer_phone ) : ?>
                    <a class="eeta-footer__contact-item" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $footer_phone ) ); ?>">
                        <svg class="eeta-footer__icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 2h3l1.5 3.5-1.5 1a8 8 0 0 0 3.5 3.5l1-1.5L14 10v3a1 1 0 0 1-1 1A12 12 0 0 1 2 3a1 1 0 0 1 1-1z" stroke="currentColor" stroke-width="1.5"/></svg>
                        <?php echo esc_html( $footer_phone ); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if ( $ack_image || $ack_text ) : ?>
        <div class="eeta-footer__acknowledgement">
            <div class="eeta-footer__acknowledgement-inner">
                <?php if ( $ack_image ) : ?>
                    <img class="eeta-footer__acknowledgement-img"
                        src="<?php echo esc_url( $ack_image['url'] ); ?>"
                        alt="<?php echo esc_attr( $ack_image['alt'] ); ?>"
                        width="101" height="73" loading="lazy" />
                <?php endif; ?>
                <?php if ( $ack_text ) : ?>
                    <p class="eeta-footer__acknowledgement-text"><?php echo esc_html( $ack_text ); ?></p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="eeta-footer__bar">
        <div class="eeta-footer__bar-inner">
            <?php if ( $copyright ) : ?>
                <p class="eeta-footer__copyright"><?php echo esc_html( $copyright ); ?></p>
            <?php endif; ?>
            <?php if ( $footer_links ) : ?>
                <nav class="eeta-footer__links" aria-label="Footer links">
                    <?php foreach ( $footer_links as $link ) :
                        if ( empty( $link['label'] ) || empty( $link['url'] ) ) continue;
                    ?>
                        <a class="eeta-footer__link" href="<?php echo esc_url( $link['url'] ); ?>"><?php echo esc_html( $link['label'] ); ?></a>
                    <?php endforeach; ?>
                </nav>
            <?php endif; ?>
        </div>
    </div>

</footer>

<?php wp_footer(); ?>
</body>
</html>
