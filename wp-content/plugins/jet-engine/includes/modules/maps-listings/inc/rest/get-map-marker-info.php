<?php
namespace Jet_Engine\Modules\Maps_Listings;

/**
 * Get_Map_Marker_Info endpoint
 */
class Get_Map_Marker_Info extends \Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'get-map-marker-info';
	}

	/**
	 * API callback
	 *
	 * @return void|\WP_Error|\WP_REST_Response
	 */
	public function callback( $request ) {

		$params     = $request->get_params();
		$listing_id = $params['listing_id'];
		$post_id    = $params['post_id'];

		if ( ! $listing_id || ! $post_id ) {
			return rest_ensure_response( array(
				'success' => false,
				'html'    => __( 'Required parameters is not found in request', 'jet-engine' ),
			) );
		}

		jet_engine()->listings->data->set_listing_by_id( $listing_id );

		$listing_source = ! empty( $params['source'] ) ? $params['source'] : 'posts';

		switch ( $listing_source ) {
			case 'posts':
				$post_obj = get_post( $post_id );
				break;

			case 'terms':
				$post_obj = get_term( $post_id );
				break;

			case 'users':
				$post_obj = get_user_by( 'ID', $post_id );
				break;

			default:
				$post_obj = apply_filters(
					'jet-engine/maps-listing/rest/object/' . $listing_source,
					false,
					$post_id
				);
		}

		if ( ! $post_obj || is_wp_error( $post_obj ) ) {
			return rest_ensure_response( array(
				'success' => false,
				'html'    => __( 'Requested post not found', 'jet-engine' ),
			) );
		}

		jet_engine()->frontend->set_listing( $listing_id );

		do_action( 'jet-engine/maps-listings/get-map-marker' );

		ob_start();

		$content = jet_engine()->frontend->get_listing_item( $post_obj );
		$content = sprintf( '<div class="jet-map-popup-%1$d jet-listing-dynamic-post-%1$d">%2$s</div>', $post_id, $content );
		$content = apply_filters( 'jet-engine/maps-listings/marker-content', $content, $post_obj, $listing_id );

		$content .= ob_get_clean();

		$result = array(
			'success' => true,
			'html'    => $content,
		);

		return rest_ensure_response( $result );

	}

	/**
	 * Returns endpoint request method - GET/POST/PUT/DELTE
	 *
	 * @return string
	 */
	public function get_method() {
		return 'GET';
	}

	/**
	 * Check user access to current end-popint
	 *
	 * @return bool
	 */
	public function permission_callback( $request ) {
		return true;
	}

	/**
	 * Returns arguments config
	 *
	 * @return array
	 */
	public function get_args() {
		return array(
			'listing_id' => array(
				'default'  => 0,
				'required' => true,
			),
			'post_id' => array(
				'default'  => 0,
				'required' => true,
			),
			'source' => array(
				'default'  => 'posts',
				'required' => false,
			),
		);
	}

}
