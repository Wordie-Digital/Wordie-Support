<?php
/**
 * Wordie — front-page-v2.php
 * Homepage template — OPcache-safe version.
 * Loaded via fix-block-templates.php mu-plugin (template_include filter).
 * Checks for template-v2.php first, falls back to template.php.
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="main" class="site-main" role="main">
	<?php
	if ( have_rows( 'sections' ) ) :
		while ( have_rows( 'sections' ) ) :
			the_row();
			$layout     = get_row_layout();
			$block_slug = str_replace( '_', '-', $layout );

			// Prefer -v2 template (fresh OPcache entry) over original template
			$template_v2 = WORDIE_DIR . '/blocks/' . $block_slug . '/template-v2.php';
			$template    = WORDIE_DIR . '/blocks/' . $block_slug . '/template.php';

			if ( file_exists( $template_v2 ) ) {
				include $template_v2;
			} elseif ( file_exists( $template ) ) {
				include $template;
			}
		endwhile;
	endif;
	?>
</main>
<?php get_footer(); ?>
