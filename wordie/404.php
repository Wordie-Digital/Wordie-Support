<?php
/**
 * Wordie — 404.php
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="main" class="site-main" role="main">
	<section class="error-404 not-found" aria-label="Page not found">
		<div class="container">
			<h1 class="page-title"><?php esc_html_e( 'Page Not Found', 'wordie' ); ?></h1>
			<p><?php esc_html_e( 'The page you\'re looking for doesn\'t exist.', 'wordie' ); ?></p>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn--primary">
				<?php esc_html_e( 'Back to Home', 'wordie' ); ?>
			</a>
		</div>
	</section>
</main>

<?php get_footer(); ?>
