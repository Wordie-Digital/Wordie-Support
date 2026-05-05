<?php
$section_heading    = get_sub_field( 'section_heading' );
$section_subheading = get_sub_field( 'section_subheading' );
$boxes              = get_sub_field( 'boxes' );
?>
<section class="eeta-icon-boxes">
    <div class="eeta-icon-boxes__inner">
        <?php if ( $section_heading ) : ?>
            <h2 class="eeta-icon-boxes__heading"><?php echo esc_html( $section_heading ); ?></h2>
        <?php endif; ?>
        <?php if ( $section_subheading ) : ?>
            <p class="eeta-icon-boxes__subheading"><?php echo esc_html( $section_subheading ); ?></p>
        <?php endif; ?>
        <?php if ( $boxes ) : ?>
            <div class="eeta-icon-boxes__grid">
                <?php foreach ( $boxes as $box ) : ?>
                    <div class="eeta-icon-boxes__box">
                        <?php if ( ! empty( $box['icon'] ) ) : ?>
                            <div class="eeta-icon-boxes__icon"><img src="<?php echo esc_url( $box['icon']['url'] ); ?>" alt="<?php echo esc_attr( $box['icon']['alt'] ); ?>" width="48" height="48" loading="lazy" /></div>
                        <?php endif; ?>
                        <?php if ( ! empty( $box['title'] ) ) : ?>
                            <h3 class="eeta-icon-boxes__box-title"><?php echo esc_html( $box['title'] ); ?></h3>
                        <?php endif; ?>
                        <?php if ( ! empty( $box['description'] ) ) : ?>
                            <p class="eeta-icon-boxes__box-desc"><?php echo esc_html( $box['description'] ); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
