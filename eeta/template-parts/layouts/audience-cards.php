<?php
/**
 * Layout: Audience Cards
 * Figma: 2417:1253 — Section heading + 3-column card grid.
 */
$section_heading    = get_sub_field( 'section_heading' );
$section_subheading = get_sub_field( 'section_subheading' );
$cards              = get_sub_field( 'cards' );
?>

<section class="eeta-audience-cards">
    <div class="eeta-audience-cards__inner">
        <?php if ( $section_heading ) : ?>
            <h2 class="eeta-audience-cards__heading"><?php echo esc_html( $section_heading ); ?></h2>
        <?php endif; ?>

        <?php if ( $section_subheading ) : ?>
            <p class="eeta-audience-cards__subheading"><?php echo esc_html( $section_subheading ); ?></p>
        <?php endif; ?>

        <?php if ( $cards ) : ?>
            <div class="eeta-audience-cards__grid">
                <?php foreach ( $cards as $card ) : ?>
                    <div class="eeta-audience-cards__card">
                        <?php if ( ! empty( $card['icon'] ) ) : ?>
                            <div class="eeta-audience-cards__card-icon">
                                <img
                                    src="<?php echo esc_url( $card['icon']['url'] ); ?>"
                                    alt="<?php echo esc_attr( $card['icon']['alt'] ); ?>"
                                    width="64"
                                    height="64"
                                    loading="lazy"
                                />
                            </div>
                        <?php endif; ?>

                        <?php if ( ! empty( $card['title'] ) ) : ?>
                            <h3 class="eeta-audience-cards__card-title"><?php echo esc_html( $card['title'] ); ?></h3>
                        <?php endif; ?>

                        <?php if ( ! empty( $card['bullet_points'] ) ) : ?>
                            <ul class="eeta-audience-cards__bullets">
                                <?php foreach ( $card['bullet_points'] as $bp ) : ?>
                                    <?php if ( ! empty( $bp['point'] ) ) : ?>
                                        <li class="eeta-audience-cards__bullet">
                                            <span class="eeta-bullet-dot"></span>
                                            <?php echo esc_html( $bp['point'] ); ?>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
