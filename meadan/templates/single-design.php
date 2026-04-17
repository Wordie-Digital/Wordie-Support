<?php
/**
 * Meadan — templates/single-design.php
 * Single Design CPT template.
 */
get_header(); ?>

<main class="site-main site-main--single-design" id="main">
    <?php while ( have_posts() ) : the_post(); ?>

    <article class="design-single">
        <?php if ( has_post_thumbnail() ) : ?>
            <figure class="design-single__hero">
                <?php the_post_thumbnail( 'full', [ 'loading' => 'eager', 'class' => 'design-single__hero-image' ] ); ?>
            </figure>
        <?php endif; ?>

        <div class="design-single__content">
            <header class="design-single__header">
                <p class="design-single__label"><?php esc_html_e( 'HOME DESIGNS', 'meadan' ); ?></p>
                <h1 class="design-single__title"><?php the_title(); ?></h1>

                <?php
                $bedrooms  = get_post_meta( get_the_ID(), '_design_bedrooms', true );
                $bathrooms = get_post_meta( get_the_ID(), '_design_bathrooms', true );
                $sqm       = get_post_meta( get_the_ID(), '_design_sqm', true );
                if ( $bedrooms || $bathrooms || $sqm ) : ?>
                    <ul class="design-single__specs">
                        <?php if ( $bedrooms ) : ?>
                            <li class="design-single__spec"><?php echo esc_html( $bedrooms ); ?> <?php esc_html_e( 'Bed', 'meadan' ); ?></li>
                        <?php endif; ?>
                        <?php if ( $bathrooms ) : ?>
                            <li class="design-single__spec"><?php echo esc_html( $bathrooms ); ?> <?php esc_html_e( 'Bath', 'meadan' ); ?></li>
                        <?php endif; ?>
                        <?php if ( $sqm ) : ?>
                            <li class="design-single__spec"><?php echo esc_html( $sqm ); ?> m²</li>
                        <?php endif; ?>
                    </ul>
                <?php endif; ?>
            </header>

            <div class="design-single__body">
                <?php the_content(); ?>
            </div>
        </div>
    </article>

    <?php endwhile; ?>
</main>

<?php get_footer();
