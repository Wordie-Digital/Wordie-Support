<?php
/**
 * Wordie — inc/block-registration.php
 *
 * Registers all ACF Gutenberg blocks and restricts the editor to Wordie blocks only.
 * Blocks are registered via acf_register_block_type() — no block.json for layout blocks.
 */

defined( 'ABSPATH' ) || exit;

// ---------------------------------------------------------------------------
// Register custom block category
// ---------------------------------------------------------------------------
add_filter( 'block_categories_all', function ( $categories ) {
	array_unshift( $categories, [
		'slug'  => 'wordie',
		'title' => 'Wordie',
		'icon'  => null,
	] );
	return $categories;
} );

// ---------------------------------------------------------------------------
// Register ACF blocks
// ---------------------------------------------------------------------------
add_action( 'acf/init', function () {

	if ( ! function_exists( 'acf_register_block_type' ) ) {
		return;
	}

	$blocks = [
		[
			'name'        => 'hero-banner',
			'title'       => __( 'Hero Banner', 'wordie' ),
			'description' => __( 'Full-viewport hero with heading, subheading and dual CTA buttons.', 'wordie' ),
			'icon'        => 'cover-image',
			'keywords'    => [ 'hero', 'banner', 'header', 'wordie' ],
		],
		[
			'name'        => 'client-logos',
			'title'       => __( 'Client Logos', 'wordie' ),
			'description' => __( 'Kicker text + scrolling marquee of client logos on dark background.', 'wordie' ),
			'icon'        => 'awards',
			'keywords'    => [ 'logos', 'clients', 'marquee', 'brands', 'wordie' ],
		],
		[
			'name'        => 'services-grid',
			'title'       => __( 'Services Grid', 'wordie' ),
			'description' => __( 'Section kicker + H2 heading + 3-column grid of bordered service cards.', 'wordie' ),
			'icon'        => 'grid-view',
			'keywords'    => [ 'services', 'cards', 'grid', 'wordie' ],
		],
		[
			'name'        => 'work-section',
			'title'       => __( 'Work Section', 'wordie' ),
			'description' => __( 'Featured project carousel with stats panel and thumbnail row.', 'wordie' ),
			'icon'        => 'portfolio',
			'keywords'    => [ 'work', 'projects', 'case studies', 'carousel', 'wordie' ],
		],
		[
			'name'        => 'why-wordie',
			'title'       => __( 'Why Wordie', 'wordie' ),
			'description' => __( 'Dark two-column section — left heading panel + right auto-numbered reasons.', 'wordie' ),
			'icon'        => 'editor-ol',
			'keywords'    => [ 'why', 'reasons', 'numbered', 'wordie' ],
		],
		[
			'name'        => 'cta-banner',
			'title'       => __( 'CTA Banner', 'wordie' ),
			'description' => __( 'Dark-grey banner with rounded bottom corners, large heading, and dual CTA buttons.', 'wordie' ),
			'icon'        => 'megaphone',
			'keywords'    => [ 'cta', 'call to action', 'banner', 'wordie' ],
		],
		[
			'name'        => 'tech-stack',
			'title'       => __( 'Tech Stack', 'wordie' ),
			'description' => __( 'Centred header + 3-column platform cards + supporting stack logo row.', 'wordie' ),
			'icon'        => 'admin-tools',
			'keywords'    => [ 'tech', 'stack', 'platforms', 'logos', 'wordie' ],
		],
		[
			'name'        => 'process-steps',
			'title'       => __( 'Process Steps', 'wordie' ),
			'description' => __( 'Left-aligned header + horizontal step row with dashed borders and coral auto-numbered steps.', 'wordie' ),
			'icon'        => 'list-view',
			'keywords'    => [ 'process', 'steps', 'numbered', 'workflow', 'wordie' ],
		],
		[
			'name'        => 'testimonial',
			'title'       => __( 'Testimonial', 'wordie' ),
			'description' => __( 'Two-column testimonial carousel — client logo, quote and author on the left; portrait image on the right.', 'wordie' ),
			'icon'        => 'format-quote',
			'keywords'    => [ 'testimonial', 'quote', 'review', 'client', 'wordie' ],
		],
		[
			'name'        => 'bottom-cta',
			'title'       => __( 'Bottom CTA', 'wordie' ),
			'description' => __( 'Dark-teal panel with rounded top corners — heading, description, trust line, dual CTAs and site logo.', 'wordie' ),
			'icon'        => 'phone',
			'keywords'    => [ 'cta', 'call to action', 'bottom', 'contact', 'wordie' ],
		],
	];

	foreach ( $blocks as $block ) {
		acf_register_block_type( [
			'name'            => $block['name'],
			'title'           => $block['title'],
			'description'     => $block['description'],
			'render_template' => WORDIE_DIR . '/blocks/' . $block['name'] . '/template.php',
			'category'        => 'wordie',
			'icon'            => $block['icon'],
			'keywords'        => $block['keywords'],
			'enqueue_style'   => WORDIE_URI . '/assets/css/blocks/' . $block['name'] . '.css',
			'supports'        => [
				'align'    => false,
				'mode'     => true,
				'jsx'      => false,
				'anchor'   => true,
				'multiple' => true,
			],
			'mode'            => 'edit',
		] );
	}

} );

// ---------------------------------------------------------------------------
// Restrict editor to Wordie blocks + core/paragraph fallback
// ---------------------------------------------------------------------------
add_filter( 'allowed_block_types_all', function ( $allowed_blocks, $editor_context ) {

	if ( empty( $editor_context->post ) ) {
		return $allowed_blocks;
	}

	return [
		'acf/hero-banner',
		'acf/client-logos',
		'acf/services-grid',
		'acf/work-section',
		'acf/why-wordie',
		'acf/cta-banner',
		'acf/tech-stack',
		'acf/process-steps',
		'acf/testimonial',
		'acf/bottom-cta',
		'core/paragraph',
	];

}, 10, 2 );
