<?php
$image   = get_sub_field( 'image' );
$caption = get_sub_field( 'caption' );
$heading = get_sub_field( 'heading' );
$body    = get_sub_field( 'body' );
?>
<section class="eeta-text-image">
    <div class="eeta-text-image__image-col">
        <?php if ( $image ) : ?>
            <figure class="eeta-text-image__figure">
                <img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" width="<?php echo esc_attr( $image['width'] ); ?>" height="<?php echo esc_attr( $image['height'] ); ?>" loading="lazy" />
                <?php if ( $caption ) : ?>
                    <figcaption class="eeta-text-image__caption"><?php echo esc_html( $caption ); ?></figcaption>
                <?php endif; ?>
            </figure>
        <?php endif; ?>
    </div>
    <div class="eeta-text-image__text-col">
        <?php if ( $heading ) : ?>
            <h2 class="eeta-text-image__heading"><?php echo esc_html( $heading ); ?></h2>
        <?php endif; ?>
        <?php if ( $body ) : ?>
            <div class="eeta-text-image__body"><?php echo wp_kses_post( $body ); ?></div>
        <?php endif; ?>
    </div>
</section>
