<?php
/**
 * Wordie — front-page.php
 * Homepage template. Content assembled via ACF Flexible Content sections.
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="main" class="site-main" role="main">
	<?php
	if ( have_rows( 'sections' ) ) :
		while ( have_rows( 'sections' ) ) :
			the_row();
			$layout   = get_row_layout();
			$template = WORDIE_DIR . '/blocks/' . str_replace( '_', '-', $layout ) . '/template.php';
			if ( file_exists( $template ) ) {
				include $template;
			}
		endwhile;
	endif;
	?>
</main>
<?php get_footer(); ?>
