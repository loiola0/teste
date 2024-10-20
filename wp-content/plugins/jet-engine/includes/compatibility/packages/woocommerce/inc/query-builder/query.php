<?php

namespace Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package\Query_Builder;

class WC_Product_Query extends \Jet_Engine\Query_Builder\Queries\Base_Query {

	use \Jet_Engine\Query_Builder\Queries\Traits\Meta_Query_Trait;

	public $current_wc_query = null;

	/**
	 * Returns queries items
	 *
	 * @return array|object
	 */
	public function _get_items() {

		$query      = $this->get_current_wc_query();
		$query_vars = $query->get_query_vars();

		$result = [];

		if ( ! $query ) {
			return $result;
		}

		if ( $query_vars['paginate'] ) {
			$result = $query->get_products()->products;
		} else {
			$result = $query->get_products();
		}

		return $result;

	}

	/**
	 * Returns `WC_Product_Query`
	 *
	 * @return \WC_Product_Query|null
	 */
	public function get_current_wc_query() {

		if ( null === $this->final_query ) {
			$this->setup_query();
		}

		if ( null !== $this->current_wc_query ) {
			return $this->current_wc_query;
		}

		$args = $this->final_query;

		if ( empty( $args['type'] ) ) {
			$args['type'] = array_merge( array_keys( wc_get_product_types() ) );
		}

		if ( empty( $args['limit'] ) ) {
			$args['limit'] = $this->get_default_wc_product_per_page();;
		}

		if ( ! empty( $args['average_rating'] ) ) {
			$args['average_rating'] = number_format( $args['average_rating'], 2 );
		}

		if ( ! empty( $args['specific_query'] ) ) {
			$row = $args['specific_query'];

			foreach ( $row as $row_query ) {
				$args[ $row_query['feature'] ] = $row_query['status'];
			}
		} elseif ( isset( $args['specific_query'] ) ) {
			unset( $args['specific_query'] );
		}

		if ( ! empty( $args['date_query'] ) ) {
			$row    = $args['date_query'];
			$format = 'Y-m-d';

			foreach ( $row as $row_query ) {
				$compare_date = date( $format, strtotime( $row_query['year'] . '-' . $row_query['month'] . '-' . $row_query['day'] ) );
				$date_before  = date( $format, strtotime( $row_query['before'] ) );
				$date_after   = date( $format, strtotime( $row_query['after'] ) );
				$compare_sign = $row_query['compare'];

				switch ( $compare_sign ) {
					case '=':
						$args[ $row_query['column'] ] = $compare_date;
						break;

					case '>':
					case '>=':
					case '<':
					case '<=':
						$args[ $row_query['column'] ] = $compare_sign . $compare_date;
						break;

					case 'BETWEEN':
						$args[ $row_query['column'] ] = $date_after . '...' . $date_before;
						break;

					default:
						break;
				}
			}
		} elseif ( isset( $args['date_query'] ) ) {
			unset( $args['date_query'] );
		}

		$this->current_wc_query = new \WC_Product_Query( $args );

		return $this->current_wc_query;

	}

	/**
	 * Returns total found items count
	 *
	 * @return mixed
	 */
	public function get_items_total_count() {

		$cached     = $this->get_cached_data( 'count' );
		$query      = $this->get_current_wc_query();
		$query_vars = $query->get_query_vars();

		if ( false !== $cached || ! $query ) {
			return $cached;
		}

		if ( $query_vars['paginate'] ) {
			$result = $query->get_products()->total;
		} else {
			$result = count( $query->get_products() );
		}

		$this->update_query_cache( $result, 'count' );

		return $result;

	}

	/**
	 * Return current listing grid page
	 *
	 * @return false|float|int
	 */
	public function get_current_items_page() {

		$query      = $this->get_current_wc_query();
		$query_vars = $query->get_query_vars();
		$page       = ! empty( $this->final_query['paged'] ) ? $this->final_query['paged'] : false;

		if ( ! $page && ! empty( $this->final_query['page'] ) ) {
			$page = $this->final_query['page'];
		}

		if ( ! $page && ! empty( $query_vars['paged'] ) ) {
			$page = $query_vars['paged'];
		}

		if ( ! $page ) {
			$page = 1;
		}

		return $page;

	}

	/**
	 * Returns count of the items visible per single listing grid loop/page
	 *
	 * @return int
	 */
	public function get_items_per_page() {

		$this->setup_query();

		$query      = $this->get_current_wc_query();
		$query_vars = $query->get_query_vars();

		if ( ! empty( $query_vars['limit'] ) ) {
			$limit = $query_vars['limit'];
		} else {
			$limit = $this->get_default_wc_product_per_page();
		}

		return $limit;

	}

	/**
	 * Returns queried items count per page
	 *
	 * @return mixed
	 */
	public function get_items_page_count() {

		$result   = $this->get_items_total_count();
		$per_page = $this->get_items_per_page();

		if ( $per_page < $result ) {
			$page  = $this->get_current_items_page();
			$pages = $this->get_items_pages_count();

			if ( $page < $pages ) {
				$result = $per_page;
			} elseif ( $page == $pages ) {
				$offset = ! empty( $this->final_query['offset'] ) ? absint( $this->final_query['offset'] ) : 0;

				if ( $result % $per_page > 0 ) {
					$result = ( $result % $per_page ) - $offset;
				} else {
					$result = $per_page - $offset;
				}
			}
		}

		return $result;

	}

	/**
	 * Returns queried items pages count
	 *
	 * @return false|float|int
	 */
	public function get_items_pages_count() {

		$per_page = $this->get_items_per_page();
		$total    = $this->get_items_total_count();

		if ( ! $per_page || ! $total ) {
			return 1;
		} else {
			return ceil( $total / $per_page );
		}

	}

	/**
	 * Set filtered prop in specific for current query type way
	 *
	 * @param string $prop
	 * @param null   $value
	 */
	public function set_filtered_prop( $prop = '', $value = null ) {
		switch ( $prop ) {
			case '_page':
				$this->final_query['page'] = $value;
				break;

			case 'orderby':
			case 'order':
				$this->set_filtered_order( $prop, $value );
				break;

			case 'meta_query':
				$this->replace_meta_query_row( $value );
				break;

			default:
				$this->final_query[ $prop ] = $value;
				break;
		}
	}

	/**
	 * Set filtering order for current query type way
	 *
	 * @param $key
	 * @param $value
	 */
	public function set_filtered_order( $key, $value ) {

		if ( empty( $this->final_query['orderby'] ) || ! isset( $this->final_query['orderby']['custom'] ) ) {
			$this->final_query['orderby'] = [ 'custom' => [] ];
		}

		$this->final_query['orderby']['custom'][ $key ] = $value;

	}

	/**
	 * Returns default WC_Query product per page.
	 *
	 * @return float|int
	 */
	public function get_default_wc_product_per_page() {
		return wc_get_default_products_per_row() * wc_get_default_product_rows_per_page();
	}

	/**
	 * Array of arguments where string should be exploded into array
	 *
	 * @return string[]
	 */
	public function get_args_to_explode() {
		return [
			'include',
			'exclude',
			'parent_exclude',
			'shipping_class',
		];
	}

}
