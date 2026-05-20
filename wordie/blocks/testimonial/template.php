<?php
/**
 * Block: Testimonial
 * Slug: testimonial
 * Description: Two-column testimonial carousel — left panel with client logo,
 *              quote, author name/title and prev/next nav; right panel with a
 *              portrait image (3:2, border-radius 12px).
 * Registered via: acf_register_block_type() in inc/block-registration.php
 * ACF fields: acf-fields/testimonial.json
 * Figma node: 3045:18172
 *
 * @package wordie
 */

defined( 'ABSPATH' ) || exit;

// ── ACF Fields ────────────────────────────────────────────────────────────────
$testimonials = get_sub_field( 'testimonials' );

// ── Empty state ───────────────────────────────────────────────────────────────
if ( ! $testimonials ) {
	return;
}

// ── Block classes ─────────────────────────────────────────────────────────────
$class = 'block-testimonial';
if ( ! empty( $block['className'] ) ) {
	$class .= ' ' . esc_attr( $block['className'] );
}

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'block-' . $block['id'];
$total    = count( $testimonials );
?>

<section
	id="<?php echo esc_attr( $block_id ); ?>"
	class="<?php echo esc_attr( $class ); ?>"
	data-block="testimonial"
	data-testimonial
	aria-label="<?php esc_attr_e( 'Client testimonials', 'wordie' ); ?>"
>

	<?php /* ── Slides ── */ ?>
	<div class="block-testimonial__slides" aria-live="polite" aria-atomic="true">

		<?php foreach ( $testimonials as $i => $t ) :
			$is_first    = ( 0 === $i );
			$logo        = $t['client_logo']   ?? null;
			$quote       = $t['quote']         ?? '';
			$author_name = $t['author_name']   ?? '';
			$author_role = $t['author_title']  ?? '';
			$portrait    = $t['author_image']  ?? null;
		?>
			<article
				class="block-testimonial__slide<?php echo $is_first ? ' is-active' : ''; ?>"
				aria-hidden="<?php echo $is_first ? 'false' : 'true'; ?>"
				aria-roledescription="slide"
				aria-label="<?php echo esc_attr( sprintf( __( 'Testimonial %1$d of %2$d', 'wordie' ), $i + 1, $total ) ); ?>"
				data-index="<?php echo esc_attr( $i ); ?>"
			>

				<?php /* Left: content */ ?>
				<div class="block-testimonial__content">

					<div class="block-testimonial__text">

						<?php if ( $logo ) : ?>
							<div class="block-testimonial__logo">
								<?php echo wp_get_attachment_image( $logo['ID'], [ 101, 81 ], false, [
									'class'   => 'block-testimonial__logo-img',
									'loading' => $is_first ? 'eager' : 'lazy',
									'alt'     => esc_attr( $logo['alt'] ?: $author_name ),
								] ); ?>
							</div>
						<?php endif; ?>

						<?php if ( $quote ) : ?>
							<blockquote class="block-testimonial__quote">
								<?php echo esc_html( $quote ); ?>
							</blockquote>
						<?php endif; ?>

						<?php if ( $author_name ) : ?>
							<div class="block-testimonial__author">
								<p class="block-testimonial__author-name"><?php echo esc_html( $author_name ); ?></p>
								<?php if ( $author_role ) : ?>
									<p class="block-testimonial__author-role"><?php echo esc_html( $author_role ); ?></p>
								<?php endif; ?>
							</div>
						<?php endif; ?>

					</div>

					<?php if ( $total > 1 ) : ?>
						<div class="block-testimonial__nav" role="group" aria-label="<?php esc_attr_e( 'Testimonial navigation', 'wordie' ); ?>">
							<button
								class="block-testimonial__nav-btn block-testimonial__nav-btn--prev"
								aria-label="<?php esc_attr_e( 'Previous testimonial', 'wordie' ); ?>"
								data-prev
							>
								<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
									<line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
								</svg>
							</button>
							<button
								class="block-testimonial__nav-btn block-testimonial__nav-btn--next"
								aria-label="<?php esc_attr_e( 'Next testimonial', 'wordie' ); ?>"
								data-next
							>
								<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
									<line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
								</svg>
							</button>
						</div>
					<?php endif; ?>

				</div>

				<?php /* Right: portrait image */ ?>
				<figure class="block-testimonial__figure">
					<?php if ( $portrait ) :
						echo wp_get_attachment_image( $portrait['ID'], 'wordie-portrait', false, [
							'class'   => 'block-testimonial__portrait',
							'loading' => $is_first ? 'eager' : 'lazy',
							'alt'     => esc_attr( $portrait['alt'] ?: $author_name ),
						] );
					else : ?>
						<div class="block-testimonial__portrait-placeholder" aria-hidden="true"></div>
					<?php endif; ?>
				</figure>

			</article>
		<?php endforeach; ?>

	</div>

</section>
