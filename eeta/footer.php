<?php
$footer_logo              = get_field( 'footer_logo', 'option' );
$footer_email             = get_field( 'footer_email', 'option' );
$footer_phone             = get_field( 'footer_phone', 'option' );
$footer_address           = get_field( 'footer_address', 'option' );
$acknowledgement_image    = get_field( 'footer_acknowledgement_image', 'option' );
$acknowledgement_text     = get_field( 'footer_acknowledgement_text', 'option' );
$copyright                = get_field( 'footer_copyright', 'option' );
$footer_links             = get_field( 'footer_links', 'option' );
?>

<footer class="eeta-footer" role="contentinfo">

    <!-- Company Info -->
    <div class="eeta-footer__company">
        <div class="eeta-footer__company-inner">

            <?php if ( $footer_logo ) : ?>
                <a class="eeta-footer__logo-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php bloginfo( 'name' ); ?> — Home">
                    <img
                        class="eeta-footer__logo"
                        src="<?php echo esc_url( $footer_logo['url'] ); ?>"
                        alt="<?php echo esc_attr( $footer_logo['alt'] ) ?: esc_attr( get_bloginfo( 'name' ) ); ?>"
                        width="<?php echo esc_attr( $footer_logo['width'] ); ?>"
                        height="<?php echo esc_attr( $footer_logo['height'] ); ?>"
                        loading="lazy"
                    />
                </a>
            <?php endif; ?>

            <div class="eeta-footer__contact">
                <?php if ( $footer_email ) : ?>
                    <a class="eeta-footer__contact-item" href="mailto:<?php echo esc_attr( $footer_email ); ?>">
                        <svg class="eeta-footer__icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <path d="M2 3h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z" stroke="currentColor" stroke-width="1.5"/>
                            <path d="M1 4l7 5 7-5" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                        <?php echo esc_html( $footer_email ); ?>
                    </a>
                <?php endif; ?>

                <?php if ( $footer_address ) : ?>
                    <span class="eeta-footer__contact-item">
                        <svg class="eeta-footer__icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <path d="M8 1a5 5 0 0 1 5 5c0 3.5-5 9-5 9S3 9.5 3 6a5 5 0 0 1 5-5z" stroke="currentColor" stroke-width="1.5"/>
                            <circle cx="8" cy="6" r="1.5" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                        <?php echo nl2br( esc_html( $footer_address ) ); ?>
                    </span>
                <?php endif; ?>

                <?php if ( $footer_phone ) : ?>
                    <a class="eeta-footer__contact-item" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $footer_phone ) ); ?>">
                        <svg class="eeta-footer__icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <path d="M3 2h3l1.5 3.5-1.5 1a8 8 0 0 0 3.5 3.5l1-1.5L14 10v3a1 1 0 0 1-1 1A12 12 0 0 1 2 3a1 1 0 0 1 1-1z" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                        <?php echo esc_html( $footer_phone ); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Acknowledgement Banner -->
    <?php if ( $acknowledgement_image || $acknowledgement_text ) : ?>
        <div class="eeta-footer__acknowledgement">
            <div class="eeta-footer__acknowledgement-inner">
                <?php if ( $acknowledgement_image ) : ?>
                    <img
                        class="eeta-footer__acknowledgement-img"
                        src="<?php echo esc_url( $acknowledgement_image['url'] ); ?>"
                        alt="<?php echo esc_attr( $acknowledgement_image['alt'] ); ?>"
                        width="101"
                        height="73"
                        loading="lazy"
                    />
                <?php endif; ?>

                <?php if ( $acknowledgement_text ) : ?>
                    <p class="eeta-footer__acknowledgement-text"><?php echo esc_html( $acknowledgement_text ); ?></p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Copyright Bar -->
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
                        <a class="eeta-footer__link" href="<?php echo esc_url( $link['url'] ); ?>">
                            <?php echo esc_html( $link['label'] ); ?>
                        </a>
                    <?php endforeach; ?>
                </nav>
            <?php endif; ?>
        </div>
    </div>

</footer>

<?php wp_footer(); ?>
</body>
</html>
