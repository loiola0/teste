<?php
namespace Jet_Engine\Query_Builder\Listings;

use Jet_Engine\Query_Builder\Manager as Query_Manager;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define listings manager class
 */
class Manager {

	public $source = 'query';
	public $source_meta = '_query_id';

	public function __construct() {
		add_action( 'jet-engine/query-builder/init', array( $this, 'init' ) );
	}

	public function init() {

		add_filter( 'jet-engine/templates/created', array( $this, 'add_meta_to_listing' ), 10, 2 );
		add_filter( 'jet-engine/listing/grid/source', array( $this, 'replace_listing_source' ), 10, 2 );

		require_once Query_Manager::instance()->component_path( 'listings/query.php' );

		new Query();

		if ( jet_engine()->has_elementor() ) {
			require_once Query_Manager::instance()->component_path( 'listings/elementor.php' );
			new Elementor();
		}

		require_once Query_Manager::instance()->component_path( 'listings/blocks.php' );
		new Blocks();

		if ( function_exists( 'jet_smart_filters' ) ) {
			require_once Query_Manager::instance()->component_path( 'listings/filters.php' );
			new Filters();
		}

	}

	/**
	 * Replace listing source if cutom query is enabled
	 *
	 * @param  [type] $source   [description]
	 * @param  [type] $settings [description]
	 * @return [type]           [description]
	 */
	public function replace_listing_source( $source, $settings ) {

		if ( ! empty( $settings['custom_query'] ) && ! empty( $settings['custom_query_id'] ) ) {
			$source = $this->source;
		}

		return $source;

	}

	/**
	 * Add query meta data on listing item creation
	 *
	 * @param [type] $data [description]
	 */
	public function add_meta_to_listing( $listing_id, $data ) {

		$source = ! empty( $_REQUEST['listing_source'] ) ? esc_attr( $_REQUEST['listing_source'] ) : 'posts';

		if ( $this->source === $source && ! empty( $_REQUEST['query_id'] ) ) {
			update_post_meta( $listing_id, $this->source_meta, absint( $_REQUEST['query_id'] ) );
		}

	}

	/**
	 * Setup preview
	 *
	 * @return [type] [description]
	 */
	public function setup_preview( $document = false ) {

		if ( ! $document ) {
			$document = jet_engine()->listings->data->get_listing();
		}

		$source = $document->get_settings( 'listing_source' );

		if ( $this->source !== $source ) {
			return false;
		}

		return $this->get_preview_object_for_document( $document->get_main_id() );

	}

	public function get_preview_object_for_document( $document_id ) {

		$query_id = get_post_meta( $document_id, $this->source_meta, true );

		if ( ! $query_id ) {
			return false;
		}

		$query = Query_Manager::instance()->get_query_by_id( $query_id );

		if ( ! $query ) {
			return false;
		}

		$items = $query->get_items();

		if ( ! empty( $items ) ) {

			$items[0]->_query_id = $query_id;

			jet_engine()->listings->data->set_current_object( $items[0] );
			return $items[0];
		} else {
			return false;
		}

	}

}
