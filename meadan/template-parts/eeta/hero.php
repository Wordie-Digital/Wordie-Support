<?php
$heading    = get_sub_field( 'heading' );
$subheading = get_sub_field( 'subheading' );
$body       = get_sub_field( 'body' );
$cta_label  = get_sub_field( 'cta_label' );
$cta_url    = get_sub_field( 'cta_url' );
$hero_image = get_sub_field( 'hero_image' );
?>
<section class="eeta-hero">
    <div class="eeta-hero__text-panel">
        <div class="eeta-hero__text-inner">
            <?php if ( $heading ) : ?>
                <h1 class="eeta-hero__heading"><?php echo esc_html( $heading ); ?></h1>
            <?php endif; ?>
            <?php if ( $subheading ) : ?>
                <p class="eeta-hero__subheading"><?php echo nl2br( esc_html( $subheading ) ); ?></p>
            <?php endif; ?>
            <?php if ( $body || $cta_label ) : ?>
                <div class="eeta-hero__lower">
                    <?php if ( $body ) : ?>
                        <p class="eeta-hero__cta-label"><?php echo esc_html( $body ); ?></p>
                    <?php endif; ?>
                    <?php if ( $cta_label && $cta_url ) : ?>
                        <a class="eeta-hero__cta" href="<?php echo esc_url( $cta_url ); ?>">
                            <?php echo esc_html( $cta_label ); ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="eeta-hero__image-panel">
        <?php if ( $hero_image ) : ?>
            <img
                src="<?php echo esc_url( $hero_image['url'] ); ?>"
                alt="<?php echo esc_attr( $hero_image['alt'] ); ?>"
                width="<?php echo esc_attr( $hero_image['width'] ); ?>"
                height="<?php echo esc_attr( $hero_image['height'] ); ?>"
                loading="eager"
            />
        <?php endif; ?>
    </div>
</section>
