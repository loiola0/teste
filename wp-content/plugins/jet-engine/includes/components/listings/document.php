<?php
/**
 * Listing document class
 *
 * @package   package_name
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Define Jet_Engine_Listings_Document class
 */
class Jet_Engine_Listings_Document {

	private $settings = array();
	private $main_id = null;

	/**
	 * Setup listing
	 * @param array $settings [description]
	 */
	public function __construct( $settings = array(), $id = null ) {
		$this->settings = $settings;
		$this->main_id  = $id;
	}

	/**
	 * Returns listing ID
	 * @return [type] [description]
	 */
	public function get_main_id() {
		return $this->main_id;
	}

	/**
	 * Returns listing settings
	 *
	 * @param  string $setting [description]
	 * @return [type]          [description]
	 */
	public function get_settings( $setting = '' ) {

		if ( empty( $this->settings ) ) {
			return;
		}

		if ( empty( $setting ) ) {
			return $this->settings;
		} else {
			return isset( $this->settings[ $setting ] ) ? $this->settings[ $setting ] : false;
		}

	}

}
