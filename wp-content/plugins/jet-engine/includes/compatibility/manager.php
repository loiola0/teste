<?php
/**
 * Class description
 *
 * @package   package_name
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Compatibility' ) ) {

	/**
	 * Define Jet_Engine_Compatibility class
	 */
	class Jet_Engine_Compatibility {

		/**
		 * Constructor for the class
		 */
		function __construct() {
			add_action( 'init', array( $this, 'load_compat_packages' ) );
		}

		/**
		 * Load compatibility packages
		 *
		 * @return void
		 */
		public function load_compat_packages() {

			$whitelist = array(
				'woocommerce/woocommerce.php' => array(
					'cb'   => 'class_exists',
					'args' => 'WooCommerce',
				),
				'acf.php' => array(
					'cb'   => 'class_exists',
					'args' => 'acf',
				),
				'meta-box.php' => array(
					'cb'   => 'class_exists',
					'args' => 'RWMB_Loader',
				),
				'elementor-pro.php' => array(
					'cb'   => 'defined',
					'args' => 'ELEMENTOR_PRO_VERSION',
				),
				'jet-theme-core.php' => array(
					'cb'   => 'class_exists',
					'args' => 'Jet_Theme_Core',
				),
				'wpml.php' => array(
					'cb'   => 'defined',
					'args' => 'WPML_ST_VERSION',
				),
				'jet-popup.php' => array(
					'cb'   => 'class_exists',
					'args' => 'Jet_Popup',
				),
				'crocoblock-wizard.php' => array(
					'cb'   => 'defined',
					'args' => 'CB_WIZARD_VERSION',
				),
				'jet-smart-filters.php' => array(
					'cb'   => 'class_exists',
					'args' => 'Jet_Smart_Filters',
				),
				'polylang.php' => array(
					'cb'   => 'function_exists',
					'args' => 'PLL',
				),
				'wcfm.php' => array(
					'cb'   => 'defined',
					'args' => 'WCFM_TOKEN',
				),
				'seo.php' => array(
					'cb'   => array( $this, 'defined_seo_plugins' ),
					'args' => false,
				),
			);

			foreach ( $whitelist as $file => $condition ) {
				if ( true === call_user_func( $condition['cb'], $condition['args'] ) ) {
					require jet_engine()->plugin_path( 'includes/compatibility/packages/' . $file );
				}
			}

		}

		public function defined_seo_plugins() {
			return defined( 'RANK_MATH_VERSION' ) || defined( 'WPSEO_VERSION' );
		}

	}

}
