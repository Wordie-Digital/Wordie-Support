<?php
/**
 * One-shot reimport of page-sections.json into ACF database.
 * Runs on next request, updates the field group (adds kicker, bg_style,
 * section_subtitle, service_icon, expertise_items, why_wordie bg_style),
 * then self-destructs.
 */
add_action( 'acf/init', function () {
	if ( ! function_exists( 'acf_import_field_group' ) ) {
		return;
	}

	$file = get_template_directory() . '/acf-fields/page-sections.json';
	if ( ! file_exists( $file ) ) {
		return;
	}

	$json = file_get_contents( $file );
	$data = json_decode( $json, true );

	if ( empty( $data ) ) {
		return;
	}

	// page-sections.json is a JSON array of one field group
	if ( isset( $data['key'] ) ) {
		$data = [ $data ];
	}

	foreach ( $data as $field_group ) {
		if ( ! empty( $field_group['key'] ) ) {
			acf_import_field_group( $field_group );
		}
	}

	@unlink( __FILE__ );
}, 99 );
