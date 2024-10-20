<?php
/**
 * Meta boxes mamager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_CPT_Meta' ) ) {
	require jet_engine()->plugin_path( 'includes/components/meta-boxes/post.php' );
}

if ( ! class_exists( 'Jet_Engine_CPT_Tax_Meta' ) ) {

	/**
	 * Define Jet_Engine_CPT_Tax_Meta class
	 */
	class Jet_Engine_CPT_Tax_Meta extends Jet_Engine_CPT_Meta {

		public $tax;

		/**
		 * Constructor for the class
		 */
		function __construct( $taxonomy, $meta_box ) {

			$this->tax      = $taxonomy;
			$this->meta_box = $meta_box;

			new Cherry_X_Term_Meta( array(
				'id'         => $this->get_box_id(),
				'tax'        => $taxonomy,
				'builder_cb' => array( $this, 'get_builder_for_meta' ),
				'fields'     => $this->prepare_meta_fields( $meta_box ),
			) );

			add_action( 'admin_enqueue_scripts', array( $this, 'maybe_enqueue_custom_css' ), 0 );
			add_filter( 'cx_term_meta/meta_box/before', array( $this, 'maybe_render_edit_link' ), 20 );

		}

		public function maybe_render_edit_link( $args ) {
			
			if ( $this->edit_link && ! empty( $args['id'] ) && $this->get_box_id() === $args['id'] ) {
				printf( 
					'<a href="%s" class="jet-engine-edit-box-link" target="_blank"><span class="dashicons dashicons-admin-generic"></span></a>', 
					$this->edit_link
				);
			}

		}

		/**
		 * Add wrappers hooks.
		 */
		public function add_wrappers_hooks() {
			add_action( 'cx_term_meta/meta_box/before', array( $this, 'open_meta_wrap' ) );
			add_action( 'cx_term_meta/meta_box/after',  array( $this, 'close_meta_wrap' ) );
		}

		/**
		 * Open meta wrap
		 * @return void
		 */
		public function open_meta_wrap() {
			echo '<div class="jet-engine-meta-wrap jet-engine-term-meta-wrap">';
		}

		public function is_allowed_on_current_admin_hook( $hook ) {

			if ( null !== $this->is_allowed_on_admin_hook ) {
				return $this->is_allowed_on_admin_hook;
			}

			$allowed_hooks = array(
				'edit-tags.php',
				'term.php',
			);

			if ( ! in_array( $hook, $allowed_hooks ) ) {
				$this->is_allowed_on_admin_hook = false;
				return $this->is_allowed_on_admin_hook;
			}

			global $current_screen;

			if ( ! isset( $current_screen->taxonomy ) || $current_screen->taxonomy !== $this->tax ) {
				$this->is_allowed_on_admin_hook = false;
				return $this->is_allowed_on_admin_hook;
			}

			$this->is_allowed_on_admin_hook = true;
			return $this->is_allowed_on_admin_hook;
		}

	}

}
