<?php
namespace Jet_Engine\Query_Builder\Query_Editor;

use Jet_Engine\Query_Builder\Manager;

class SQL_Query extends Base_Query {

	/**
	 * Qery type ID
	 */
	public function get_id() {
		return 'sql';
	}

	/**
	 * Qery type name
	 */
	public function get_name() {
		return __( 'SQL Query', 'jet-engine' );
	}

	/**
	 * Returns Vue component name for the Query editor for the current type.
	 * I
	 * @return [type] [description]
	 */
	public function editor_component_name() {
		return 'jet-sql-query';
	}

	/**
	 * Returns Vue component template for the Query editor for the current type.
	 * I
	 * @return [type] [description]
	 */
	public function editor_component_data() {

		global $wpdb;

		$prefix = $wpdb->prefix;
		$dbname = $wpdb->dbname;

		$all_tables = $wpdb->get_results( "SHOW TABLES LIKE '$prefix%'", ARRAY_N );
		$all_tables = array_map( function( $item ) use ( $prefix ) {
			return preg_replace( '/' . $prefix . '/', '', $item, 1 );
		}, $all_tables );

		$all_columns = $wpdb->get_results( "SELECT TABLE_NAME, COLUMN_NAME FROM information_schema.columns WHERE table_schema = '$dbname'", ARRAY_N );

		$tables_list  = array();
		$columns_list = array();

		foreach ( $all_tables as $table ) {
			$tables_list[] = array(
				'value' => $table[0],
				'label' => $table[0],
			);
		}

		foreach ( $all_columns as $col_data ) {
			$table = preg_replace( '/' . $prefix . '/', '', $col_data[0], 1 );

			if ( ! isset( $columns_list[ $table ] ) ) {
				$columns_list[ $table ] = array();
			}

			$columns_list[ $table ][] = array(
				'value' => $col_data[1],
				'label' => $col_data[1],
			);

		}

		return apply_filters( 'jet-engine/query-builder/types/sql-query/data', array(
			'tables'  => $tables_list,
			'columns' => $columns_list,
		) );

	}

	/**
	 * Returns Vue component template for the Query editor for the current type.
	 * I
	 * @return [type] [description]
	 */
	public function editor_component_template() {
		ob_start();
		include Manager::instance()->component_path( 'templates/admin/types/sql.php' );
		return ob_get_clean();
	}

	/**
	 * Returns Vue component template for the Query editor for the current type.
	 * I
	 * @return [type] [description]
	 */
	public function editor_component_file() {
		return Manager::instance()->component_url( 'assets/js/admin/types/sql.js' );
	}

	public function get_default_columns( $args ) {
		$tables = array();

		if ( ! empty( $args['table'] ) ) {
			$tables[] = $args['table'];
		}

		$result = array();

		if ( ! defined( 'DB_NAME' ) ) {
			return $result;
		}

		$db_name = DB_NAME;

		if ( ! empty( $tables ) ) {

			global $wpdb;

			$prepared_tables = array();

			foreach ( $tables as $table ) {
				$prepared_tables[] = sprintf( '\'%1$s%2$s\'', $wpdb->prefix, $table );
			}

			$prepared_tables = implode( ', ', $prepared_tables );

			$all_columns = $wpdb->get_results( "SELECT TABLE_NAME, COLUMN_NAME FROM information_schema.columns WHERE table_schema = '$db_name' AND table_name IN ( $prepared_tables );", ARRAY_N );

			foreach ( $all_columns as $col ) {
				$result[] = $col[1];
			}
		}

		return $result;

	}

	public function update_fields_list_for_query( $request ) {

		if ( ! empty( $request['args']['query_type'] ) && $this->get_id() === $request['args']['query_type'] ) {
			$request['args']['sql']['default_columns'] = $this->get_default_columns( $request['args']['sql'] );
		}

		return $request;
	}

	/**
	 * Constructor for the class
	 */
	public function __construct() {
		add_filter( 'jet-engine/query-builder/edit-query/request', array( $this, 'update_fields_list_for_query' ) );
	}

}
