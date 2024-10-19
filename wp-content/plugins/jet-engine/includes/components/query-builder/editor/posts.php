<?php
namespace Jet_Engine\Query_Builder\Query_Editor;

use Jet_Engine\Query_Builder\Manager;

class Posts_Query extends Base_Query {

	/**
	 * Qery type ID
	 */
	public function get_id() {
		return 'posts';
	}

	/**
	 * Qery type name
	 */
	public function get_name() {
		return __( 'Posts Query', 'jet-engine' );
	}

	/**
	 * Returns Vue component name for the Query editor for the current type.
	 * I
	 * @return [type] [description]
	 */
	public function editor_component_name() {
		return 'jet-posts-query';
	}

	/**
	 * Returns Vue component template for the Query editor for the current type.
	 * 
	 * @return [type] [description]
	 */
	public function editor_component_data() {
		return apply_filters( 'jet-engine/query-builder/types/posts-query/data', array(
			'posts_statuses' => \Jet_Engine_Tools::get_post_statuses_for_js(),
		) );
	}

	/**
	 * Returns Vue component template for the Query editor for the current type.
	 * I
	 * @return [type] [description]
	 */
	public function editor_component_template() {
		ob_start();
		include Manager::instance()->component_path( 'templates/admin/types/posts.php' );
		return ob_get_clean();
	}

	/**
	 * Returns Vue component template for the Query editor for the current type.
	 * I
	 * @return [type] [description]
	 */
	public function editor_component_file() {
		return Manager::instance()->component_url( 'assets/js/admin/types/posts.js' );
	}

}
