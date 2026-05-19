<?php
/**
 * Wordie — index.php
 *
 * Fallback template. WordPress requires this file to recognise the theme.
 * All actual page rendering is handled by front-page.php / page.php.
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		the_content();
	endwhile;
endif;

get_footer();
