<?php
/**
 * Wordie — header.php
 *
 * Navigation uses ACF Options fields for logo, nav links and CTA.
 * Falls back to wp_nav_menu if the options page is not yet configured.
 */

defined( 'ABSPATH' ) || exit;

$nav_cta = get_field( 'nav_cta', 'option' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link sr-only" href="#main"><?php esc_html_e( 'Skip to content', 'wordie' ); ?></a>

<header class="site-header" role="banner" aria-label="<?php esc_attr_e( 'Site header', 'wordie' ); ?>">
	<div class="site-header__inner">

		<a class="site-header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) . ' — ' . esc_html__( 'Home', 'wordie' ) ); ?>">
			<?php
			$logo = get_field( 'site_logo', 'option' );
			if ( $logo ) :
				echo wp_get_attachment_image( $logo['ID'], 'full', false, [
					'class' => 'site-header__logo-img',
					'alt'   => esc_attr( get_bloginfo( 'name' ) ),
				] );
			else :
				?>
				<span class="site-header__logo-text">Wordie</span>
			<?php endif; ?>
		</a>

		<nav class="site-header__nav" aria-label="<?php esc_attr_e( 'Primary navigation', 'wordie' ); ?>">
			<?php
			wp_nav_menu( [
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'site-header__menu',
				'fallback_cb'    => false,
			] );
			?>

			<?php if ( $nav_cta && ! empty( $nav_cta['url'] ) ) : ?>
				<a
					href="<?php echo esc_url( $nav_cta['url'] ); ?>"
					class="btn btn--primary site-header__cta"
					<?php echo ( '_blank' === $nav_cta['target'] ) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>
				>
					<?php echo esc_html( $nav_cta['title'] ); ?>
				</a>
			<?php endif; ?>
		</nav>

		<button
			class="site-header__menu-toggle"
			aria-expanded="false"
			aria-controls="site-header__nav"
			aria-label="<?php esc_attr_e( 'Toggle navigation menu', 'wordie' ); ?>"
		>
			<span class="site-header__menu-toggle-bar" aria-hidden="true"></span>
			<span class="site-header__menu-toggle-bar" aria-hidden="true"></span>
			<span class="site-header__menu-toggle-bar" aria-hidden="true"></span>
		</button>

	</div>
</header>
