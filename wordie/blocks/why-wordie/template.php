<?php
/**
 * Block: Why Wordie
 * Slug: why-wordie
 * Description: Dark-grey two-column section — left panel with kicker/heading/intro,
 *              right panel with auto-numbered reasons (01, 02, 03…).
 * Registered via: acf_register_block_type() in inc/block-registration.php
 * ACF fields: acf-fields/why-wordie.json
 * Figma node: 3035:17031
 *
 * @package wordie
 */

defined( 'ABSPATH' ) || exit;

// ── ACF Fields ────────────────────────────────────────────────────────────────
$kicker      = get_sub_field( 'section_kicker' );
$heading     = get_sub_field( 'section_heading' );
$description = get_sub_field( 'section_description' );
$reasons     = get_sub_field( 'reasons' );
$bg_style    = get_sub_field( 'background_style' ) ?: 'dark';

// ── Empty state ───────────────────────────────────────────────────────────────
if ( ! $heading && ! $reasons ) {
	return;
}

// ── Block classes ─────────────────────────────────────────────────────────────
$class = 'block-why-wordie block-why-wordie--' . esc_attr( $bg_style );

?>

<section
	class="<?php echo esc_attr( $class ); ?>"
	data-block="why-wordie"
	aria-labelledby="<?php echo esc_attr( $block_id ); ?>-heading"
>
	<div class="block-why-wordie__container">

		<?php /* ── Left panel ── */ ?>
		<div class="block-why-wordie__panel">

			<?php if ( $kicker ) : ?>
				<p class="block-why-wordie__kicker"><?php echo esc_html( $kicker ); ?></p>
			<?php endif; ?>

			<?php if ( $heading ) : ?>
				<h2 class="block-why-wordie__heading" id="<?php echo esc_attr( $block_id ); ?>-heading">
					<?php echo esc_html( $heading ); ?>
				</h2>
			<?php endif; ?>

			<?php if ( $description ) : ?>
				<p class="block-why-wordie__description"><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>

		</div>

		<?php /* ── Right panel — numbered reasons ── */ ?>
		<?php if ( $reasons ) : ?>
			<ol class="block-why-wordie__reasons" role="list" aria-label="<?php esc_attr_e( 'Reasons to choose Wordie', 'wordie' ); ?>">
				<?php foreach ( $reasons as $i => $reason ) :
					$reason_title = $reason['reason_title']       ?? '';
					$reason_desc  = $reason['reason_description'] ?? '';
					if ( ! $reason_title ) { continue; }
				?>
					<li class="block-why-wordie__reason">
						<span class="block-why-wordie__reason-number" aria-hidden="true">
							<?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?>
						</span>
						<h3 class="block-why-wordie__reason-title"><?php echo esc_html( $reason_title ); ?></h3>
						<?php if ( $reason_desc ) : ?>
							<p class="block-why-wordie__reason-desc"><?php echo esc_html( $reason_desc ); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>

	</div>
</section>
