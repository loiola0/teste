<?php
namespace Jet_Engine\Query_Builder\Queries;

abstract class Base_Query {

	public $id            = false;
	public $name          = false;
	public $query         = array();
	public $dynamic_query = array();
	public $final_query   = null;

	public function __construct( $args = array() ) {

		$this->id            = ! empty( $args['id'] ) ? $args['id'] : false;
		$this->name          = ! empty( $args['name'] ) ? $args['name'] : false;
		$this->query         = ! empty( $args['query'] ) ? $args['query'] : false;
		$this->dynamic_query = ! empty( $args['dynamic_query'] ) ? $args['dynamic_query'] : false;

		$this->maybe_add_instance_fields_to_ui();

	}

	/**
	 * Returns items per page.
	 * Each query which allows pagintaion should implement own method to gettings items per page.
	 *
	 * @param  integer $default [description]
	 * @return [type]           [description]
	 */
	public function get_items_per_page() {
		return 0;
	}

	/**
	 * Returns query cache
	 *
	 * @param  [type] $key [description]
	 * @return [type]      [description]
	 */
	public function get_query_hash( $key = null ) {

		$this->setup_query();

		$prefix = 'jet_query_';

		if ( $key ) {
			$prefix .= $key . '_';
		}

		return $prefix . md5( json_encode( $this->final_query ) );

	}

	/**
	 * Get cached data
	 *
	 * @param  [type] $key [description]
	 * @return [type]      [description]
	 */
	public function get_cached_data( $key = null ) {
		return wp_cache_get( $this->get_query_hash( $key ) );
	}

	/**
	 * Update cache for the current query instance
	 *
	 * @param  [type] $data [description]
	 * @param  [type] $key  [description]
	 * @return [type]       [description]
	 */
	public function update_query_cache( $data = null, $key = null ) {
		return wp_cache_set( $this->get_query_hash( $key ), $data );
	}

	/**
	 * Check if current query has items
	 *
	 * @return boolean [description]
	 */
	public function has_items() {
		$items = $this->get_items();
		return ! empty( $items );
	}

	/**
	 * Add current instance fields into the UI elements
	 *
	 * @param [type] $groups [description]
	 */
	public function maybe_add_instance_fields_to_ui() {

		$fields = $this->get_instance_fields();

		if ( empty( $fields ) ) {
			return;
		}

		add_filter(
			'jet-engine/listing/data/object-fields-groups',
			array( $this, 'add_source_fields' )
		);

	}

	/**
	 * Add source fields into the UI elements
	 *
	 * @param [type] $groups [description]
	 */
	public function add_source_fields( $groups ) {

		$groups[] = array(
			'label'   => __( 'Query:', 'jet-engine' ) . ' ' . $this->get_instance_name(),
			'options' => $this->get_instance_fields(),
		);

		return $groups;

	}

	/**
	 * Get fields list are available for the current instance of this query
	 *
	 * @return [type] [description]
	 */
	public function get_instance_fields() {
		return array();
	}

	/**
	 * Returns query instance name to use in the UI
	 * @return [type] [description]
	 */
	public function get_instance_name() {
		return $this->name;
	}

	/**
	 * Preapre query
	 *
	 * @return [type] [description]
	 */
	public function setup_query() {

		if ( null !== $this->final_query ) {

			/**
			 * Before get query items
			 */
			//do_action( 'jet-engine/query-builder/query/after-query-setup', $this );
			return;
		}

		$processed_dynamics = array();

		$this->final_query = $this->query;

		foreach ( $this->final_query as $key => $value ) {
			if ( is_array( $value ) ) {
				$reset = false;

				foreach ( $value as $inner_key => $inner_value ) {

					if ( ! $reset && is_array( $inner_value ) && ! empty( $inner_value['_id'] ) ) {
						$reset = true;
						$this->final_query[ $key ] = array();
					}

					if ( $reset ) {

						if ( isset( $this->dynamic_query[ $key ][ $inner_value['_id'] ] ) ) {

							$inner_value = array_merge( $inner_value, $this->apply_macros( $this->dynamic_query[ $key ][ $inner_value['_id'] ] ) );

							if ( ! in_array( $key, $processed_dynamics ) ) {
								$processed_dynamics[] = $key;
							}

						}

						$this->final_query[ $key ][] = $inner_value;
					}
				}
			}
		}

		if ( ! empty( $this->dynamic_query ) ) {
			foreach ( $this->dynamic_query as $key => $value ) {
				if ( ! in_array( $key, $processed_dynamics ) ) {
					if ( ! empty( $value ) ) {
						$this->final_query[ $key ] = $this->apply_macros( $value );
					}
				}
			}
		}

		$explode = $this->get_args_to_explode();

		if ( ! empty( $explode ) ) {
			foreach ( $this->final_query as $key => $value ) {
				if ( in_array( $key, $explode ) ) {
					$this->final_query[ $key ] = $this->explode_string( $value );
				}
			}
		}

		/**
		 * Before get query items
		 */
		do_action( 'jet-engine/query-builder/query/after-query-setup', $this );

	}

	/**
	 * Apply macros by passed string
	 *
	 * @param  [type] $val [description]
	 * @return [type]      [description]
	 */
	public function apply_macros( $val ) {

		if ( is_array( $val ) ) {

			$result = array();

			foreach ( $val as $key => $value ) {
				if ( ! empty( $value ) ) {
					$result[ $key ] = jet_engine()->listings->macros->do_macros( $value );
				}
			}

			return $result;

		} else {
			return jet_engine()->listings->macros->do_macros( $val );
		}

	}

	/**
	 * Returns query instance id
	 * @return [type] [description]
	 */
	public function get_instance_id() {
		return '_query_' . $this->id;
	}

	/**
	 * Returns queried items array
	 *
	 * @return array
	 */
	public function get_items() {

		$cached = $this->get_cached_data();

		if ( false !== $cached ) {
			/**
			 * Before get query items
			 */
			do_action( 'jet-engine/query-builder/query/before-get-items', $this, true );

			return $cached;
		}

		$this->setup_query();

		/**
		 * Before get query items
		 */
		do_action( 'jet-engine/query-builder/query/before-get-items', $this, false );

		$items = $this->_get_items();

		$this->update_query_cache( $items );

		return $items;

	}

	/**
	 * Array of arguments where string should be exploded into array
	 * Format:
	 * array(
	 * 	'post__in',
	 * 	'post__not_in',
	 * )
	 * @return [type] [description]
	 */
	public function get_args_to_explode() {
		return array();
	}

	public function explode_string( $value ) {

		if ( $value && ! is_array( $value ) && ( false !== strpos( $value, ',' ) ) ) {
			$value = str_replace( ', ', ',', $value );
			$value = explode( ',', $value );
			$value = array_map( 'trim', $value );
		}

		if ( $value && ! is_array( $value ) ) {
			$value = array( $value );
		}

		return $value;

	}

	/**
	 * Returns queries items
	 *
	 * @return [type] [description]
	 */
	abstract public function _get_items();

	/**
	 * Returns total found items count
	 *
	 * @return [type] [description]
	 */
	abstract public function get_items_total_count();

	/**
	 * Returns queried items count per page
	 *
	 * @return [type] [description]
	 */
	abstract public function get_items_page_count();

	/**
	 * Returns queried items pages count
	 *
	 * @return [type] [description]
	 */
	abstract public function get_items_pages_count();

	/**
	 * Returns currently queried items page
	 *
	 * @return [type] [description]
	 */
	abstract public function get_current_items_page();

	/**
	 * Set filtered prop in specific for current query type way
	 *
	 * @param string $prop  [description]
	 * @param [type] $value [description]
	 */
	abstract public function set_filtered_prop( $prop = '', $value = null );

}
