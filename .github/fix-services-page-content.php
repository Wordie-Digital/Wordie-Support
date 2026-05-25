<?php
/**
 * One-shot: re-import page-sections field group + set correct content on
 * the services-2 page (post ID 9296).
 *
 * Fixes:
 *  - Hero: swap heading/subheading, add kicker "SERVICES"
 *  - Services grid: set background_style=light, add subtitle
 *  - Registers new ACF sub-fields (kicker, bg_style, subtitle, etc.)
 *
 * Self-destructs after running.
 */
add_action( 'init', function () {

	// ── 1. Re-import the field group so new fields are in the DB ─────────────
	if ( function_exists( 'acf_import_field_group' ) ) {
		$file = get_template_directory() . '/acf-fields/page-sections.json';
		if ( file_exists( $file ) ) {
			$data = json_decode( file_get_contents( $file ), true );
			if ( ! empty( $data ) ) {
				if ( isset( $data['key'] ) ) { $data = [ $data ]; }
				foreach ( $data as $group ) {
					if ( ! empty( $group['key'] ) ) {
						acf_import_field_group( $group );
					}
				}
			}
		}
	}

	// ── 2. Update post meta for services-2 page (post ID 9296) ──────────────
	$post_id = 9296;

	// Hero banner (row-0)
	// Fix: heading was "Services", subheading was the real H1 text
	update_post_meta( $post_id, 'sections_0_heading',    'We Make WordPress Work Harder For You' );
	update_post_meta( $post_id, '_sections_0_heading',   'field_hero_heading' );

	update_post_meta( $post_id, 'sections_0_subheading', 'At Wordie, we use AI tools and WordPress expertise to create effective websites. Whether you need a quick turnaround or custom UX, we\'re ready.' );
	update_post_meta( $post_id, '_sections_0_subheading', 'field_hero_subheading' );

	// Hero kicker — new field
	update_post_meta( $post_id, 'sections_0_kicker',  'SERVICES' );
	update_post_meta( $post_id, '_sections_0_kicker', 'field_hero_kicker' );

	// Services grid (row-1) — background_style and subtitle are new fields
	update_post_meta( $post_id, 'sections_1_background_style',  'light' );
	update_post_meta( $post_id, '_sections_1_background_style', 'field_services_bg_style' );

	update_post_meta( $post_id, 'sections_1_section_subtitle',  'Every engagement starts with design thinking. Every build ships with AI behind it.' );
	update_post_meta( $post_id, '_sections_1_section_subtitle', 'field_services_subtitle' );

	// Services grid kicker — fix capitalisation
	update_post_meta( $post_id, 'sections_1_section_kicker',  'WHAT WE DO' );
	update_post_meta( $post_id, '_sections_1_section_kicker', 'field_services_kicker' );

	// Tech stack (row-2) — fix heading capitalisation
	update_post_meta( $post_id, 'sections_2_section_heading',  'The platforms we know deeply' );
	update_post_meta( $post_id, '_sections_2_section_heading', 'field_tech_heading' );

	// Self-destruct
	@unlink( __FILE__ );

}, 20 );
