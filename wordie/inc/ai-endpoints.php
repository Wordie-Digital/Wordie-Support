<?php
/**
 * Wordie — inc/ai-endpoints.php
 *
 * Registers AI/LLM REST API endpoints for content discoverability.
 *
 * Endpoints:
 *  GET /wp-json/ai/v1/site-map      — All pages + CPTs in flat JSON
 *  GET /wp-json/ai/v1/content-model — Block inventory + ACF field structure
 *  GET /wp-json/ai/v1/page/{id}     — Single page with all ACF field output
 *
 * 🚩 ENGINEER NOTE: permission_callback is currently open (__return_true).
 *    Review access control before go-live. Consider rate limiting or
 *    restricting to authenticated requests in production.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'rest_api_init', function () {

	// ── Site map ─────────────────────────────────────────────────────────────
	register_rest_route( 'ai/v1', '/site-map', [
		'methods'             => 'GET',
		'callback'            => 'wordie_ai_site_map',
		'permission_callback' => '__return_true',
	] );

	// ── Content model ────────────────────────────────────────────────────────
	register_rest_route( 'ai/v1', '/content-model', [
		'methods'             => 'GET',
		'callback'            => 'wordie_ai_content_model',
		'permission_callback' => '__return_true',
	] );

	// ── Single page ──────────────────────────────────────────────────────────
	register_rest_route( 'ai/v1', '/page/(?P<id>\d+)', [
		'methods'             => 'GET',
		'callback'            => 'wordie_ai_page',
		'permission_callback' => '__return_true',
		'args'                => [
			'id' => [
				'validate_callback' => fn( $v ) => is_numeric( $v ),
				'sanitize_callback' => 'absint',
			],
		],
	] );

} );

/**
 * Returns all published pages and CPT entries.
 */
function wordie_ai_site_map(): WP_REST_Response {
	$post_types = get_post_types( [ 'public' => true ], 'names' );
	$entries    = [];

	foreach ( $post_types as $type ) {
		$posts = get_posts( [
			'post_type'      => $type,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		] );

		foreach ( $posts as $id ) {
			$entries[] = [
				'id'         => $id,
				'type'       => $type,
				'title'      => get_the_title( $id ),
				'url'        => get_permalink( $id ),
				'modified'   => get_the_modified_date( 'c', $id ),
			];
		}
	}

	return new WP_REST_Response( $entries, 200 );
}

/**
 * Returns the block inventory and ACF field structure.
 */
function wordie_ai_content_model(): WP_REST_Response {
	return new WP_REST_Response( [
		'theme'  => 'wordie',
		'blocks' => [
			[
				'slug'        => 'hero-banner',
				'title'       => 'Hero Banner',
				'description' => 'Full-viewport hero with heading, subheading and dual CTA buttons.',
				'fields'      => [
					'heading'           => 'text — Main hero H1 heading',
					'subheading'        => 'textarea — Supporting body text',
					'cta_primary'       => 'link — Primary CTA (coral button)',
					'cta_secondary'     => 'link — Secondary CTA (white outline button)',
					'background_image'  => 'image — Optional hero background or right-panel image',
				],
			],
			[
				'slug'        => 'client-logos',
				'title'       => 'Client Logos',
				'description' => 'Kicker text + horizontally scrolling marquee of client logos on dark background.',
				'fields'      => [
					'kicker_text' => 'text — Small uppercase label above the logos',
					'logos'       => 'repeater — Each item: logo_image (image), logo_name (text), logo_link (url)',
				],
			],
			[
				'slug'        => 'services-grid',
				'title'       => 'Services Grid',
				'description' => 'Section kicker + H2 heading + 3-column grid of bordered service cards.',
				'fields'      => [
					'section_kicker'  => 'text — Small uppercase label above heading',
					'section_heading' => 'textarea — H2 section heading',
					'services'        => 'repeater — Each item: service_title (text), service_description (textarea), service_link (link)',
				],
			],
			[
				'slug'        => 'work-section',
				'title'       => 'Work Section',
				'description' => 'Featured project carousel with outcome stats panel and thumbnail row navigation.',
				'fields'      => [
					'section_kicker' => 'text — Small uppercase label above heading',
					'section_heading' => 'text — H2 section heading',
					'section_cta'    => 'link — Optional header CTA button',
					'projects'       => 'repeater — Each item: project_image (image), project_client_logo (image), project_title (text), project_link (link), project_stats (repeater: stat_value, stat_label), project_description (textarea), project_tags (text)',
				],
			],
			[
				'slug'        => 'why-wordie',
				'title'       => 'Why Wordie',
				'description' => 'Dark two-column section — left panel with kicker/heading/intro; right panel with auto-numbered reasons.',
				'fields'      => [
					'section_kicker'      => 'text — Small uppercase label',
					'section_heading'     => 'text — H2 heading',
					'section_description' => 'textarea — Intro paragraph',
					'reasons'             => 'repeater — Each item: reason_title (text), reason_description (textarea). Numbers auto-generated.',
				],
			],
			[
				'slug'        => 'cta-banner',
				'title'       => 'CTA Banner',
				'description' => 'Dark-grey banner with rounded bottom corners, large heading, supporting text, and dual CTA buttons.',
				'fields'      => [
					'section_heading'     => 'text — Large H2 heading',
					'section_description' => 'text — Short supporting line',
					'cta_primary'         => 'link — Coral primary CTA button',
					'cta_secondary'       => 'link — White/coral secondary CTA button',
				],
			],
			[
				'slug'        => 'tech-stack',
				'title'       => 'Tech Stack',
				'description' => 'Centred header + 3-column primary platform cards + supporting stack logo row.',
				'fields'      => [
					'section_kicker'      => 'text — Small uppercase label',
					'section_heading'     => 'text — H2 heading',
					'section_description' => 'textarea — Intro paragraph',
					'platforms'           => 'repeater — Each item: platform_logo (image), platform_name (text), platform_description (textarea)',
					'supporting_label'    => 'text — Label above the supporting logos row',
					'supporting_logos'    => 'repeater — Each item: logo_image (image), logo_alt (text)',
				],
			],
			[
				'slug'        => 'process-steps',
				'title'       => 'Process Steps',
				'description' => 'Left-aligned header + horizontal row of steps with dashed left borders and auto-numbered coral step numbers.',
				'fields'      => [
					'section_kicker' => 'text — Small uppercase label',
					'section_heading' => 'text — H2 heading',
					'steps'          => 'repeater — Each item: step_title (text), step_description (textarea). Numbers auto-generated.',
				],
			],
			[
				'slug'        => 'testimonial',
				'title'       => 'Testimonial',
				'description' => 'Two-column testimonial carousel — client logo, quote and author on the left; portrait image on the right.',
				'fields'      => [
					'testimonials' => 'repeater — Each item: client_logo (image), author_image (image), quote (textarea), author_name (text), author_title (text)',
				],
			],
			[
				'slug'        => 'bottom-cta',
				'title'       => 'Bottom CTA',
				'description' => 'Dark-teal panel with rounded top corners — visually connects to the footer. Heading, description, trust line, dual CTAs, and site logo.',
				'fields'      => [
					'section_heading'     => 'text — H2 heading (required)',
					'section_description' => 'textarea — Supporting paragraph (2–3 sentences)',
					'trust_line'          => 'text — Short bold reassurance line below description',
					'cta_primary'         => 'link — Coral primary CTA button (required)',
					'cta_secondary'       => 'link — White/coral secondary CTA button (optional)',
				],
			],
		],
		'options_pages' => [
			'navigation' => [
				'site_logo' => 'image — Header logo',
				'nav_cta'   => 'link — Navigation CTA button ("Brief us")',
			],
			'footer' => [
				'footer_phone'   => 'text — Contact phone number',
				'footer_email'   => 'email — Contact email address',
				'footer_address' => 'textarea — Physical address',
				'social_linkedin'=> 'url — LinkedIn profile URL',
				'social_instagram'=> 'url — Instagram profile URL',
			],
		],
	], 200 );
}

/**
 * Returns a single page's title, URL and all ACF field values.
 *
 * @param WP_REST_Request $request
 */
function wordie_ai_page( WP_REST_Request $request ): WP_REST_Response {
	$id   = $request->get_param( 'id' );
	$post = get_post( $id );

	if ( ! $post || 'publish' !== $post->post_status ) {
		return new WP_REST_Response( [ 'error' => 'Page not found.' ], 404 );
	}

	return new WP_REST_Response( [
		'id'     => $id,
		'title'  => get_the_title( $id ),
		'url'    => get_permalink( $id ),
		'fields' => get_fields( $id ) ?: [],
	], 200 );
}
