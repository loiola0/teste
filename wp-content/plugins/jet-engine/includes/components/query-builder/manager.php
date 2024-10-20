<?php
namespace Jet_Engine\Query_Builder;
/**
 * Options pages manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! trait_exists( '\Jet_Engine_Notices_Trait' ) ) {
	require_once jet_engine()->plugin_path( 'includes/traits/notices.php' );
}

/**
 * Define Jet_Engine_Glossaries class
 */
class Manager extends \Jet_Engine_Base_WP_Intance {

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

	/**
	 * Base slug for CPT-related pages
	 * @var string
	 */
	public $page = 'jet-engine-query';

	/**
	 * Action request key
	 *
	 * @var string
	 */
	public $action_key = 'query_action';

	/**
	 * Metaboxes to register
	 *
	 * @var array
	 */
	public $meta_boxes = array();

	/**
	 * Set object type
	 * @var string
	 */
	public $object_type = 'query';

	public $types;
	public $advanced_fields = array();
	public $queries = array();

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
	 * Constructor for the class
	 */
	function __construct() {

		add_action( 'init', array( $this, 'register_instances' ), 11 );

		$this->init_data();

		add_action( 'jet-engine/rest-api/init-endpoints', array( $this, 'init_rest' ) );

		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'add_menu_page' ), 20 );
		}

		if ( ! $this->is_cpt_page() ) {
			return;
		}

		add_action( 'admin_init', array( $this, 'register_pages' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ), 0 );
		add_action( 'admin_init', array( $this, 'handle_actions' ) );

	}

	/**
	 * Init data instance
	 *
	 * @return [type] [description]
	 */
	public function init_data() {

		if ( ! class_exists( '\Jet_Engine_Base_Data' ) ) {
			require_once jet_engine()->plugin_path( 'includes/base/base-data.php' );
		}

		require $this->component_path( 'data.php' );

		$this->data = new Data( $this );

	}

	/**
	 * Initiizlize post type specific API endpoints
	 *
	 * @param  Jet_Engine_REST_API $api_manager API manager instance.
	 * @return void
	 */
	public function init_rest( $api_manager ) {

		require_once $this->component_path( 'rest-api/add-query.php' );
		require_once $this->component_path( 'rest-api/edit-query.php' );
		require_once $this->component_path( 'rest-api/get-query.php' );
		require_once $this->component_path( 'rest-api/delete-query.php' );
		require_once $this->component_path( 'rest-api/get-queries.php' );
		require_once $this->component_path( 'rest-api/search-preview.php' );
		require_once $this->component_path( 'rest-api/update-preview.php' );

		$api_manager->register_endpoint( new Rest\Add_Query() );
		$api_manager->register_endpoint( new Rest\Edit_Query() );
		$api_manager->register_endpoint( new Rest\Get_Query() );
		$api_manager->register_endpoint( new Rest\Delete_Query() );
		$api_manager->register_endpoint( new Rest\Get_Queries() );
		$api_manager->register_endpoint( new Rest\Search_Preview() );
		$api_manager->register_endpoint( new Rest\Update_Preview() );

	}

	/**
	 * Return path to file inside component
	 *
	 * @param  [type] $path_inside_component [description]
	 * @return [type]                        [description]
	 */
	public function component_path( $path_inside_component ) {
		return jet_engine()->plugin_path( 'includes/components/query-builder/' . $path_inside_component );
	}

	/**
	 * Return URL of the file inside component
	 *
	 * @param  [type] $path_inside_component [description]
	 * @return [type]                        [description]
	 */
	public function component_url( $path_inside_component ) {
		return jet_engine()->plugin_url( 'includes/components/query-builder/' . $path_inside_component );
	}

	/**
	 * Register query instances where it required
	 * @return [type] [description]
	 */
	public function register_instances() {

		require $this->component_path( 'query-editor.php' );
		require $this->component_path( 'listings/manager.php' );

		$this->editor   = new Query_Editor();
		$this->listings = new Listings\Manager();

		do_action( 'jet-engine/query-builder/init', $this );

		$this->setup_queries();

		add_action( 'jet-engine/modules/dynamic-visibility/conditions/register', array( $this, 'register_visibility_conditions' ) );
		add_action( 'jet-engine/elementor-views/dynamic-tags/register', array( $this, 'register_dynamic_tags' ) );

	}

	public function register_dynamic_tags( $tags_module ) {
		require_once $this->component_path( 'dynamic-tags/query-count.php' );
		$tags_module->register_tag( new Dynamic_Tags\Query_Count_Tag() );
	}

	public function register_visibility_conditions( $manager ) {
		require $this->component_path( 'conditions/has-items.php' );
		$manager->register_condition( new Conditions\Has_Items() );
	}

	/**
	 * Ensure query factory class is included
	 * @return [type] [description]
	 */
	public function include_factory() {
		if ( ! class_exists( 'Jet_Engine\Query_Builder\Query_Factory' ) ) {
			require $this->component_path( 'query-factory.php' );
		}
	}

	/**
	 * Setup registeed query objects
	 *
	 * @return [type] [description]
	 */
	public function setup_queries() {

		$queries = $this->data->get_items();

		if ( empty( $queries ) ) {
			return;
		}

		$this->include_factory();

		foreach ( $queries as $query ) {
			$factory = new Query_Factory( $query );
			$this->queries[ $query['id'] ] = $factory->get_query();
		}
	}

	public function get_query_by_id( $id ) {
		return isset( $this->queries[ $id ] ) ? $this->queries[ $id ] : false;
	}

	/**
	 * Return admin pages for current instance
	 *
	 * @return array
	 */
	public function get_instance_pages() {

		return array(
			__NAMESPACE__ . '\Pages\Queries_List' => $this->component_path( 'pages/list.php' ),
			__NAMESPACE__ . '\Pages\Edit'         => $this->component_path( 'pages/edit.php' ),
		);

	}

	/**
	 * Returns current menu page title (for JetEngine submenu)
	 * @return [type] [description]
	 */
	public function get_page_title() {
		return __( 'Query Builder', 'jet-engine' );
	}

	/**
	 * Returns current instance slug
	 *
	 * @return [type] [description]
	 */
	public function instance_slug() {
		return 'query';
	}

	/**
	 * Returns queries list for the options
	 *
	 * @return [type] [description]
	 */
	public function get_queries_for_options( $blocks = false ) {

		$items = $this->data->get_items();

		if ( $blocks ) {
			$result = array();
			$result[] = array(
				'value' => '',
				'label' => __( 'Select query...', 'jet-engine' ),
			);
		} else {
			$result = array( '' => __( 'Select query...', 'jet-engine' ) );
		}

		foreach ( $items as $item ) {
			$labels = maybe_unserialize( $item['labels'] );

			if ( $blocks ) {
				$result[] = array(
					'value' => $item['id'],
					'label' => $labels['name'],
				);
			} else {
				$result[ $item['id'] ] = $labels['name'];
			}
		}

		return $result;

	}

	/**
	 * Returns default config for add/edit page
	 *
	 * @param  array  $config [description]
	 * @return [type]         [description]
	 */
	public function get_admin_page_config( $config = array() ) {

		$default_settings = array(
			'type'  => 'text',
			'width' => '100%',
		);

		$default = array(
			'api_path_edit'       => '', // Set individually for apropriate page
			'api_path_get'        => jet_engine()->api->get_route( 'get-query' ),
			'edit_button_label'   => '', // Set individually for apropriate page,
			'item_id'             => false,
			'query_types'         => $this->editor->get_types_for_js(),
			'types_components'    => $this->editor->get_editor_components_map(),
			'post_types'          => \Jet_Engine_Tools::get_post_types_for_js(),
			'taxonomies'          => \Jet_Engine_Tools::get_taxonomies_for_js(),
			'redirect'            => '', // Set individually for apropriate page,
			'general_settings'    => array( 'query_type' => 'post' ),
			'notices'             => array(
				'name'    => __( 'Please, set query name', 'jet-engine' ),
				'success' => __( 'Query updated', 'jet-engine' ),
			),
		);

		return array_merge( $default, $config );

	}

}

Manager::instance();
