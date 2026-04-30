<?php
/**
 * Component: Blog Post — Hero
 * Figma node: 4520:33601
 *
 * bg: #c8bbab (Stone), height: 400px
 * Post thumbnail as full-bleed background image, gradient overlay at bottom.
 * Centred: breadcrumbs → H1 title → excerpt → metadata row (category · date · reading time)
 *
 * @package MeadanHomes
 */

defined( 'ABSPATH' ) || exit;

// Thumbnail
$thumb_url = has_post_thumbnail() ? get_the_post_thumbnail_url( get_the_ID(), 'full' ) : '';

// Breadcrumbs
$blog_page_id  = (int) get_option( 'page_for_posts' );
$blog_page_url = $blog_page_id ? get_permalink( $blog_page_id ) : home_url( '/blog/' );
$blog_page_name = $blog_page_id ? get_the_title( $blog_page_id ) : 'Meadan Musings';

$cats  = get_the_category();
$cat   = ! empty( $cats ) ? $cats[0] : null;

// Metadata
$date       = get_the_date( 'M j, Y' );
$word_count = str_word_count( strip_tags( get_the_content() ) );
$read_mins  = max( 1, (int) ceil( $word_count / 250 ) );
$read_time  = $read_mins . '-min read';

// Excerpt — use manual excerpt if set, otherwise auto
$excerpt = has_excerpt() ? get_the_excerpt() : wp_trim_words( get_the_content(), 25 );
?>
<section
    class="post-hero"
    <?php if ( $thumb_url ) : ?>
    style="background-image: url('<?php echo esc_url( $thumb_url ); ?>');"
    <?php endif; ?>
>
    <div class="post-hero__overlay"></div>

    <div class="post-hero__content">

        <!-- Breadcrumbs -->
        <nav class="post-hero__breadcrumb" aria-label="Breadcrumb">
            <a class="post-hero__breadcrumb-link" href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
            <span class="post-hero__breadcrumb-sep">/</span>
            <a class="post-hero__breadcrumb-link" href="<?php echo esc_url( $blog_page_url ); ?>"><?php echo esc_html( $blog_page_name ); ?></a>
            <?php if ( $cat ) : ?>
            <span class="post-hero__breadcrumb-sep">/</span>
            <a class="post-hero__breadcrumb-link" href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>"><?php echo esc_html( $cat->name ); ?></a>
            <?php endif; ?>
        </nav>

        <!-- Title -->
        <h1 class="post-hero__title"><?php the_title(); ?></h1>

        <!-- Excerpt -->
        <?php if ( $excerpt ) : ?>
        <p class="post-hero__excerpt"><?php echo esc_html( $excerpt ); ?></p>
        <?php endif; ?>

        <!-- Metadata: category · date · reading time -->
        <div class="post-hero__meta">
            <?php if ( $cat ) : ?>
            <span class="post-hero__meta-cat">
                <a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>"><?php echo esc_html( $cat->name ); ?></a>
            </span>
            <?php endif; ?>
            <span class="post-hero__meta-date"><?php echo esc_html( $date ); ?></span>
            <span class="post-hero__meta-read"><?php echo esc_html( $read_time ); ?></span>
        </div>

    </div><!-- .post-hero__content -->
</section><!-- .post-hero -->
