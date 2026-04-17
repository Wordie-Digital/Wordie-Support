<?php
/**
 * Block: Feature Cards
 * Template: blocks/feature-cards/template.php
 *
 * Fields:
 *  - cards (repeater)
 *    - title       (text)    Required
 *    - description (textarea)
 *    - image       (image array)
 *    - link        (url)
 */

defined( 'ABSPATH' ) || exit;

// Retrieve fields
$cards = get_field( 'cards' );

// Block attributes
$block_id    = ! empty( $block['anchor'] ) ? $block['anchor'] : 'feature-cards-' . $block['id'];
$extra_class = ! empty( $block['className'] ) ? ' ' . $block['className'] : '';

// Determine if we are inside the block editor (preview mode)
$is_preview = ! empty( $block['data']['_is_preview'] );

// If no cards and we're in the editor, render placeholder cards for visual reference
if ( empty( $cards ) ) :
    ?>
    <section
        id="<?php echo esc_attr( $block_id ); ?>"
        class="feature-cards feature-cards--placeholder<?php echo esc_attr( $extra_class ); ?>"
        data-block-id="<?php echo esc_attr( $block['id'] ); ?>"
    >
        <div class="feature-cards__grid">
            <?php for ( $i = 1; $i <= 3; $i++ ) : ?>
                <article class="feature-card feature-card--placeholder">
                    <div class="feature-card__image-wrap feature-card__image-wrap--placeholder">
                        <span class="feature-card__placeholder-icon" aria-hidden="true"></span>
                    </div>
                    <div class="feature-card__body">
                        <h3 class="feature-card__title feature-card__title--placeholder">
                            <?php printf( esc_html__( 'Card Title %d', 'meadan' ), $i ); ?>
                        </h3>
                        <p class="feature-card__description feature-card__description--placeholder">
                            <?php esc_html_e( 'Add cards via the block settings panel. Each card supports a title, description, image and optional link.', 'meadan' ); ?>
                        </p>
                    </div>
                </article>
            <?php endfor; ?>
        </div>
        <p class="feature-cards__editor-notice">
            <?php esc_html_e( 'No cards added yet. Use the field panel to add up to 6 cards.', 'meadan' ); ?>
        </p>
    </section>
    <?php
    return;
endif;
?>

<section
    id="<?php echo esc_attr( $block_id ); ?>"
    class="feature-cards<?php echo esc_attr( $extra_class ); ?>"
    data-block-id="<?php echo esc_attr( $block['id'] ); ?>"
>
    <div class="feature-cards__grid">

        <?php foreach ( $cards as $card ) :
            $title       = ! empty( $card['title'] )       ? $card['title']       : '';
            $description = ! empty( $card['description'] ) ? $card['description'] : '';
            $image       = ! empty( $card['image'] )       ? $card['image']       : null;
            $link        = ! empty( $card['link'] )        ? $card['link']        : '';
            ?>

            <article class="feature-card">

                <?php if ( $image && ! empty( $image['url'] ) ) : ?>
                    <div class="feature-card__image-wrap">
                        <?php if ( $link ) : ?>
                            <a class="feature-card__image-link" href="<?php echo esc_url( $link ); ?>" tabindex="-1" aria-hidden="true">
                        <?php endif; ?>

                            <img
                                class="feature-card__image"
                                src="<?php echo esc_url( $image['url'] ); ?>"
                                alt="<?php echo esc_attr( $image['alt'] ?: $title ); ?>"
                                width="<?php echo esc_attr( $image['width'] ); ?>"
                                height="<?php echo esc_attr( $image['height'] ); ?>"
                                loading="lazy"
                            >

                        <?php if ( $link ) : ?>
                            </a>
                        <?php endif; ?>
                    </div><!-- .feature-card__image-wrap -->
                <?php endif; ?>

                <div class="feature-card__body">

                    <?php if ( $title ) : ?>
                        <h3 class="feature-card__title">
                            <?php if ( $link ) : ?>
                                <a class="feature-card__title-link" href="<?php echo esc_url( $link ); ?>">
                                    <?php echo esc_html( $title ); ?>
                                </a>
                            <?php else : ?>
                                <?php echo esc_html( $title ); ?>
                            <?php endif; ?>
                        </h3>
                    <?php endif; ?>

                    <?php if ( $description ) : ?>
                        <p class="feature-card__description">
                            <?php echo esc_html( $description ); ?>
                        </p>
                    <?php endif; ?>

                    <?php if ( $link && $title ) : ?>
                        <a
                            class="feature-card__link"
                            href="<?php echo esc_url( $link ); ?>"
                            aria-label="<?php echo esc_attr( $title ); ?>"
                        >
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                    <?php endif; ?>

                </div><!-- .feature-card__body -->

            </article><!-- .feature-card -->

        <?php endforeach; ?>

    </div><!-- .feature-cards__grid -->
</section><!-- .feature-cards -->
