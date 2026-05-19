<?php
/**
 * Block: Hero Banner
 * Slug: hero-banner
 * Description: Full-viewport hero with heading, subheading and dual CTA buttons.
 * Registered via: acf_register_block_type() in inc/block-registration.php
 * ACF fields: acf-fields/hero-banner.json
 * Figma node: 2923:1409
 *
 * @package wordie
 */

defined( 'ABSPATH' ) || exit;

// ── ACF Fields ────────────────────────────────────────────────────────────────
$heading            = get_field( 'heading' );
$subheading         = get_field( 'subheading' );
$cta_primary        = get_field( 'cta_primary' );    // link — url, title, target
$cta_secondary      = get_field( 'cta_secondary' );  // link — url, title, target
$media_type         = get_field( 'media_type' ) ?: 'image'; // 'image' | 'video'
$background_image   = get_field( 'background_image' ); // image array
$background_video   = get_field( 'background_video' );  // file array

// ── Empty state ───────────────────────────────────────────────────────────────
if ( ! $heading && ! $subheading ) {
	return;
}

// ── Block classes ─────────────────────────────────────────────────────────────
$class = 'block-hero-banner';
if ( ! empty( $block['className'] ) ) {
	$class .= ' ' . esc_attr( $block['className'] );
}

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'block-' . $block['id'];
?>

<section
	id="<?php echo esc_attr( $block_id ); ?>"
	class="<?php echo esc_attr( $class ); ?>"
	data-block="hero-banner"
	aria-label="<?php echo esc_attr( $heading ?: __( 'Hero section', 'wordie' ) ); ?>"
>

	<?php if ( 'video' === $media_type && $background_video && ! empty( $background_video['url'] ) ) : ?>
		<div class="block-hero-banner__bg" aria-hidden="true">
			<video
				class="block-hero-banner__bg-video"
				autoplay
				muted
				loop
				playsinline
				preload="metadata"
			>
				<source src="<?php echo esc_url( $background_video['url'] ); ?>" type="video/mp4">
			</video>
		</div>
	<?php elseif ( $background_image ) : ?>
		<div class="block-hero-banner__bg" aria-hidden="true">
			<?php
			echo wp_get_attachment_image(
				$background_image['ID'],
				'wordie-hero',
				false,
				[
					'class'         => 'block-hero-banner__bg-img',
					'loading'       => 'eager',
					'fetchpriority' => 'high',
					'alt'           => '',
				]
			);
			?>
		</div>
	<?php endif; ?>

	<div class="block-hero-banner__container">
		<div class="block-hero-banner__content">

			<?php if ( $heading ) : ?>
				<h1 class="block-hero-banner__heading">
					<?php echo esc_html( $heading ); ?>
				</h1>
			<?php endif; ?>

			<?php if ( $subheading ) : ?>
				<p class="block-hero-banner__subheading">
					<?php echo esc_html( $subheading ); ?>
				</p>
			<?php endif; ?>

			<?php if ( $cta_primary || $cta_secondary ) : ?>
				<div class="block-hero-banner__ctas">

					<?php if ( $cta_primary && ! empty( $cta_primary['url'] ) ) : ?>
						<a
							href="<?php echo esc_url( $cta_primary['url'] ); ?>"
							class="block-hero-banner__cta-primary"
							<?php echo ( '_blank' === $cta_primary['target'] ) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>
						>
							<?php echo esc_html( $cta_primary['title'] ); ?>
							<svg class="icon" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
								<line x1="5" y1="12" x2="19" y2="12"/>
								<polyline points="12 5 19 12 12 19"/>
							</svg>
							<?php if ( '_blank' === $cta_primary['target'] ) : ?>
								<span class="sr-only"><?php esc_html_e( '(opens in new tab)', 'wordie' ); ?></span>
							<?php endif; ?>
						</a>
					<?php endif; ?>

					<?php if ( $cta_secondary && ! empty( $cta_secondary['url'] ) ) : ?>
						<a
							href="<?php echo esc_url( $cta_secondary['url'] ); ?>"
							class="block-hero-banner__cta-secondary"
							<?php echo ( '_blank' === $cta_secondary['target'] ) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>
						>
							<?php echo esc_html( $cta_secondary['title'] ); ?>
							<?php if ( '_blank' === $cta_secondary['target'] ) : ?>
								<span class="sr-only"><?php esc_html_e( '(opens in new tab)', 'wordie' ); ?></span>
							<?php endif; ?>
						</a>
					<?php endif; ?>

				</div>
			<?php endif; ?>

		</div>
	</div>

</section>
