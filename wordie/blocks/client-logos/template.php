<?php
/**
 * Block: Client Logos
 * Slug: client-logos
 * Description: Kicker text + horizontally scrolling client logo marquee on dark background.
 * Registered via: acf_register_block_type() in inc/block-registration.php
 * ACF fields: acf-fields/client-logos.json
 * Figma node: 2923:1418
 *
 * @package wordie
 */

defined( 'ABSPATH' ) || exit;

// ── ACF Fields ────────────────────────────────────────────────────────────────
$kicker_text = get_sub_field( 'kicker_text' );
$logos       = get_sub_field( 'logos' );

// ── Empty state ───────────────────────────────────────────────────────────────
if ( ! $logos ) {
	return;
}

// ── Block classes ─────────────────────────────────────────────────────────────
$class = 'block-client-logos';
if ( ! empty( $block['className'] ) ) {
	$class .= ' ' . esc_attr( $block['className'] );
}

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'block-' . $block['id'];

// Duplicate logos for seamless marquee loop
$logos_doubled = array_merge( $logos, $logos );
?>

<section
	id="<?php echo esc_attr( $block_id ); ?>"
	class="<?php echo esc_attr( $class ); ?>"
	data-block="client-logos"
	aria-label="<?php echo esc_attr( $kicker_text ?: __( 'Our clients', 'wordie' ) ); ?>"
>
	<div class="block-client-logos__container">

		<?php if ( $kicker_text ) : ?>
			<p class="block-client-logos__kicker">
				<?php echo esc_html( $kicker_text ); ?>
			</p>
		<?php endif; ?>

		<div class="block-client-logos__marquee" aria-hidden="true">
			<ul class="block-client-logos__track" role="list">
				<?php foreach ( $logos_doubled as $index => $logo ) :
					$image = $logo['logo_image'] ?? null;
					$name  = $logo['logo_name'] ?? '';
					$link  = $logo['logo_link'] ?? '';

					if ( ! $image ) { continue; }
				?>
					<li class="block-client-logos__item">
						<?php if ( $link ) : ?>
							<a
								href="<?php echo esc_url( $link ); ?>"
								class="block-client-logos__link"
								target="_blank"
								rel="noopener noreferrer"
								aria-label="<?php echo esc_attr( $name ?: __( 'Client website', 'wordie' ) ); ?>"
							>
						<?php endif; ?>

						<?php
						echo wp_get_attachment_image(
							$image['ID'],
							[ 152, 81 ],
							false,
							[
								'class'   => 'block-client-logos__logo',
								'loading' => 'lazy',
								'alt'     => esc_attr( $name ),
							]
						);
						?>

						<?php if ( $link ) : ?>
							</a>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>

		<?php
		// Accessible non-animated version for screen readers — renders logos once as a plain list
		?>
		<ul class="block-client-logos__accessible-list sr-only" role="list" aria-label="<?php echo esc_attr( $kicker_text ?: __( 'Client logos', 'wordie' ) ); ?>">
			<?php foreach ( $logos as $logo ) :
				$name = $logo['logo_name'] ?? '';
				$link = $logo['logo_link'] ?? '';
				if ( ! $name ) { continue; }
			?>
				<li>
					<?php if ( $link ) : ?>
						<a href="<?php echo esc_url( $link ); ?>" target="_blank" rel="noopener noreferrer">
							<?php echo esc_html( $name ); ?>
						</a>
					<?php else : ?>
						<?php echo esc_html( $name ); ?>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>

	</div>
</section>
