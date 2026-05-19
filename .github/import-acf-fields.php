<?php
/**
 * Imports all ACF field group JSON files from the Wordie theme's acf-fields
 * directory, then self-destructs.
 */
add_action( 'acf/init', function () {
	if ( ! function_exists( 'acf_import_field_group' ) ) {
		return;
	}

	$dir   = get_template_directory() . '/acf-fields';
	$files = glob( $dir . '/*.json' );

	if ( empty( $files ) ) {
		return;
	}

	foreach ( $files as $file ) {
		$json = file_get_contents( $file );
		if ( ! $json ) {
			continue;
		}

		$data = json_decode( $json, true );
		if ( empty( $data ) ) {
			continue;
		}

		// ACF JSON can be a single group or an array of groups.
		if ( isset( $data['key'] ) ) {
			$data = [ $data ];
		}

		foreach ( $data as $field_group ) {
			if ( ! empty( $field_group['key'] ) ) {
				acf_import_field_group( $field_group );
			}
		}
	}

	@unlink( __FILE__ );
}, 99 );
