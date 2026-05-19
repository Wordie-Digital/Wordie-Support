<?php
/**
 * Block: Services Grid
 * Slug: services-grid
 * Description: Section kicker + H2 header + 3-column grid of bordered service cards.
 * Registered via: acf_register_block_type() in inc/block-registration.php
 * ACF fields: acf-fields/services-grid.json
 * Figma node: 2957:2984
 *
 * @package wordie
 */

defined( 'ABSPATH' ) || exit;

// ── ACF Fields ────────────────────────────────────────────────────────────────
$kicker   = get_field( 'section_kicker' );
$heading  = get_field( 'section_heading' );
$services = get_field( 'services' );

// ── Empty state ───────────────────────────────────────────────────────────────
if ( ! $services ) {
	return;
}

// ── Block classes ─────────────────────────────────────────────────────────────
$class = 'block-services-grid';
if ( ! empty( $block['className'] ) ) {
	$class .= ' ' . esc_attr( $block['className'] );
}

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'block-' . $block['id'];
?>

<section
	id="<?php echo esc_attr( $block_id ); ?>"
	class="<?php echo esc_attr( $class ); ?>"
	data-block="services-grid"
	aria-labelledby="<?php echo esc_attr( $block_id ); ?>-heading"
>
	<div class="block-services-grid__bg-decoration" aria-hidden="true"></div>

	<div class="block-services-grid__container">

		<?php if ( $kicker || $heading ) : ?>
			<header class="block-services-grid__header">

				<?php if ( $kicker ) : ?>
					<p class="block-services-grid__kicker">
						<?php echo esc_html( $kicker ); ?>
					</p>
				<?php endif; ?>

				<?php if ( $heading ) : ?>
					<h2
						id="<?php echo esc_attr( $block_id ); ?>-heading"
						class="block-services-grid__heading"
					>
						<?php echo esc_html( $heading ); ?>
					</h2>
				<?php endif; ?>

			</header>
		<?php endif; ?>

		<ul class="block-services-grid__cards" role="list">
			<?php foreach ( $services as $service ) :
				$title       = $service['service_title']       ?? '';
				$description = $service['service_description'] ?? '';
				$link        = $service['service_link']        ?? null;

				if ( ! $title ) { continue; }
			?>
				<li class="block-services-grid__card">

					<div class="block-services-grid__card-content">

						<?php if ( $title ) : ?>
							<h3 class="block-services-grid__card-title">
								<?php echo esc_html( $title ); ?>
							</h3>
						<?php endif; ?>

						<?php if ( $description ) : ?>
							<p class="block-services-grid__card-description">
								<?php echo esc_html( $description ); ?>
							</p>
						<?php endif; ?>

					</div>

					<?php if ( $link && ! empty( $link['url'] ) ) : ?>
						<a
							href="<?php echo esc_url( $link['url'] ); ?>"
							class="block-services-grid__card-link"
							<?php echo ( '_blank' === $link['target'] ) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>
						>
							<?php echo esc_html( $link['title'] ); ?>
							<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
								<line x1="5" y1="12" x2="19" y2="12"/>
								<polyline points="12 5 19 12 12 19"/>
							</svg>
							<?php if ( '_blank' === $link['target'] ) : ?>
								<span class="sr-only"><?php esc_html_e( '(opens in new tab)', 'wordie' ); ?></span>
							<?php endif; ?>
						</a>
					<?php endif; ?>

				</li>
			<?php endforeach; ?>
		</ul>

	</div>
</section>
