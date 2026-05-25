<?php
/**
 * One-shot: directly patch post meta for the services-2 page (post ID 9296).
 *
 * Swaps heading/subheading values, sets kicker = "SERVICES",
 * fixes services grid kicker capitalisation, and sets bg_style = light.
 *
 * NO ACF import — pure wp_postmeta writes only.
 * Self-destructs after running.
 */
add_action( 'wp_loaded', function () {

	$post_id = 9296;

	// ── Hero banner (row 0) ───────────────────────────────────────────────────
	// Current state: heading="Services", subheading="We Make WordPress Work Harder For You"
	// Target state : heading="We Make WordPress Work Harder For You", kicker="SERVICES", subheading=body copy

	update_post_meta( $post_id, 'sections_0_heading',    'We Make WordPress Work Harder For You' );
	update_post_meta( $post_id, '_sections_0_heading',   'field_hero_heading' );

	update_post_meta( $post_id, 'sections_0_subheading', 'At Wordie, we use AI tools and WordPress expertise to create effective websites. Whether you need a quick turnaround or custom UX, we\'re ready.' );
	update_post_meta( $post_id, '_sections_0_subheading', 'field_hero_subheading' );

	update_post_meta( $post_id, 'sections_0_kicker',  'SERVICES' );
	update_post_meta( $post_id, '_sections_0_kicker', 'field_hero_kicker' );

	// ── Services grid (row 1) ─────────────────────────────────────────────────
	update_post_meta( $post_id, 'sections_1_background_style',  'light' );
	update_post_meta( $post_id, '_sections_1_background_style', 'field_services_bg_style' );

	update_post_meta( $post_id, 'sections_1_section_subtitle',  'Every engagement starts with design thinking. Every build ships with AI behind it.' );
	update_post_meta( $post_id, '_sections_1_section_subtitle', 'field_services_subtitle' );

	update_post_meta( $post_id, 'sections_1_section_kicker',  'WHAT WE DO' );
	update_post_meta( $post_id, '_sections_1_section_kicker', 'field_services_kicker' );

	// ── Tech stack (row 2) ────────────────────────────────────────────────────
	update_post_meta( $post_id, 'sections_2_section_heading',  'The platforms we know deeply' );
	update_post_meta( $post_id, '_sections_2_section_heading', 'field_tech_heading' );

	// ── Self-destruct ─────────────────────────────────────────────────────────
	@unlink( __FILE__ );

}, 10 );
