<?php
/**
 * Meadan — templates/archive-design.php
 * Design CPT archive template.
 */
get_header(); ?>

<main class="site-main site-main--archive-design" id="main">
    <header class="archive-header">
        <p class="archive-header__label"><?php esc_html_e( 'HOME DESIGNS', 'meadan' ); ?></p>
        <h1 class="archive-header__title"><?php esc_html_e( 'Homes Designed for the Way You Live', 'meadan' ); ?></h1>
    </header>

    <div class="design-grid">
        <?php if ( have_posts() ) :
            while ( have_posts() ) : the_post();
                $bedrooms  = get_post_meta( get_the_ID(), '_design_bedrooms', true );
                $bathrooms = get_post_meta( get_the_ID(), '_design_bathrooms', true );
                $sqm       = get_post_meta( get_the_ID(), '_design_sqm', true );
                ?>
                <article class="design-card">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <a class="design-card__image-link" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
                            <?php the_post_thumbnail( 'medium_large', [ 'loading' => 'lazy', 'class' => 'design-card__image' ] ); ?>
                        </a>
                    <?php endif; ?>
                    <div class="design-card__body">
                        <h2 class="design-card__title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                        <?php if ( $bedrooms || $bathrooms || $sqm ) : ?>
                            <ul class="design-card__specs">
                                <?php if ( $bedrooms ) : ?>
                                    <li><?php echo esc_html( $bedrooms ); ?> Bed</li>
                                <?php endif; ?>
                                <?php if ( $bathrooms ) : ?>
                                    <li><?php echo esc_html( $bathrooms ); ?> Bath</li>
                                <?php endif; ?>
                                <?php if ( $sqm ) : ?>
                                    <li><?php echo esc_html( $sqm ); ?> m²</li>
                                <?php endif; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endwhile;

            the_posts_pagination();
        else : ?>
            <p><?php esc_html_e( 'No designs found.', 'meadan' ); ?></p>
        <?php endif; ?>
    </div>
</main>

<?php get_footer();
