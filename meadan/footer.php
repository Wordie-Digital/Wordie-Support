<footer class="site-footer" role="contentinfo">
    <div class="site-footer__inner">

        <?php
        $footer_logo    = function_exists( 'get_field' ) ? get_field( 'logo_image', 'option' )     : false;
        $copyright_text = function_exists( 'get_field' ) ? get_field( 'copyright_text', 'option' ) : '';
        $social_links   = function_exists( 'get_field' ) ? get_field( 'social_links', 'option' )   : [];
        $license_nsw    = function_exists( 'get_field' ) ? get_field( 'license_nsw', 'option' )    : '264398C';
        $license_qld    = function_exists( 'get_field' ) ? get_field( 'license_qld', 'option' )    : 'QBCC 15233875';
        ?>

        <!-- Brand column: logo + social -->
        <div class="site-footer__brand">
            <a
                class="site-footer__logo-link"
                href="<?php echo esc_url( home_url( '/' ) ); ?>"
                aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?> — <?php esc_attr_e( 'Home', 'meadan' ); ?>"
            >
                <?php if ( $footer_logo && is_array( $footer_logo ) ) : ?>
                    <img
                        class="site-footer__logo-img"
                        src="<?php echo esc_url( $footer_logo['url'] ); ?>"
                        alt="<?php echo esc_attr( $footer_logo['alt'] ?: get_bloginfo( 'name' ) ); ?>"
                        width="<?php echo esc_attr( $footer_logo['width'] ); ?>"
                        height="<?php echo esc_attr( $footer_logo['height'] ); ?>"
                        loading="lazy"
                    >
                <?php elseif ( $footer_logo ) :
                    echo wp_get_attachment_image(
                        (int) $footer_logo,
                        'full',
                        false,
                        [ 'class' => 'site-footer__logo-img', 'loading' => 'lazy' ]
                    );
                else : ?>
                    <span class="site-footer__logo-text"><?php bloginfo( 'name' ); ?></span>
                <?php endif; ?>
            </a>

            <?php if ( ! empty( $social_links ) ) : ?>
                <div class="site-footer__social" aria-label="<?php esc_attr_e( 'Social media links', 'meadan' ); ?>">
                    <?php foreach ( $social_links as $item ) :
                        $url      = ! empty( $item['url'] )      ? $item['url']      : '';
                        $platform = ! empty( $item['platform'] ) ? $item['platform'] : '';
                        if ( ! $url ) continue;
                    ?>
                        <a
                            class="site-footer__social-link"
                            href="<?php echo esc_url( $url ); ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="<?php echo esc_attr( $platform ); ?>"
                        >
                            <?php echo esc_html( $platform ); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div><!-- .site-footer__brand -->

        <!-- Nav columns -->
        <nav class="site-footer__nav" aria-label="<?php esc_attr_e( 'Footer', 'meadan' ); ?>">
            <?php
            // Try to output a structured 4-column nav from the footer menu.
            // If the theme has nav group support, it will render .site-footer__nav-columns.
            // Fall back to a simple flat list.
            $has_footer_menu = has_nav_menu( 'footer' );
            if ( $has_footer_menu ) :
                // Retrieve menu items grouped by top-level items
                $menu_obj    = wp_get_nav_menu_object( get_nav_menu_locations()['footer'] ?? 0 );
                $menu_items  = $menu_obj ? wp_get_nav_menu_items( $menu_obj->term_id ) : [];

                // Build a grouped structure: top-level => children
                $groups = [];
                if ( $menu_items ) {
                    foreach ( $menu_items as $item ) {
                        if ( $item->menu_item_parent == 0 ) {
                            $groups[ $item->ID ] = [ 'item' => $item, 'children' => [] ];
                        }
                    }
                    foreach ( $menu_items as $item ) {
                        if ( $item->menu_item_parent != 0 && isset( $groups[ $item->menu_item_parent ] ) ) {
                            $groups[ $item->menu_item_parent ]['children'][] = $item;
                        }
                    }
                }

                if ( ! empty( $groups ) ) : ?>
                    <div class="site-footer__nav-columns">
                        <?php foreach ( $groups as $group ) : ?>
                            <div class="site-footer__nav-group">
                                <p class="site-footer__nav-group-title">
                                    <?php if ( $group['item']->url && $group['item']->url !== '#' ) : ?>
                                        <a href="<?php echo esc_url( $group['item']->url ); ?>">
                                            <?php echo esc_html( $group['item']->title ); ?>
                                        </a>
                                    <?php else : ?>
                                        <?php echo esc_html( $group['item']->title ); ?>
                                    <?php endif; ?>
                                </p>
                                <?php if ( ! empty( $group['children'] ) ) : ?>
                                    <ul class="site-footer__nav-group-list">
                                        <?php foreach ( $group['children'] as $child ) : ?>
                                            <li>
                                                <a href="<?php echo esc_url( $child->url ); ?>">
                                                    <?php echo esc_html( $child->title ); ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else :
                    // Flat fallback
                    wp_nav_menu( [
                        'theme_location' => 'footer',
                        'menu_class'     => 'site-footer__nav-list',
                        'container'      => false,
                        'fallback_cb'    => false,
                        'depth'          => 2,
                    ] );
                endif;
            else :
                // No footer menu registered — output nothing or a placeholder
            endif;
            ?>
        </nav><!-- .site-footer__nav -->

        <!-- Builders licence + legal -->
        <div class="site-footer__legal">
            <div>
                <p class="site-footer__license-heading"><?php esc_html_e( 'Builders License', 'meadan' ); ?></p>
                <div class="site-footer__license-grid">
                    <span class="site-footer__license-state"><?php esc_html_e( 'NSW', 'meadan' ); ?></span>
                    <span class="site-footer__license-number"><?php echo esc_html( $license_nsw ); ?></span>
                    <span class="site-footer__license-state"><?php esc_html_e( 'QLD', 'meadan' ); ?></span>
                    <span class="site-footer__license-number"><?php echo esc_html( $license_qld ); ?></span>
                </div>
            </div>
        </div><!-- .site-footer__legal -->

        <!-- Bottom bar: copyright + privacy/disclaimers -->
        <div class="site-footer__bottom">
            <p class="site-footer__copyright-text">
                <?php if ( $copyright_text ) :
                    echo esc_html( $copyright_text );
                else : ?>
                    &copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>.
                    <?php esc_html_e( 'Website by', 'meadan' ); ?>
                    <a href="https://wordie.com.au" target="_blank" rel="noopener">Wordie</a>
                <?php endif; ?>
            </p>
            <ul class="site-footer__bottom-links">
                <li><a href="/privacy-policy"><?php esc_html_e( 'Privacy Policy', 'meadan' ); ?></a></li>
                <li><a href="/disclaimers"><?php esc_html_e( 'Disclaimers', 'meadan' ); ?></a></li>
            </ul>
        </div>

    </div><!-- .site-footer__inner -->
</footer><!-- .site-footer -->

<?php wp_footer(); ?>
</body>
</html>
