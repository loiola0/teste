<?php
namespace Jet_Engine\Query_Builder;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define query factory class
 */
class Query_Factory {

	private $type   = null;
	private $config = array();

	private static $_queries = array();

	public function __construct( $data ) {

		$id   = absint( $data['id'] );
		$args = maybe_unserialize( $data['args'] );
		$type = $args['query_type'];

		if ( ! empty( $data['labels'] ) ) {
			$labels = maybe_unserialize( $data['labels'] );
		} else {
			$labels = array( 'name' => 'Preview' );
		}

		$query_config                  = array();
		$query_config['id']            = $id;
		$query_config['name']          = $labels['name'];
		$query_config['query']         = isset( $args[ $type ] ) ? $args[ $type ] : array();
		$query_config['dynamic_query'] = isset( $args[ '__dynamic_' . $type ] ) ? $args[ '__dynamic_' . $type ] : array();

		$this->type = $type;
		$this->config = $query_config;

		self::ensure_queries();

	}

	public function get_query() {

		$class = $this->get_query_class( $this->type );

		if ( $class && class_exists( $class ) ) {
			return new $class( $this->config );
		}

		return false;

	}

	public static function ensure_queries() {

		if ( empty( self::$_queries ) ) {

			require_once Manager::instance()->component_path( 'queries/traits/meta-query.php' );

			require_once Manager::instance()->component_path( 'queries/base.php' );
			require_once Manager::instance()->component_path( 'queries/sql.php' );
			require_once Manager::instance()->component_path( 'queries/posts.php' );
			require_once Manager::instance()->component_path( 'queries/terms.php' );
			require_once Manager::instance()->component_path( 'queries/users.php' );
			require_once Manager::instance()->component_path( 'queries/comments.php' );

			$defaults = array(
				'sql'      => __NAMESPACE__ . '\Queries\SQL_Query',
				'posts'    => __NAMESPACE__ . '\Queries\Posts_Query',
				'terms'    => __NAMESPACE__ . '\Queries\Terms_Query',
				'users'    => __NAMESPACE__ . '\Queries\Users_Query',
				'comments' => __NAMESPACE__ . '\Queries\Comments_Query',
			);

			foreach ( $defaults as $type => $class ) {
				self::register_query( $type, $class );
			}

			do_action( 'jet-engine/query-builder/queries/register', get_called_class() );

		}

	}

	public static function register_query( $type, $class ) {
		self::$_queries[ $type ] = $class;
	}

	/**
	 * Returns query class name by type
	 *
	 * @param  [type] $type [description]
	 * @return [type]       [description]
	 */
	public function get_query_class( $type ) {
		return isset( self::$_queries[ $type ] ) ? self::$_queries[ $type ] : false;
	}

}
