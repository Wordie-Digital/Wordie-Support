<?php
$section_heading    = get_sub_field( 'section_heading' );
$section_subheading = get_sub_field( 'section_subheading' );
$boxes              = get_sub_field( 'boxes' );
?>
<section class="eeta-icon-boxes">
    <?php if ( $section_heading ) : ?>
        <h2 class="eeta-icon-boxes__heading"><?php echo nl2br( esc_html( $section_heading ) ); ?></h2>
    <?php endif; ?>
    <?php if ( $boxes ) : ?>
        <div class="eeta-icon-boxes__grid">
            <?php foreach ( $boxes as $box ) : ?>
                <div class="eeta-icon-box">
                    <?php if ( ! empty( $box['icon'] ) && ! empty( $box['icon']['url'] ) ) : ?>
                        <div class="eeta-icon-box__icon">
                            <img
                                src="<?php echo esc_url( $box['icon']['url'] ); ?>"
                                alt=""
                                width="24"
                                height="24"
                                loading="lazy"
                                aria-hidden="true"
                            />
                        </div>
                    <?php endif; ?>
                    <?php if ( ! empty( $box['description'] ) ) : ?>
                        <p class="eeta-icon-box__text"><?php echo esc_html( $box['description'] ); ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
