<?php
/**
 * Block: CTA Banner — v2 (OPcache-safe rename)
 * Slug: cta-banner
 * Description: Dark-grey banner with rounded bottom corners — large heading,
 *              supporting text, and a primary + secondary CTA pair.
 * Registered via: acf_register_block_type() in inc/block-registration.php
 * ACF fields: acf-fields/cta-banner.json
 * Figma node: 2958:17193
 *
 * @package wordie
 */

defined( 'ABSPATH' ) || exit;

// ── ACF Fields ────────────────────────────────────────────────────────────────
$heading       = get_sub_field( 'heading' );
$description   = get_sub_field( 'description' );
$cta_primary   = get_sub_field( 'cta_primary' );
$cta_secondary = get_sub_field( 'cta_secondary' );

// ── Empty state ───────────────────────────────────────────────────────────────
if ( ! $heading ) {
	return;
}

// ── Block classes ─────────────────────────────────────────────────────────────
$class = 'block-cta-banner';

?>

<section
	class="<?php echo esc_attr( $class ); ?>"
	data-block="cta-banner"
>
	<div class="block-cta-banner__inner">

		<div class="block-cta-banner__text">
			<h2 class="block-cta-banner__heading"><?php echo esc_html( $heading ); ?></h2>
			<?php if ( $description ) : ?>
				<p class="block-cta-banner__description"><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $cta_primary || $cta_secondary ) : ?>
			<div class="block-cta-banner__ctas">

				<?php if ( $cta_primary && ! empty( $cta_primary['url'] ) ) : ?>
					<a
						href="<?php echo esc_url( $cta_primary['url'] ); ?>"
						class="btn btn--primary block-cta-banner__btn block-cta-banner__btn--primary"
						<?php echo ( '_blank' === $cta_primary['target'] ) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>
					>
						<?php echo esc_html( $cta_primary['title'] ); ?>
						<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
							<line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
						</svg>
					</a>
				<?php endif; ?>

				<?php if ( $cta_secondary && ! empty( $cta_secondary['url'] ) ) : ?>
					<a
						href="<?php echo esc_url( $cta_secondary['url'] ); ?>"
						class="block-cta-banner__btn block-cta-banner__btn--secondary"
						<?php echo ( '_blank' === $cta_secondary['target'] ) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>
					>
						<?php echo esc_html( $cta_secondary['title'] ); ?>
					</a>
				<?php endif; ?>

			</div>
		<?php endif; ?>

	</div>
</section>
