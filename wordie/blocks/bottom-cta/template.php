<?php
/**
 * Block: Bottom CTA
 * Slug: bottom-cta
 * Description: Dark-teal section with rounded top corners — heading, description,
 *              trust line and dual CTA buttons. Visually connects to the site footer.
 * Registered via: acf_register_block_type() in inc/block-registration.php
 * ACF fields: acf-fields/bottom-cta.json
 * Figma node: 2942:14970 (CTA portion — 397px)
 *
 * @package wordie
 */

defined( 'ABSPATH' ) || exit;

// ── ACF Fields ────────────────────────────────────────────────────────────────
$heading       = get_sub_field( 'section_heading' );
$description   = get_sub_field( 'section_description' );
$trust_line    = get_sub_field( 'trust_line' );
$cta_primary   = get_sub_field( 'cta_primary' );
$cta_secondary = get_sub_field( 'cta_secondary' );

// Wordie logo from global options (shown top-right per Figma)
$site_logo = get_sub_field( 'site_logo', 'option' );

// ── Empty state ───────────────────────────────────────────────────────────────
if ( ! $heading ) {
	return;
}

// ── Block classes ─────────────────────────────────────────────────────────────
$class = 'block-bottom-cta';
if ( ! empty( $block['className'] ) ) {
	$class .= ' ' . esc_attr( $block['className'] );
}

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'block-' . $block['id'];
?>

<section
	id="<?php echo esc_attr( $block_id ); ?>"
	class="<?php echo esc_attr( $class ); ?>"
	data-block="bottom-cta"
>
	<div class="block-bottom-cta__inner">

		<div class="block-bottom-cta__content">

			<div class="block-bottom-cta__text">
				<h2 class="block-bottom-cta__heading"><?php echo esc_html( $heading ); ?></h2>
				<?php if ( $description ) : ?>
					<p class="block-bottom-cta__description"><?php echo esc_html( $description ); ?></p>
				<?php endif; ?>
				<?php if ( $trust_line ) : ?>
					<p class="block-bottom-cta__trust"><?php echo esc_html( $trust_line ); ?></p>
				<?php endif; ?>
			</div>

			<?php if ( $cta_primary || $cta_secondary ) : ?>
				<div class="block-bottom-cta__ctas">
					<?php if ( $cta_primary && ! empty( $cta_primary['url'] ) ) : ?>
						<a
							href="<?php echo esc_url( $cta_primary['url'] ); ?>"
							class="block-bottom-cta__btn block-bottom-cta__btn--primary"
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
							class="block-bottom-cta__btn block-bottom-cta__btn--secondary"
							<?php echo ( '_blank' === $cta_secondary['target'] ) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>
						>
							<?php echo esc_html( $cta_secondary['title'] ); ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>

		</div>

		<?php if ( $site_logo ) : ?>
			<div class="block-bottom-cta__logo" aria-hidden="true">
				<?php echo wp_get_attachment_image( $site_logo['ID'], [ 276, 70 ], false, [
					'class' => 'block-bottom-cta__logo-img',
					'alt'   => '',
				] ); ?>
			</div>
		<?php endif; ?>

	</div>
</section>
