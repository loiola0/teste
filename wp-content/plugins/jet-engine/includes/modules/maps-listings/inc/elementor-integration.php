<?php
namespace Jet_Engine\Modules\Maps_Listings;

class Elementor_Integration {

	/**
	 * Constructor for the class
	 */
	public function __construct() {
		add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_widgets' ), 99 );
		add_action( 'jet-engine/listings/preview-scripts', array( $this, 'preview_scripts' ) );

		add_action( 'jet-engine/elementor-views/dynamic-tags/register', array( $this, 'register_dynamic_tags' ) );
	}

	/**
	 * Preview scripts
	 *
	 * @return void
	 */
	public function preview_scripts() {
		wp_enqueue_script( 'jet-markerclustererplus' );
		wp_enqueue_script( 'jet-maps-listings' );
	}

	/**
	 * Register widgets
	 */
	public function register_widgets( $widgets_manager ) {

		require jet_engine()->modules->modules_path( 'maps-listings/inc/widgets/maps-listings-widget.php' );
		$widgets_manager->register_widget_type( new Maps_Listings_Widget() );

	}

	/**
	 * Register dynamic tags
	 *
	 * @param $tags_module
	 */
	public function register_dynamic_tags( $tags_module ) {

		require_once jet_engine()->modules->modules_path( 'maps-listings/inc/dynamic-tags/open-map-popup.php' );

		$tags_module->register_tag( new Dynamic_Tags\Open_Map_Popup() );

	}

}
