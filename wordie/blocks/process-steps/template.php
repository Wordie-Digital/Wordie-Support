<?php
/**
 * Block: Process Steps
 * Slug: process-steps
 * Description: Left-aligned header + horizontal step row — each step has an
 *              auto-generated coral number, bold title and description, separated
 *              by dashed left borders. A solid bottom border underlines the row.
 * Registered via: acf_register_block_type() in inc/block-registration.php
 * ACF fields: acf-fields/process-steps.json
 * Figma node: 2923:2442
 *
 * @package wordie
 */

defined( 'ABSPATH' ) || exit;

// ── ACF Fields ────────────────────────────────────────────────────────────────
$kicker  = get_sub_field( 'section_kicker' );
$heading = get_sub_field( 'section_heading' );
$steps   = get_sub_field( 'steps' );

// ── Empty state ───────────────────────────────────────────────────────────────
if ( ! $heading && ! $steps ) {
	return;
}

// ── Block classes ─────────────────────────────────────────────────────────────
$class = 'block-process-steps';
if ( ! empty( $block['className'] ) ) {
	$class .= ' ' . esc_attr( $block['className'] );
}

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'block-' . $block['id'];
?>

<section
	id="<?php echo esc_attr( $block_id ); ?>"
	class="<?php echo esc_attr( $class ); ?>"
	data-block="process-steps"
	aria-labelledby="<?php echo esc_attr( $block_id ); ?>-heading"
>
	<div class="block-process-steps__container">

		<?php /* ── Section header ── */ ?>
		<div class="block-process-steps__header">
			<?php if ( $kicker ) : ?>
				<p class="block-process-steps__kicker"><?php echo esc_html( $kicker ); ?></p>
			<?php endif; ?>
			<?php if ( $heading ) : ?>
				<h2 id="<?php echo esc_attr( $block_id ); ?>-heading" class="block-process-steps__heading">
					<?php echo esc_html( $heading ); ?>
				</h2>
			<?php endif; ?>
		</div>

		<?php /* ── Steps row ── */ ?>
		<?php if ( $steps ) : ?>
			<ol class="block-process-steps__steps" role="list">
				<?php foreach ( $steps as $i => $step ) :
					$step_title = $step['step_title']       ?? '';
					$step_desc  = $step['step_description'] ?? '';
					if ( ! $step_title ) { continue; }
				?>
					<li class="block-process-steps__step">
						<span class="block-process-steps__step-number" aria-hidden="true">
							<?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?>
						</span>
						<h3 class="block-process-steps__step-title"><?php echo esc_html( $step_title ); ?></h3>
						<?php if ( $step_desc ) : ?>
							<p class="block-process-steps__step-desc"><?php echo esc_html( $step_desc ); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>

	</div>
</section>
