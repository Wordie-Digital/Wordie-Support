<?php
/**
 * This file holds all functionality for the dynamic filter variables.
 *
 * @since 1.10.1
 * @package ajax-load-more-filters
 */

/**
 * Parse dynamic variables in a filter config.
 *
 * @param string $key
 * @param string $value
 * @return string $value
 */
function alm_filters_parse_dynamic_vars( $key, $value ) {

	// Archive %archive%.
	if ( '%archive%' === strtolower( $value ) && is_archive() ) {
		$obj = get_queried_object();

		// Date Query.
		if ( is_date() ) {
			if ( is_year() ) {
				$value = get_the_date( 'Y' );
			}
			if ( is_month() ) {
				$value = get_the_date( 'm' );
			}
			if ( is_day() ) {
				$value = get_the_date( 'd' );
			}
		}

		// Taxonomy, Tag, Category.
		if ( 'taxonomy' === $key || 'tag' === $key || 'category' === $key ) {
			if ( is_tax() || is_category() || is_tag() ) {
				$value = $obj->slug;
			}
		}

		// Author.
		if ( is_author() ) {
			$value = get_the_author_meta( 'ID' );
		}

		// Post Type.
		if ( is_post_type_archive() ) {
			if ( isset( $obj->name ) ) {
				$value = $obj->name;
			}
		}
	}

	return $value;
}
