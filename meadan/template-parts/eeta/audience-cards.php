<?php
$section_heading    = get_sub_field( 'section_heading' );
$section_subheading = get_sub_field( 'section_subheading' );
$cards              = get_sub_field( 'cards' );
?>
<section class="eeta-audience">
    <div class="eeta-audience__header">
        <?php if ( $section_heading ) : ?>
            <h2 class="eeta-audience__heading"><?php echo esc_html( $section_heading ); ?></h2>
        <?php endif; ?>
        <?php if ( $section_subheading ) : ?>
            <p class="eeta-audience__subheading"><?php echo esc_html( $section_subheading ); ?></p>
        <?php endif; ?>
    </div>
    <?php if ( $cards ) : ?>
        <div class="eeta-audience__grid">
            <?php foreach ( $cards as $card ) : ?>
                <div class="eeta-audience__card">
                    <?php if ( ! empty( $card['icon'] ) && ! empty( $card['icon']['url'] ) ) : ?>
                        <img
                            class="eeta-audience__card-image"
                            src="<?php echo esc_url( $card['icon']['url'] ); ?>"
                            alt="<?php echo esc_attr( $card['icon']['alt'] ); ?>"
                            loading="lazy"
                        />
                    <?php endif; ?>
                    <div class="eeta-audience__card-body">
                        <?php if ( ! empty( $card['title'] ) ) : ?>
                            <h3 class="eeta-audience__card-title"><?php echo esc_html( $card['title'] ); ?></h3>
                        <?php endif; ?>
                        <?php if ( ! empty( $card['bullet_points'] ) ) : ?>
                            <ul class="eeta-audience__bullets">
                                <?php foreach ( $card['bullet_points'] as $bp ) : ?>
                                    <?php if ( ! empty( $bp['point'] ) ) : ?>
                                        <li class="eeta-audience__bullet">
                                            <span class="eeta-audience__bullet-dot" aria-hidden="true">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M2 6L5 9L10 3" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </span>
                                            <?php echo esc_html( $bp['point'] ); ?>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
