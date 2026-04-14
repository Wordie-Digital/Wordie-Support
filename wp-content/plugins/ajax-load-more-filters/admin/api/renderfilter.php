<?php
/**
 * Init Endpoint.
 */
add_action( 'rest_api_init', function () {
	$my_namespace = 'alm-filters';
	$my_endpoint  = '/renderfilter';
	register_rest_route(
		$my_namespace,
		$my_endpoint,
		array(
			'methods'             => 'POST',
			'callback'            => 'renderfilter',
			'permission_callback' => '__return_true',
		)
	);
});



/*
*  renderfilter
*  Get the filter data as PHP
*
*  @param $request      $_POST
*  @return $response    json
*  @since 1.0

*/

function renderfilter( WP_REST_Request $request ) {

	error_reporting(E_ALL|E_STRICT);

	// Get contents of request and convert to array
	$data = (array)json_decode($request->get_body());

	// access the data obj
	$data = $data['data'];

	if($data){
		// Get the option and unserialize
		$filter = unserialize(get_option('alm_filter_'. $data));
	}

	// Parse the json
	$array = json_decode(json_encode($filter), true);


	if($array){
		$response = array(
			'success' => true,
			'msg'     => '',
			'code'    => json_encode($array, JSON_PRETTY_PRINT)
		);
	} else {
		$response = array(
			'success' => false,
			'msg'     => __('Error accessing filter', 'ajax-load-more-filters '),
			'code'    => ''
		);
	}

	wp_send_json($response); // Send JSON response

}
