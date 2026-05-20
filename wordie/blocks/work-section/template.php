<?php
/**
 * Block: Work Section
 * Slug: work-section
 * Description: "Design decisions backed by outcomes" — featured project carousel
 *              with stats panel + thumbnail row. JS-driven prev/next navigation.
 * Registered via: acf_register_block_type() in inc/block-registration.php
 * ACF fields: acf-fields/work-section.json
 * Figma node: 2957:2912
 *
 * @package wordie
 */

defined( 'ABSPATH' ) || exit;

// ── ACF Fields ────────────────────────────────────────────────────────────────
$kicker   = get_sub_field( 'section_kicker' );
$heading  = get_sub_field( 'section_heading' );
$cta      = get_sub_field( 'section_cta' );
$projects = get_sub_field( 'projects' );

// ── Empty state ───────────────────────────────────────────────────────────────
if ( ! $projects ) {
	return;
}

// ── Block classes ─────────────────────────────────────────────────────────────
$class = 'block-work-section';
if ( ! empty( $block['className'] ) ) {
	$class .= ' ' . esc_attr( $block['className'] );
}

$block_id   = ! empty( $block['anchor'] ) ? $block['anchor'] : 'block-' . $block['id'];
$total      = count( $projects );
?>

<section
	id="<?php echo esc_attr( $block_id ); ?>"
	class="<?php echo esc_attr( $class ); ?>"
	data-block="work-section"
	aria-label="<?php echo esc_attr( $heading ?: __( 'Our work', 'wordie' ) ); ?>"
>
	<div class="block-work-section__container">

		<?php /* ── Section header ── */ ?>
		<header class="block-work-section__header">

			<div class="block-work-section__header-text">
				<?php if ( $kicker ) : ?>
					<p class="block-work-section__kicker"><?php echo esc_html( $kicker ); ?></p>
				<?php endif; ?>
				<?php if ( $heading ) : ?>
					<h2 id="<?php echo esc_attr( $block_id ); ?>-heading" class="block-work-section__heading">
						<?php echo esc_html( $heading ); ?>
					</h2>
				<?php endif; ?>
			</div>

			<?php if ( $cta && ! empty( $cta['url'] ) ) : ?>
				<a
					href="<?php echo esc_url( $cta['url'] ); ?>"
					class="btn btn--primary block-work-section__header-cta"
					<?php echo ( '_blank' === $cta['target'] ) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>
				>
					<?php echo esc_html( $cta['title'] ); ?>
					<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
						<line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
					</svg>
				</a>
			<?php endif; ?>

		</header>

		<?php /* ── Featured carousel ── */ ?>
		<div
			class="block-work-section__carousel"
			data-carousel
			aria-label="<?php esc_attr_e( 'Work showcase', 'wordie' ); ?>"
			aria-roledescription="carousel"
		>

			<?php /* ── Slides ── */ ?>
			<div class="block-work-section__slides" aria-live="polite" aria-atomic="true">

				<?php foreach ( $projects as $i => $project ) :
					$is_first       = ( 0 === $i );
					$proj_image     = $project['project_image']       ?? null;
					$client_logo    = $project['project_client_logo'] ?? null;
					$proj_title     = $project['project_title']       ?? '';
					$proj_desc      = $project['project_description'] ?? '';
					$proj_tags      = $project['project_tags']        ?? '';
					$proj_link      = $project['project_link']        ?? null;
					$proj_stats     = $project['project_stats']       ?? [];
				?>
					<article
						class="block-work-section__slide<?php echo $is_first ? ' is-active' : ''; ?>"
						aria-hidden="<?php echo $is_first ? 'false' : 'true'; ?>"
						aria-roledescription="slide"
						aria-label="<?php echo esc_attr( sprintf( __( 'Project %1$d of %2$d: %3$s', 'wordie' ), $i + 1, $total, $proj_title ) ); ?>"
						data-index="<?php echo esc_attr( $i ); ?>"
					>

						<?php /* Featured image */ ?>
						<div class="block-work-section__slide-image">
							<?php if ( $proj_image ) :
								echo wp_get_attachment_image( $proj_image['ID'], 'wordie-card-wide', false, [
									'class'   => 'block-work-section__slide-img',
									'loading' => $is_first ? 'eager' : 'lazy',
									'alt'     => esc_attr( $proj_image['alt'] ?: $proj_title ),
								] );
							else : ?>
								<div class="block-work-section__slide-img-placeholder" aria-hidden="true"></div>
							<?php endif; ?>
						</div>

						<?php /* Details panel */ ?>
						<div class="block-work-section__slide-details">

							<?php if ( $client_logo ) : ?>
								<div class="block-work-section__client-logo">
									<?php echo wp_get_attachment_image( $client_logo['ID'], [ 149, 46 ], false, [
										'class'   => 'block-work-section__client-logo-img',
										'loading' => 'lazy',
										'alt'     => esc_attr( $client_logo['alt'] ?: $proj_title ),
									] ); ?>
								</div>
							<?php endif; ?>

							<div class="block-work-section__slide-text">

								<?php if ( $proj_title ) : ?>
									<h3 class="block-work-section__slide-title">
										<?php echo esc_html( $proj_title ); ?>
									</h3>
								<?php endif; ?>

								<?php if ( $proj_stats ) : ?>
									<ul class="block-work-section__stats" role="list" aria-label="<?php esc_attr_e( 'Project results', 'wordie' ); ?>">
										<?php foreach ( $proj_stats as $stat ) :
											$stat_value = $stat['stat_value'] ?? '';
											$stat_label = $stat['stat_label'] ?? '';
											if ( ! $stat_value ) { continue; }
										?>
											<li class="block-work-section__stat">
												<span class="block-work-section__stat-value"><?php echo esc_html( $stat_value ); ?></span>
												<span class="block-work-section__stat-label"><?php echo esc_html( $stat_label ); ?></span>
											</li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>

								<?php if ( $proj_desc ) : ?>
									<p class="block-work-section__slide-desc">
										<?php echo esc_html( $proj_desc ); ?>
									</p>
								<?php endif; ?>

								<?php if ( $proj_tags ) : ?>
									<p class="block-work-section__slide-tags">
										<?php echo esc_html( $proj_tags ); ?>
									</p>
								<?php endif; ?>

							</div>

							<div class="block-work-section__slide-footer">

								<?php if ( $proj_link && ! empty( $proj_link['url'] ) ) : ?>
									<a
										href="<?php echo esc_url( $proj_link['url'] ); ?>"
										class="block-work-section__case-study-link"
										<?php echo ( '_blank' === $proj_link['target'] ) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>
									>
										<?php echo esc_html( $proj_link['title'] ); ?>
										<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
											<line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
										</svg>
									</a>
								<?php endif; ?>

								<?php if ( $total > 1 ) : ?>
									<div class="block-work-section__nav" role="group" aria-label="<?php esc_attr_e( 'Carousel navigation', 'wordie' ); ?>">
										<button
											class="block-work-section__nav-btn block-work-section__nav-btn--prev"
											aria-label="<?php esc_attr_e( 'Previous project', 'wordie' ); ?>"
											data-prev
										>
											<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
												<line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
											</svg>
										</button>
										<button
											class="block-work-section__nav-btn block-work-section__nav-btn--next"
											aria-label="<?php esc_attr_e( 'Next project', 'wordie' ); ?>"
											data-next
										>
											<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
												<line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
											</svg>
										</button>
									</div>
								<?php endif; ?>

							</div>

						</div>
					</article>
				<?php endforeach; ?>

			</div>

		</div>

		<?php /* ── Thumbnail row ── */ ?>
		<?php if ( $total > 1 ) : ?>
			<ul class="block-work-section__thumbnails" role="list" aria-label="<?php esc_attr_e( 'All projects', 'wordie' ); ?>">
				<?php foreach ( $projects as $i => $project ) :
					$thumb_image = $project['project_image']       ?? null;
					$thumb_title = $project['project_title']       ?? '';
				?>
					<li class="block-work-section__thumbnail<?php echo ( 0 === $i ) ? ' is-active' : ''; ?>">
						<button
							class="block-work-section__thumbnail-btn"
							data-goto="<?php echo esc_attr( $i ); ?>"
							aria-label="<?php echo esc_attr( sprintf( __( 'Show project: %s', 'wordie' ), $thumb_title ) ); ?>"
							aria-pressed="<?php echo ( 0 === $i ) ? 'true' : 'false'; ?>"
						>
							<?php if ( $thumb_image ) :
								echo wp_get_attachment_image( $thumb_image['ID'], 'wordie-thumbnail', false, [
									'class'   => 'block-work-section__thumbnail-img',
									'loading' => 'lazy',
									'alt'     => '',
								] );
							else : ?>
								<div class="block-work-section__thumbnail-placeholder" aria-hidden="true"></div>
							<?php endif; ?>
						</button>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

	</div>
</section>
