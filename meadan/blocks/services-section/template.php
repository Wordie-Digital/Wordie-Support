<?php
/**
 * Block: Services Section
 * Slug: acf/services-section
 */

defined( 'ABSPATH' ) || exit;

$section_label = get_field( 'section_label' );
$heading       = get_field( 'heading' );
$service_cards = get_field( 'service_cards' );

$block_id = ! empty( $block['anchor'] ) ? ' id="' . esc_attr( $block['anchor'] ) . '"' : '';
?>
<section class="services-section"<?php echo $block_id; ?>>

    <?php if ( $section_label || $heading ) : ?>
        <div class="services-section__header">
            <?php if ( $section_label ) : ?>
                <p class="services-section__label"><?php echo esc_html( $section_label ); ?></p>
            <?php endif; ?>
            <?php if ( $heading ) : ?>
                <h2 class="services-section__heading"><?php echo esc_html( $heading ); ?></h2>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ( $service_cards ) : ?>
        <div class="services-section__cards">
            <?php foreach ( $service_cards as $card ) : ?>
                <article class="service-card">

                    <?php if ( ! empty( $card['image'] ) ) : ?>
                        <figure class="service-card__image-wrap">
                            <img
                                class="service-card__image"
                                src="<?php echo esc_url( $card['image']['url'] ); ?>"
                                alt="<?php echo esc_attr( $card['image']['alt'] ); ?>"
                                loading="lazy"
                                width="<?php echo esc_attr( $card['image']['width'] ); ?>"
                                height="<?php echo esc_attr( $card['image']['height'] ); ?>"
                            >
                        </figure>
                    <?php endif; ?>

                    <div class="service-card__body">
                        <div class="service-card__text">
                            <?php if ( ! empty( $card['title'] ) ) : ?>
                                <h3 class="service-card__title"><?php echo esc_html( $card['title'] ); ?></h3>
                            <?php endif; ?>

                            <?php if ( ! empty( $card['description'] ) ) : ?>
                                <p class="service-card__description"><?php echo esc_html( $card['description'] ); ?></p>
                            <?php endif; ?>
                        </div>

                        <?php
                        // Build CTA buttons — support up to 2 (link + link_2 fields)
                        $ctas = [];
                        if ( ! empty( $card['link_label'] ) && ! empty( $card['link_url'] ) ) {
                            $ctas[] = [ 'label' => $card['link_label'], 'url' => $card['link_url'] ];
                        }
                        if ( ! empty( $card['link_2_label'] ) && ! empty( $card['link_2_url'] ) ) {
                            $ctas[] = [ 'label' => $card['link_2_label'], 'url' => $card['link_2_url'] ];
                        }
                        ?>
                        <?php if ( ! empty( $ctas ) ) : ?>
                            <div class="service-card__ctas">
                                <?php foreach ( $ctas as $cta ) : ?>
                                    <a class="btn btn--primary" href="<?php echo esc_url( $cta['url'] ); ?>">
                                        <?php echo esc_html( $cta['label'] ); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php elseif ( ! empty( $card['link_label'] ) ) : ?>
                            <!-- label only, no url — render as text -->
                            <span class="service-card__link"><?php echo esc_html( $card['link_label'] ); ?></span>
                        <?php endif; ?>
                    </div>

                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</section>
