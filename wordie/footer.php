<?php
/**
 * Wordie — footer.php
 *
 * Renders the site footer: dark-teal nav + contact columns, then the copyright bar.
 * All content pulled from ACF Options (wordie-footer-settings) and wp_nav_menu().
 * Visually connects flush to the bottom-cta block above it (both dark teal).
 * Figma reference: 2942:14970 (Desktop Container + Desktop Footer Container)
 */

defined( 'ABSPATH' ) || exit;

$phone     = get_field( 'footer_phone',     'option' );
$email     = get_field( 'footer_email',     'option' );
$address   = get_field( 'footer_address',   'option' );
$linkedin  = get_field( 'social_linkedin',  'option' );
$instagram = get_field( 'social_instagram', 'option' );
$copyright = get_field( 'footer_copyright', 'option' );
?>

	<footer class="site-footer" aria-label="<?php esc_attr_e( 'Site footer', 'wordie' ); ?>">

		<?php /* ── Main footer row: nav + contact ── */ ?>
		<div class="site-footer__main">
			<div class="site-footer__inner">

				<?php /* Nav menu — "footer" location registered in theme-setup.php */ ?>
				<?php if ( has_nav_menu( 'footer' ) ) : ?>
					<nav class="site-footer__nav" aria-label="<?php esc_attr_e( 'Footer navigation', 'wordie' ); ?>">
						<?php wp_nav_menu( [
							'theme_location' => 'footer',
							'container'      => false,
							'menu_class'     => 'site-footer__nav-list',
							'depth'          => 2,
							'fallback_cb'    => false,
						] ); ?>
					</nav>
				<?php endif; ?>

				<?php /* Contact info column */ ?>
				<?php if ( $phone || $email || $address ) : ?>
					<div class="site-footer__contact">
						<p class="site-footer__contact-heading"><?php esc_html_e( 'Contact Us', 'wordie' ); ?></p>
						<ul class="site-footer__contact-list" role="list">
							<?php if ( $address ) : ?>
								<li class="site-footer__contact-item">
									<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
										<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
									</svg>
									<span><?php echo esc_html( $address ); ?></span>
								</li>
							<?php endif; ?>
							<?php if ( $phone ) : ?>
								<li class="site-footer__contact-item">
									<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
										<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.86 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.77 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l.91-.91a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 17z"/>
									</svg>
									<a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
								</li>
							<?php endif; ?>
							<?php if ( $email ) : ?>
								<li class="site-footer__contact-item">
									<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
										<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
									</svg>
									<a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				<?php endif; ?>

			</div>
		</div>

		<?php /* ── Bottom bar: copyright + legal links + social ── */ ?>
		<div class="site-footer__bar">
			<div class="site-footer__bar-inner">

				<p class="site-footer__copyright">
					<?php echo esc_html( $copyright ?: sprintf( '© %d Wordie. All rights reserved.', gmdate( 'Y' ) ) ); ?>
				</p>

				<?php if ( has_nav_menu( 'footer-legal' ) ) : ?>
					<nav class="site-footer__legal" aria-label="<?php esc_attr_e( 'Legal links', 'wordie' ); ?>">
						<?php wp_nav_menu( [
							'theme_location' => 'footer-legal',
							'container'      => false,
							'menu_class'     => 'site-footer__legal-list',
							'depth'          => 1,
							'fallback_cb'    => false,
						] ); ?>
					</nav>
				<?php endif; ?>

				<?php if ( $linkedin || $instagram ) : ?>
					<div class="site-footer__social" aria-label="<?php esc_attr_e( 'Social media links', 'wordie' ); ?>">
						<?php if ( $linkedin ) : ?>
							<a href="<?php echo esc_url( $linkedin ); ?>" class="site-footer__social-link" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'LinkedIn', 'wordie' ); ?>">
								<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
									<path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/>
								</svg>
							</a>
						<?php endif; ?>
						<?php if ( $instagram ) : ?>
							<a href="<?php echo esc_url( $instagram ); ?>" class="site-footer__social-link" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Instagram', 'wordie' ); ?>">
								<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
								</svg>
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>

			</div>
		</div>

	</footer>

<?php wp_footer(); ?>
</body>
</html>
