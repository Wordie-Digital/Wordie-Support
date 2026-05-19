<?php
/**
 * Block: Tech Stack
 * Slug: tech-stack
 * Description: Centred section header + 3-column primary platform cards (logo + description)
 *              + supporting stack logo row.
 * Registered via: acf_register_block_type() in inc/block-registration.php
 * ACF fields: acf-fields/tech-stack.json
 * Figma node: 3037:17712
 *
 * @package wordie
 */

defined( 'ABSPATH' ) || exit;

// ── ACF Fields ────────────────────────────────────────────────────────────────
$kicker           = get_field( 'section_kicker' );
$heading          = get_field( 'section_heading' );
$description      = get_field( 'section_description' );
$platforms        = get_field( 'platforms' );
$supporting_label = get_field( 'supporting_label' );
$supporting_logos = get_field( 'supporting_logos' );

// ── Empty state ───────────────────────────────────────────────────────────────
if ( ! $heading && ! $platforms ) {
	return;
}

// ── Block classes ─────────────────────────────────────────────────────────────
$class = 'block-tech-stack';
if ( ! empty( $block['className'] ) ) {
	$class .= ' ' . esc_attr( $block['className'] );
}

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'block-' . $block['id'];
?>

<section
	id="<?php echo esc_attr( $block_id ); ?>"
	class="<?php echo esc_attr( $class ); ?>"
	data-block="tech-stack"
	aria-labelledby="<?php echo esc_attr( $block_id ); ?>-heading"
>
	<div class="block-tech-stack__container">

		<?php /* ── Section header (centred) ── */ ?>
		<div class="block-tech-stack__header">
			<?php if ( $kicker ) : ?>
				<p class="block-tech-stack__kicker"><?php echo esc_html( $kicker ); ?></p>
			<?php endif; ?>
			<?php if ( $heading ) : ?>
				<h2 id="<?php echo esc_attr( $block_id ); ?>-heading" class="block-tech-stack__heading">
					<?php echo esc_html( $heading ); ?>
				</h2>
			<?php endif; ?>
			<?php if ( $description ) : ?>
				<p class="block-tech-stack__description"><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>
		</div>

		<?php /* ── Primary platform cards ── */ ?>
		<?php if ( $platforms ) : ?>
			<ul class="block-tech-stack__cards" role="list" aria-label="<?php esc_attr_e( 'Primary platforms', 'wordie' ); ?>">
				<?php foreach ( $platforms as $platform ) :
					$plat_logo = $platform['platform_logo']        ?? null;
					$plat_name = $platform['platform_name']        ?? '';
					$plat_desc = $platform['platform_description'] ?? '';
				?>
					<li class="block-tech-stack__card">
						<?php if ( $plat_logo ) : ?>
							<div class="block-tech-stack__card-logo">
								<?php echo wp_get_attachment_image( $plat_logo['ID'], [ 302, 103 ], false, [
									'class'   => 'block-tech-stack__card-logo-img',
									'loading' => 'lazy',
									'alt'     => esc_attr( $plat_logo['alt'] ?: $plat_name ),
								] ); ?>
							</div>
						<?php endif; ?>
						<?php if ( $plat_desc ) : ?>
							<p class="block-tech-stack__card-desc"><?php echo esc_html( $plat_desc ); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<?php /* ── Supporting stack logo row ── */ ?>
		<?php if ( $supporting_logos ) : ?>
			<div class="block-tech-stack__supporting">
				<?php if ( $supporting_label ) : ?>
					<p class="block-tech-stack__supporting-label"><?php echo esc_html( $supporting_label ); ?></p>
				<?php endif; ?>
				<ul class="block-tech-stack__supporting-logos" role="list" aria-label="<?php esc_attr_e( 'Supporting stack', 'wordie' ); ?>">
					<?php foreach ( $supporting_logos as $item ) :
						$sup_logo = $item['logo_image'] ?? null;
						$sup_alt  = $item['logo_alt']   ?? '';
						if ( ! $sup_logo ) { continue; }
					?>
						<li class="block-tech-stack__supporting-logo">
							<?php echo wp_get_attachment_image( $sup_logo['ID'], [ 200, 60 ], false, [
								'class'   => 'block-tech-stack__supporting-logo-img',
								'loading' => 'lazy',
								'alt'     => esc_attr( $sup_alt ?: ( $sup_logo['alt'] ?? '' ) ),
							] ); ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

	</div>
</section>
