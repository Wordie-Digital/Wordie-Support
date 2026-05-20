<?php
/**
 * Layout: Hero Banner
 * Flexible Content layout — rendered via page.php sections loop.
 */
defined( 'ABSPATH' ) || exit;

$heading          = get_sub_field( 'heading' );
$subheading       = get_sub_field( 'subheading' );
$cta_primary      = get_sub_field( 'cta_primary' );
$cta_secondary    = get_sub_field( 'cta_secondary' );
$media_type       = get_sub_field( 'media_type' ) ?: 'image';
$background_image = get_sub_field( 'background_image' );
$background_video = get_sub_field( 'background_video' );

if ( ! $heading && ! $subheading ) {
	return;
}
?>

<section
	class="block-hero-banner"
	data-block="hero-banner"
	aria-label="<?php echo esc_attr( $heading ?: __( 'Hero section', 'wordie' ) ); ?>"
>

	<?php if ( 'video' === $media_type && $background_video && ! empty( $background_video['url'] ) ) : ?>
		<div class="block-hero-banner__bg" aria-hidden="true">
			<video
				class="block-hero-banner__bg-video"
				autoplay muted loop playsinline preload="metadata"
			>
				<source src="<?php echo esc_url( $background_video['url'] ); ?>" type="video/mp4">
			</video>
		</div>
	<?php elseif ( $background_image ) : ?>
		<div class="block-hero-banner__bg" aria-hidden="true">
			<?php echo wp_get_attachment_image( $background_image['ID'], 'wordie-hero', false, [
				'class'         => 'block-hero-banner__bg-img',
				'loading'       => 'eager',
				'fetchpriority' => 'high',
				'alt'           => '',
			] ); ?>
		</div>
	<?php endif; ?>

	<div class="block-hero-banner__container">
		<div class="block-hero-banner__content">

			<?php if ( $heading ) : ?>
				<h1 class="block-hero-banner__heading"><?php echo esc_html( $heading ); ?></h1>
			<?php endif; ?>

			<?php if ( $subheading ) : ?>
				<p class="block-hero-banner__subheading"><?php echo esc_html( $subheading ); ?></p>
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
