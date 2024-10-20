<?php
namespace Jet_Engine\Modules\Custom_Content_Types\Query_Builder;

use Jet_Engine\Modules\Custom_Content_Types\Module;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Manager {

	/**
	 * Instance.
	 *
	 * Holds query builder instance.
	 *
	 * @access public
	 * @static
	 *
	 * @var Plugin
	 */
	public static $instance = null;

	public $slug = 'custom-content-type';

	/**
	 * Instance.
	 *
	 * Ensures only one instance of the plugin class is loaded or can be loaded.
	 *
	 * @access public
	 * @static
	 *
	 * @return Plugin An instance of the class.
	 */
	public static function instance() {

		if ( is_null( self::$instance ) ) {

			self::$instance = new self();

		}

		return self::$instance;

	}

	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'jet-engine/query-builder/query-editor/register', array( $this, 'register_editor_component' ) );
		add_action( 'jet-engine/query-builder/queries/register', array( $this, 'register_query' ) );
	}

	/**
	 * Register editor componenet for the query builder
	 *
	 * @param  [type] $manager [description]
	 * @return [type]          [description]
	 */
	public function register_editor_component( $manager ) {
		require_once Module::instance()->module_path( 'query-builder/editor.php' );
		$manager->register_type( new CCT_Query_Editor() );
	}

	/**
	 * Regsiter query class
	 *
	 * @param  [type] $manager [description]
	 * @return [type]          [description]
	 */
	public function register_query( $manager ) {

		require_once Module::instance()->module_path( 'query-builder/query.php' );
		$type  = $this->slug;
		$class = __NAMESPACE__ . '\CCT_Query';

		$manager::register_query( $type, $class );

	}

}
