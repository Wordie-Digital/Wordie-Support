<?php
/**
 * Wordie — footer.php
 *
 * Global footer component. All content served from ACF Options Page
 * (Theme Settings → Footer) — zero hardcoded content.
 *
 * Structure (Figma 2942:14797):
 *  ┌─────────────────────────────────────────────────────────┐
 *  │  Industries (3 sub-cols)  │ Locations │ Quick Links │ Contact Us │
 *  ├─────────────────────────────────────────────────────────┤
 *  │  © Copyright text    Legal links (gap 40px)    Social   │
 *  └─────────────────────────────────────────────────────────┘
 *
 * Figma node: 2942:14797
 */

defined( 'ABSPATH' ) || exit;

// ── ACF Options fields ────────────────────────────────────────────────────────
$industry_groups = get_field( 'footer_industry_groups', 'option' ) ?: [];
$locations       = get_field( 'footer_locations',       'option' ) ?: [];
$quick_links     = get_field( 'footer_quick_links',     'option' ) ?: [];
$address         = get_field( 'footer_address',         'option' );
$phone           = get_field( 'footer_phone',           'option' );
$email           = get_field( 'footer_email',           'option' );
$copyright       = get_field( 'footer_copyright',       'option' );
$legal_links     = get_field( 'footer_legal_links',     'option' ) ?: [];
$linkedin        = get_field( 'social_linkedin',        'option' );
$facebook        = get_field( 'social_facebook',        'option' );
?>

	<footer class="site-footer" aria-label="<?php esc_attr_e( 'Site footer', 'wordie' ); ?>">

		<?php /* ── Main columns row ── */ ?>
		<div class="site-footer__main">

			<?php /* ── Industries block (heading + 3 sub-columns) ── */ ?>
			<?php if ( $industry_groups ) : ?>
				<div class="site-footer__industries">
					<p class="site-footer__col-heading"><?php esc_html_e( 'Industries', 'wordie' ); ?></p>
					<div class="site-footer__industry-cols">
						<?php foreach ( $industry_groups as $group ) :
							$sub_heading = $group['sub_heading'] ?? '';
							$links       = $group['links'] ?? [];
						?>
							<div class="site-footer__industry-col">
								<?php if ( $sub_heading ) : ?>
									<p class="site-footer__sub-heading"><?php echo esc_html( $sub_heading ); ?></p>
								<?php endif; ?>
								<?php if ( $links ) : ?>
									<ul class="site-footer__link-list" role="list">
										<?php foreach ( $links as $link ) :
											$label = $link['label'] ?? '';
											$url   = $link['url']   ?? '';
											if ( ! $label ) { continue; }
										?>
											<li class="site-footer__link-item">
												<?php if ( $url ) : ?>
													<a href="<?php echo esc_url( $url ); ?>" class="site-footer__link">
														<?php echo esc_html( $label ); ?>
													</a>
												<?php else : ?>
													<span class="site-footer__link"><?php echo esc_html( $label ); ?></span>
												<?php endif; ?>
											</li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>

			<?php /* ── Locations column ── */ ?>
			<?php if ( $locations ) : ?>
				<div class="site-footer__col">
					<p class="site-footer__col-heading"><?php esc_html_e( 'Locations', 'wordie' ); ?></p>
					<ul class="site-footer__link-list" role="list">
						<?php foreach ( $locations as $loc ) :
							$label = $loc['label'] ?? '';
							$url   = $loc['url']   ?? '';
							if ( ! $label ) { continue; }
						?>
							<li class="site-footer__link-item">
								<?php if ( $url ) : ?>
									<a href="<?php echo esc_url( $url ); ?>" class="site-footer__link">
										<?php echo esc_html( $label ); ?>
									</a>
								<?php else : ?>
									<span class="site-footer__link"><?php echo esc_html( $label ); ?></span>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<?php /* ── Quick Links column ── */ ?>
			<?php if ( $quick_links ) : ?>
				<div class="site-footer__col">
					<p class="site-footer__col-heading"><?php esc_html_e( 'Quick Links', 'wordie' ); ?></p>
					<ul class="site-footer__link-list" role="list">
						<?php foreach ( $quick_links as $ql ) :
							$label = $ql['label'] ?? '';
							$url   = $ql['url']   ?? '';
							if ( ! $label ) { continue; }
						?>
							<li class="site-footer__link-item">
								<?php if ( $url ) : ?>
									<a href="<?php echo esc_url( $url ); ?>" class="site-footer__link">
										<?php echo esc_html( $label ); ?>
									</a>
								<?php else : ?>
									<span class="site-footer__link"><?php echo esc_html( $label ); ?></span>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<?php /* ── Contact Us column ── */ ?>
			<?php if ( $address || $phone || $email ) : ?>
				<div class="site-footer__contact">
					<p class="site-footer__col-heading"><?php esc_html_e( 'Contact Us', 'wordie' ); ?></p>
					<ul class="site-footer__contact-list" role="list">

						<?php if ( $address ) : ?>
							<li class="site-footer__contact-item">
								<svg class="site-footer__contact-icon" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
									<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
									<circle cx="12" cy="10" r="3"/>
								</svg>
								<span><?php echo esc_html( $address ); ?></span>
							</li>
						<?php endif; ?>

						<?php if ( $phone ) : ?>
							<li class="site-footer__contact-item">
								<svg class="site-footer__contact-icon" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
									<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.86 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.77 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l.91-.91a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 17z"/>
								</svg>
								<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>" class="site-footer__contact-link">
									<?php echo esc_html( $phone ); ?>
								</a>
							</li>
						<?php endif; ?>

						<?php if ( $email ) : ?>
							<li class="site-footer__contact-item">
								<svg class="site-footer__contact-icon" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
									<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
									<polyline points="22,6 12,13 2,6"/>
								</svg>
								<a href="mailto:<?php echo esc_attr( $email ); ?>" class="site-footer__contact-link">
									<?php echo esc_html( $email ); ?>
								</a>
							</li>
						<?php endif; ?>

					</ul>
				</div>
			<?php endif; ?>

		</div><!-- /.site-footer__main -->

		<?php /* ── Bottom bar: copyright + legal + social ── */ ?>
		<div class="site-footer__bar">

			<p class="site-footer__copyright">
				<?php echo esc_html( $copyright ?: sprintf( '© %d Wordie. All Rights Reserved. A G Squared Company.', gmdate( 'Y' ) ) ); ?>
			</p>

			<?php if ( $legal_links ) : ?>
				<nav class="site-footer__legal" aria-label="<?php esc_attr_e( 'Legal links', 'wordie' ); ?>">
					<ul class="site-footer__legal-list" role="list">
						<?php foreach ( $legal_links as $ll ) :
							$label = $ll['label'] ?? '';
							$url   = $ll['url']   ?? '';
							if ( ! $label ) { continue; }
						?>
							<li>
								<?php if ( $url ) : ?>
									<a href="<?php echo esc_url( $url ); ?>" class="site-footer__legal-link">
										<?php echo esc_html( $label ); ?>
									</a>
								<?php else : ?>
									<span class="site-footer__legal-link"><?php echo esc_html( $label ); ?></span>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</nav>
			<?php endif; ?>

			<?php if ( $linkedin || $facebook ) : ?>
				<div class="site-footer__social" aria-label="<?php esc_attr_e( 'Social media links', 'wordie' ); ?>">

					<?php if ( $linkedin ) : ?>
						<a href="<?php echo esc_url( $linkedin ); ?>" class="site-footer__social-link" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'LinkedIn', 'wordie' ); ?>">
							<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
								<path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/>
								<rect x="2" y="9" width="4" height="12"/>
								<circle cx="4" cy="4" r="2"/>
							</svg>
						</a>
					<?php endif; ?>

					<?php if ( $facebook ) : ?>
						<a href="<?php echo esc_url( $facebook ); ?>" class="site-footer__social-link" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Facebook', 'wordie' ); ?>">
							<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
								<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
							</svg>
						</a>
					<?php endif; ?>

				</div>
			<?php endif; ?>

		</div><!-- /.site-footer__bar -->

	</footer>

<?php wp_footer(); ?>
</body>
</html>
