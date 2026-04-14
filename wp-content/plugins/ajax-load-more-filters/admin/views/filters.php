<?php

$editing   = $deleted = false;
$filter_id = $filter_vue = '';
$section   = 'dashboard';

// Export/Import, New.
if ( isset( $_GET['action'] ) ) {
	if ( $_GET['action'] === 'tools' ) {
		$section = 'tools';
	}
	if ( $_GET['action'] === 'new' ) {
		$section = 'new';
	}
}

// Edit Filter [EDIT Mode].
if ( isset( $_GET['filter'] ) ) {
	$filter = get_option( 'alm_filter_' . $_GET['filter'] );
	if ( $filter ) {
		$filter_id  = $_GET['filter'];
		$section    = 'edit';
		$editing    = true;
		$filter     = unserialize( $filter ); // Unseralize the filter array.
		$filter_vue = json_encode( $filter ); // encode json to read in Vue.
	}
}

// Delete Filter.
$deleted_filter = '';
if ( isset( $_GET['delete_filter'] ) ) {
	$deleted_filter = $_GET['delete_filter'];
	// Confirm option exists.
	if ( ! empty( get_option( 'alm_filter_' . $_GET['delete_filter'] ) ) ) {
		delete_option( 'alm_filter_' . $_GET['delete_filter'] );
		$deleted = true;
		$section = 'dashboard';
	}
}

$selected = ' selected="selected"';
?>

<div class="admin ajax-load-more" id="alm-filters">
	<div class="wrap main-cnkt-wrap">
		<header class="header-wrap">
			<h1>
				<?php echo esc_html( ALM_TITLE ); ?>: <strong><?php esc_html_e( 'Filters', 'ajax-load-more-filters' ); ?></strong>
				<em><?php esc_html_e( 'Build and manage your Ajax Load More filters', 'ajax-load-more-filters' ); ?></em>
			</h1>
		</header>
		<?php
		if ( 'dashboard' === $section ) {
			include ALM_FILTERS_PATH . 'admin/views/includes/dashboard.php';
		} elseif ( 'tools' === $section ) {
			include ALM_FILTERS_PATH . 'admin/views/includes/tools.php';
		} else {
			include ALM_FILTERS_PATH . 'admin/views/includes/edit.php';
		}
		?>
	</div>
</div>
