<?php
/**
 * Wordie — front-page.php
 *
 * Homepage template. Content is assembled via ACF Gutenberg blocks.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="main" class="site-main" role="main">
	<?php
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();
			the_content();
		endwhile;
	endif;
	?>
</main>

<?php get_footer(); ?>
