<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header" id="site-header" role="banner">
    <div class="site-header__inner">

        <a
            class="site-header__logo-link"
            href="<?php echo esc_url( home_url( '/' ) ); ?>"
            aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?> &mdash; <?php esc_attr_e( 'Home', 'meadan' ); ?>"
        >
            <?php
            // Prefer ACF global options logo, fall back to WP custom logo, then site name.
            $acf_logo = function_exists( 'get_field' ) ? get_field( 'site_logo', 'option' ) : false;

            if ( $acf_logo ) :
                // ACF returns array when return_format is 'array'
                if ( is_array( $acf_logo ) ) : ?>
                    <img
                        class="site-header__logo-img"
                        src="<?php echo esc_url( $acf_logo['url'] ); ?>"
                        alt="<?php echo esc_attr( $acf_logo['alt'] ?: get_bloginfo( 'name' ) ); ?>"
                        width="<?php echo esc_attr( $acf_logo['width'] ); ?>"
                        height="<?php echo esc_attr( $acf_logo['height'] ); ?>"
                        loading="eager"
                    >
                <?php else :
                    // ACF returned an attachment ID
                    echo wp_get_attachment_image(
                        (int) $acf_logo,
                        'full',
                        false,
                        [ 'class' => 'site-header__logo-img', 'loading' => 'eager' ]
                    );
                endif;

            elseif ( has_custom_logo() ) :
                $custom_logo_id = get_theme_mod( 'custom_logo' );
                echo wp_get_attachment_image(
                    $custom_logo_id,
                    'full',
                    false,
                    [ 'class' => 'site-header__logo-img', 'loading' => 'eager' ]
                );

            else :
                echo '<span class="site-header__logo-text">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
            endif;
            ?>
        </a>

        <button
            class="site-header__nav-toggle"
            type="button"
            aria-controls="primary-navigation"
            aria-expanded="false"
            aria-label="<?php esc_attr_e( 'Toggle navigation menu', 'meadan' ); ?>"
        >
            <span class="site-header__nav-toggle-bar" aria-hidden="true"></span>
            <span class="site-header__nav-toggle-bar" aria-hidden="true"></span>
            <span class="site-header__nav-toggle-bar" aria-hidden="true"></span>
        </button>

        <nav
            class="site-header__nav"
            id="primary-navigation"
            aria-label="<?php esc_attr_e( 'Primary', 'meadan' ); ?>"
        >
            <?php
            wp_nav_menu( [
                'theme_location' => 'primary',
                'menu_class'     => 'site-header__nav-list',
                'container'      => false,
                'fallback_cb'    => false,
                'depth'          => 2,
            ] );
            ?>
        </nav>

        <?php
        // Contact Us CTA — pill button, right side of header
        $contact_url   = function_exists( 'get_field' ) ? get_field( 'header_cta_url', 'option' ) : '';
        $contact_label = function_exists( 'get_field' ) ? get_field( 'header_cta_label', 'option' ) : '';
        $contact_url   = $contact_url   ?: get_page_link( get_page_by_path( 'contact' ) );
        $contact_label = $contact_label ?: __( 'Contact Us', 'meadan' );
        if ( $contact_url ) : ?>
            <div class="site-header__cta">
                <a class="btn btn--outline-light" href="<?php echo esc_url( $contact_url ); ?>">
                    <?php echo esc_html( $contact_label ); ?>
                </a>
            </div>
        <?php endif; ?>

    </div><!-- .site-header__inner -->
</header><!-- .site-header -->
