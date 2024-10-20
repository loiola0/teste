<?php

namespace Jet_Engine\Query_Builder\Pages;

use Jet_Engine\Query_Builder\Manager;
use Jet_Engine\Query_Builder\Dynamic_Args;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Edit extends \Jet_Engine_CPT_Page_Base {

	/**
	 * Page slug
	 *
	 * @return string
	 */
	public function get_slug() {
		if ( $this->item_id() ) {
			return 'edit';
		} else {
			return 'add';
		}
	}

	/**
	 * Page name
	 *
	 * @return string
	 */
	public function get_name() {
		if ( $this->item_id() ) {
			return esc_html__( 'Edit Query', 'jet-engine' );
		} else {
			return esc_html__( 'Add New Query', 'jet-engine' );
		}
	}

	/**
	 * Returns currently requested items ID.
	 * If this funciton returns an empty result - this is add new item page
	 *
	 * @return [type] [description]
	 */
	public function item_id() {
		return isset( $_GET['id'] ) ? esc_attr( $_GET['id'] ) : false;
	}

	/**
	 * Register add controls
	 *
	 * @return [type] [description]
	 */
	public function page_specific_assets() {

		timer_start();

		$module_data = jet_engine()->framework->get_included_module_data( 'cherry-x-vue-ui.php' );

		$ui = new \CX_Vue_UI( $module_data );

		$ui->enqueue_assets();

		wp_enqueue_script(
			'jet-engine-query-delete-dialog',
			Manager::instance()->component_url( 'assets/js/admin/delete-dialog.js' ),
			array( 'cx-vue-ui', 'wp-api-fetch', ),
			jet_engine()->get_version(),
			true
		);

		wp_localize_script(
			'jet-engine-query-delete-dialog',
			'JetEngineQueryDeleteDialog',
			array(
				'api_path' => jet_engine()->api->get_route( 'delete-query' ),
				'redirect' => $this->manager->get_page_link( 'list' ),
			)
		);

		wp_enqueue_style(
			'jet-engine-query-dynamic-args',
			Manager::instance()->component_url( 'assets/css/query-builder.css' ),
			array(),
			jet_engine()->get_version()
		);

		wp_enqueue_script(
			'jet-engine-query-dynamic-args',
			Manager::instance()->component_url( 'assets/js/admin/dynamic-args.js' ),
			array( 'cx-vue-ui' ),
			jet_engine()->get_version(),
			true
		);

		wp_localize_script(
			'jet-engine-query-dynamic-args',
			'JetEngineQueryDynamicArgs',
			array(
				'macros_list' => $this->get_macros_for_editor(),
			)
		);

		wp_enqueue_script(
			'jet-engine-query-mixins',
			Manager::instance()->component_url( 'assets/js/admin/mixins.js' ),
			array(),
			jet_engine()->get_version(),
			true
		);

		do_action( 'jet-engine/query-builder/editor/before-enqueue-scripts' );

		wp_enqueue_script(
			'jet-engine-query-edit',
			Manager::instance()->component_url( 'assets/js/admin/edit.js' ),
			array( 'cx-vue-ui', 'wp-api-fetch' ),
			jet_engine()->get_version(),
			true
		);

		do_action( 'jet-engine/query-builder/editor/after-enqueue-scripts' );

		$id = $this->item_id();

		if ( $id ) {
			$button_label = __( 'Update Query', 'jet-engine' );
			$redirect     = false;
		} else {
			$button_label = __( 'Add Query', 'jet-engine' );
			$redirect     = $this->manager->get_edit_item_link( '%id%' );
		}

		wp_localize_script(
			'jet-engine-query-edit',
			'JetEngineQueryConfig',
			$this->manager->get_admin_page_config( array(
				'api_path_edit'           => jet_engine()->api->get_route( $this->get_slug() . '-query' ),
				'api_path_search_preview' => jet_engine()->api->get_route( 'search-query-preview' ),
				'api_path_update_preview' => jet_engine()->api->get_route( 'update-query-preview' ),
				'item_id'                 => $id,
				'edit_button_label'       => $button_label,
				'redirect'                => $redirect,
				'post_types'              => \Jet_Engine_Tools::get_post_types_for_js(),
				'operators_list'          => \Jet_Engine_Tools::operators_list(),
				'data_types'              => \Jet_Engine_Tools::data_types_list(),
				'help_links'              => array(
					array(
						'url'   => 'https://crocoblock.com/knowledge-base/article-category/jetengine-query-builder/?utm_source=jetengine&amp;utm_medium=query-builder&amp;utm_campaign=need-help',
						'label' => __( 'Query Builder knowledge base', 'jet-engine' ),
					),
				),
			) )
		);

		add_action( 'admin_footer', array( $this, 'add_page_template' ) );

	}

	public function get_macros_for_editor() {

		$res = array();

		foreach ( jet_engine()->listings->macros->get_all() as $macros_id => $data ) {

			$macros_data = array(
				'id' => $macros_id,
			);

			if ( ! is_array( $data ) || empty( $data['label'] ) ) {
				$macros_data['name'] = $macros_id;
			} elseif ( ! empty( $data['label'] ) ) {
				$macros_data['name'] = $data['label'];
			}

			if ( is_array( $data ) && ! empty( $data['args'] ) ) {
				$macros_data['controls'] = $data['args'];
			}

			$res[] = $macros_data;

		}

		usort( $res, function ( $a, $b ) {
			return strcmp( $a['name'], $b['name'] );
		} );

		return $res;

	}

	/**
	 * Print add/edit page template
	 */
	public function add_page_template() {

		ob_start();
		include Manager::instance()->component_path( 'templates/admin/edit.php' );
		$content = ob_get_clean();
		printf( '<script type="text/x-template" id="jet-query-form">%s</script>', $content );

		ob_start();
		include Manager::instance()->component_path( 'templates/admin/delete-dialog.php' );
		$content = ob_get_clean();
		printf( '<script type="text/x-template" id="jet-query-delete-dialog">%s</script>', $content );

		ob_start();
		include Manager::instance()->component_path( 'templates/admin/dynamic-args.php' );
		$content = ob_get_clean();
		printf( '<script type="text/x-template" id="jet-query-dynamic-args">%s</script>', $content );

	}

	/**
	 * Renderer callback
	 *
	 * @return void
	 */
	public function render_page() {
		?>
		<br>
		<div id="jet_query_form"></div>
		<?php
	}

}
