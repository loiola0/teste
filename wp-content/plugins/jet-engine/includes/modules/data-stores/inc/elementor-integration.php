<?php
namespace Jet_Engine\Modules\Data_Stores;

class Elementor_Integration {

	public function __construct() {
		add_filter( 'jet-engine/elementor-view/dynamic-link/generel-options', array( $this, 'register_dynamic_link_option' ) );
		add_action( 'jet-engine/listings/dynamic-link/source-controls', array( $this, 'register_dynamic_link_controls' ) );

		add_action( 'jet-engine/listing/after-posts-query-fields', array( $this, 'register_listing_controls' ) );
		add_action( 'jet-engine/elementor-views/dynamic-tags/register', array( $this, 'register_dynamic_tags' ) );

	}

	public function register_dynamic_tags( $tags_module ) {

		require_once jet_engine()->modules->modules_path( 'data-stores/inc/dynamic-tags/post-count.php' );
		require_once jet_engine()->modules->modules_path( 'data-stores/inc/dynamic-tags/store-count.php' );
		require_once jet_engine()->modules->modules_path( 'data-stores/inc/dynamic-tags/get-store.php' );

		$tags_module->register_tag( new Dynamic_Tags\Post_Count() );
		$tags_module->register_tag( new Dynamic_Tags\Store_Count() );
		$tags_module->register_tag( new Dynamic_Tags\Get_Store() );

	}

	/**
	 * Add required options to the dynamic link widget
	 */
	public function register_dynamic_link_option( $options ) {

		$options['add_to_store'] = __( 'Add to store', 'jet-engine' );
		$options['remove_from_store'] = __( 'Remove from store', 'jet-engine' );

		return $options;

	}

	public function get_store_options( $only_countable = false ) {

		$stores = Module::instance()->stores->get_stores();

		$options = array(
			'' => __( 'Select...', 'jet-engine' ),
		);

		foreach ( $stores as $store ) {

			if ( $only_countable && $store->can_count_posts() ) {
				$options[ $store->get_slug() ] = $store->get_name();
			} elseif ( ! $only_countable ) {
				$options[ $store->get_slug() ] = $store->get_name();
			}

		}

		return $options;

	}

	public function register_listing_controls( $posts_query_repeater ) {

		$posts_query_repeater->add_control(
			'posts_from_data_store',
			array(
				'label'     => __( 'Get posts from store', 'jet-engine' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => '',
				'groups'    => $this->get_store_options(),
				'condition'   => array(
					'type'     => 'posts_params',
				),
			)
		);

	}

	/**
	 * Register controls for store-related source
	 */
	public function register_dynamic_link_controls( $widget ) {

		$widget->add_control(
			'dynamic_link_store',
			array(
				'label'     => __( 'Select store', 'jet-engine' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => '',
				'groups'    => $this->get_store_options(),
				'condition' => array(
					'dynamic_link_source' => array( 'add_to_store', 'remove_from_store' ),
				),
			)
		);

		if ( function_exists( 'jet_popup' ) ) {
			$widget->add_control(
				'dynamic_link_trigger_popup',
				array(
					'type'        => \Elementor\Controls_Manager::SWITCHER,
					'label'       => __( 'Open popup on success', 'jet-engine' ),
					'description' => __( 'Open selected popup from JetPopup after post succesfully added to store. Popup should be selected in the <b>Advanced Tab > JetPopup</b> section, <b>Trigger Type</b> must be set to <b>None</b>', 'jet-engine' ),
					'condition' => array(
						'dynamic_link_source' => array( 'add_to_store' ),
					),
				)
			);
		}

		$widget->add_control(
			'dynamic_link_synch_grid',
			array(
				'type'        => \Elementor\Controls_Manager::SWITCHER,
				'label'       => __( 'Reload listing grid on success', 'jet-engine' ),
				'description' => __( 'You can use this option to reload listing grid with current Store posts on success', 'jet-engine' ),
				'condition' => array(
					'dynamic_link_source' => array( 'add_to_store' ),
				),
			)
		);

		$widget->add_control(
			'dynamic_link_synch_grid_id',
			array(
				'label'       => __( 'Listing grid ID', 'jet-engine' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
				'description' => __( 'Here you need to set listing ID to reload. The same ID must be set in the Advanced settings of selected listing', 'jet-engine' ),
				'condition'   => array(
					'dynamic_link_source'     => array( 'add_to_store' ),
					'dynamic_link_synch_grid' => 'yes',
				),
			)
		);

		$widget->add_control(
			'added_to_store_text',
			array(
				'label'       => __( 'Added to store text', 'jet-engine' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
				'condition'   => array(
					'dynamic_link_source' => array( 'add_to_store' ),
				),
			)
		);

		$widget->add_control(
			'added_to_store_url',
			array(
				'label'       => __( 'Added to store URL', 'jet-engine' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
				'condition'   => array(
					'dynamic_link_source' => array( 'add_to_store' ),
				),
			)
		);

		$widget->add_control(
			'added_to_store_icon',
			array(
				'label'            => __( 'Added to store icon', 'jet-engine' ),
				'type'             => \Elementor\Controls_Manager::ICONS,
				'label_block'      => true,
				'condition'   => array(
					'dynamic_link_source' => array( 'add_to_store' ),
				),
				'separator'        => 'after',

			)
		);

		$widget->add_control(
			'remove_post_from_listing',
			array(
				'type'        => \Elementor\Controls_Manager::SWITCHER,
				'label'       => __( 'Remove post from current listing', 'jet-engine' ),
				'description' => __( 'Check this is you want to remove current post from current listing grid after removing post from store', 'jet-engine' ),
				'default'     => 'yes',
				'condition'   => array(
					'dynamic_link_source' => array( 'remove_from_store' ),
				),
				'separator'   => 'after',

			)
		);

	}

}
