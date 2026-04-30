<?php
/**
 * Component: Blog Post — Content
 * Figma node: 4520:33625 + 4520:33974
 *
 * bg: white, padding: 80px 60px
 * Featured image (2:1 ratio, 1096px wide, centred) then the_content() in 874px container.
 * Styled headings (H1–H4) and body text from theme typography.
 *
 * @package MeadanHomes
 */

defined( 'ABSPATH' ) || exit;

$has_thumb = has_post_thumbnail();
?>
<section class="post-content">
    <div class="post-content__inner">

        <?php if ( $has_thumb ) : ?>
        <!-- Featured image — displayed as 2:1 banner above content -->
        <figure class="post-content__featured-image">
            <?php the_post_thumbnail( 'full', [ 'class' => 'post-content__featured-img', 'loading' => 'eager', 'alt' => get_the_title() ] ); ?>
        </figure>
        <?php endif; ?>

        <!-- Article body — the_content() output -->
        <div class="post-content__body">
            <?php the_content(); ?>
        </div>

    </div><!-- .post-content__inner -->
</section><!-- .post-content -->
