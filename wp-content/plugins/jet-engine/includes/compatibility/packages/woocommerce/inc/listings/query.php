<?php

namespace Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package\Listings;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Query {

	/**
	 * Constructor for the class
	 */
	public function __construct() {

		add_filter(
			'jet-engine/listings/macros-list',
			[ $this, 'get_wc_products_query_macros' ]
		);

		add_filter(
			'woocommerce_product_data_store_cpt_get_products_query',
			[ $this, 'handle_meta_query_var' ],
			10, 2
		);

	}

	/**
	 * Expand available macros list with WC_Products_Query macros.
	 *
	 * @param $macros_list
	 *
	 * @return mixed
	 */
	public function get_wc_products_query_macros( $macros_list ) {

		$macros_list['wc_product_title'] = [
			'label' => esc_html__( 'WC Product Title', 'jet-engine' ),
			'cb'    => [ $this, 'get_product_title' ],
		];

		return $macros_list;

	}

	/**
	 * Get WC_Product object title
	 *
	 * @param null $field_value
	 *
	 * @return string
	 */
	public function get_product_title( $field_value = null ) {

		$object = jet_engine()->listings->data->get_current_object();

		if ( ! $object || ! is_subclass_of( $object, 'WC_Product' ) ) {
			return '';
		}

		return $object->get_title();

	}

	/**
	 * Handle a meta query var.
	 *
	 * @param array $query      - Args for WP_Query.
	 * @param array $query_vars - Query vars from WC_Product_Query.
	 *
	 * @return array modified $query
	 */
	public function handle_meta_query_var( $query, $query_vars ) {

		$raw = null;

		if ( ! empty( $query_vars['meta_query'] ) ) {
			$raw = $query_vars['meta_query'];
		}

		if ( ! $raw ) {
			return $query;
		}

		if ( ! empty( $query_vars['meta_query_relation'] ) ) {
			$query['meta_query']['relation'] = $query_vars['meta_query_relation'];
		}

		foreach ( $raw as $query_row ) {
			$query['meta_query'][] = $query_row;
		}

		return apply_filters( 'jet-engine/listing/query/wc-product-query/meta-query-var', $query );

	}

}