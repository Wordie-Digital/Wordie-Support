<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="eeta-nav" role="banner">
    <div class="eeta-nav__inner">

        <?php
        $logo = get_field( 'header_logo', 'option' );
        if ( $logo ) :
        ?>
            <a class="eeta-nav__logo-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php bloginfo( 'name' ); ?> — Home">
                <img
                    class="eeta-nav__logo"
                    src="<?php echo esc_url( $logo['url'] ); ?>"
                    alt="<?php echo esc_attr( $logo['alt'] ) ?: esc_attr( get_bloginfo( 'name' ) ); ?>"
                    width="<?php echo esc_attr( $logo['width'] ); ?>"
                    height="<?php echo esc_attr( $logo['height'] ); ?>"
                />
            </a>
        <?php else : ?>
            <a class="eeta-nav__logo-link eeta-nav__logo-link--text" href="<?php echo esc_url( home_url( '/' ) ); ?>">
                <?php bloginfo( 'name' ); ?>
            </a>
        <?php endif; ?>

        <nav class="eeta-nav__links" aria-label="Primary navigation">
            <?php
            $nav_links = get_field( 'header_nav_links', 'option' );
            if ( $nav_links ) :
                foreach ( $nav_links as $link ) :
                    if ( empty( $link['label'] ) || empty( $link['url'] ) ) continue;
                    $is_current = trailingslashit( home_url( $_SERVER['REQUEST_URI'] ) ) === trailingslashit( $link['url'] );
            ?>
                <a
                    class="eeta-nav__link<?php echo $is_current ? ' eeta-nav__link--current' : ''; ?>"
                    href="<?php echo esc_url( $link['url'] ); ?>"
                    <?php echo $is_current ? 'aria-current="page"' : ''; ?>
                >
                    <?php echo esc_html( $link['label'] ); ?>
                </a>
            <?php
                endforeach;
            endif;
            ?>
        </nav>

        <?php
        $cta_label = get_field( 'header_cta_label', 'option' );
        $cta_url   = get_field( 'header_cta_url', 'option' );
        if ( $cta_label && $cta_url ) :
        ?>
            <a class="eeta-btn eeta-btn--green eeta-nav__cta" href="<?php echo esc_url( $cta_url ); ?>">
                <?php echo esc_html( $cta_label ); ?>
            </a>
        <?php endif; ?>

        <button class="eeta-nav__hamburger" aria-label="Open menu" aria-expanded="false" aria-controls="eeta-mobile-menu">
            <span></span><span></span><span></span>
        </button>
    </div>

    <div class="eeta-nav__mobile-menu" id="eeta-mobile-menu" hidden>
        <?php
        if ( $nav_links ) :
            foreach ( $nav_links as $link ) :
                if ( empty( $link['label'] ) || empty( $link['url'] ) ) continue;
        ?>
            <a class="eeta-nav__mobile-link" href="<?php echo esc_url( $link['url'] ); ?>">
                <?php echo esc_html( $link['label'] ); ?>
            </a>
        <?php
            endforeach;
        endif;

        if ( $cta_label && $cta_url ) :
        ?>
            <a class="eeta-btn eeta-btn--green eeta-nav__mobile-cta" href="<?php echo esc_url( $cta_url ); ?>">
                <?php echo esc_html( $cta_label ); ?>
            </a>
        <?php endif; ?>
    </div>
</header>
