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

if ( ! class_exists( 'Jet_Engine_Skins_Export' ) ) {

	/**
	 * Define Jet_Engine_Skins_Export class
	 */
	class Jet_Engine_Skins_Export {

		public $nonce = 'jet-engine-export';
		public $id    = null;

		/**
		 * Process skin export
		 */
		public function __construct() {
			add_action( 'admin_footer', array( $this, 'print_templates' ) );
			$this->process_export();
		}

		/**
		 * Export skin
		 *
		 * @return [type] [description]
		 */
		public function process_export() {

			if ( empty( $_GET['export_skin'] ) ) {
				return;
			}

			if ( ! current_user_can( 'export' ) ) {
				return;
			}

			$this->id = null;

			$map = array(
				array(
					'key' => 'post_types',
					'var' => 'post_types',
					'cb'  => array( $this, 'export_post_types' ),
				),
				array(
					'key' => 'taxonomies',
					'var' => 'taxonomies',
					'cb'  => array( $this, 'export_taxonomies' ),
				),
				array(
					'key' => 'listings',
					'var' => 'listing_items',
					'cb'  => array( $this, 'export_listings' ),
				),
				array(
					'key' => 'meta_boxes',
					'var' => 'meta_boxes',
					'cb'  => array( $this, 'export_meta_boxes' ),
				),
				array(
					'key' => 'relations',
					'var' => 'relations',
					'cb'  => array( $this, 'export_relations' ),
				),
				array(
					'key' => 'options_pages',
					'var' => 'options_pages',
					'cb'  => array( $this, 'export_options_pages' ),
				),
				array(
					'key' => 'content',
					'var' => 'sample_content',
					'cb'  => array( $this, 'export_content' ),
				),
			);

			$json = array();

			foreach ( $map as $item ) {
				if ( empty( $_REQUEST[ $item['var'] ] ) ) {
					$json[ $item['key'] ] = array();
				} else {
					$json[ $item['key'] ] = call_user_func( $item['cb'], $_REQUEST[ $item['var'] ] );
				}
			}

			$file     = json_encode( $json );
			$filename = 'skin-export-' . $this->id . '.json';

			Jet_Engine_Tools::file_download( $filename, $file );

		}

		/**
		 * Export post types
		 *
		 * @return void
		 */
		public function export_post_types( $post_types = array() ) {

			if ( ! is_array( $post_types ) ) {
				$post_types = array( $post_types );
			}

			$this->id .= implode( '', $post_types );

			return jet_engine()->cpt->data->get_raw( array( 'id' => $post_types ) );

		}

		/**
		 * Export meta boxes
		 *
		 * @param  array  $meta_boxes meta boxes to export
		 * @return array
		 */
		public function export_meta_boxes( $meta_boxes = array() ) {

			$all_boxes = jet_engine()->meta_boxes->data->get_raw();
			$result    = array();

			$result = array_filter( $all_boxes, function( $box ) use ( $meta_boxes ) {
				return in_array( $box['id'], $meta_boxes );
			} );

			return $result;
		}

		/**
		 * Export relations
		 *
		 * @param  array  $meta_boxes meta boxes to export
		 * @return array
		 */
		public function export_relations( $relations = array() ) {

			$all_items = jet_engine()->relations->data->get_raw();
			$result    = array();

			$result = array_filter( $all_items, function( $item ) use ( $relations ) {
				return in_array( $item['id'], $relations );
			} );

			return $result;
		}

		/**
		 * Export post types
		 *
		 * @return void
		 */
		public function export_taxonomies( $taxonomies = array() ) {

			if ( ! is_array( $taxonomies ) ) {
				$taxonomies = array( $taxonomies );
			}

			$this->id .= implode( '', $taxonomies );

			return jet_engine()->taxonomies->data->get_raw( array( 'id' => $taxonomies ) );

		}

		/**
		 * Export options pages
		 *
		 * @param  array $options_pages Options pages to export
		 * @return array
		 */
		public function export_options_pages( $options_pages = array() ) {

			if ( ! is_array( $options_pages ) ) {
				$options_pages = array( $options_pages );
			}

			$this->id .= implode( '', $options_pages );

			return jet_engine()->options_pages->data->get_raw( array( 'id' => $options_pages ) );
		}

		/**
		 * Export sample content
		 * @param  string $export [description]
		 * @return [type]         [description]
		 */
		public function export_content( $export = '' ) {

			$export = filter_var( $export, FILTER_VALIDATE_BOOLEAN );

			if ( ! $export ) {
				return;
			}

			$this->id .= '1';

			$post_types    = ! empty( $_REQUEST['post_types'] ) ? $_REQUEST['post_types'] : array();
			$taxonomies    = ! empty( $_REQUEST['taxonomies'] ) ? $_REQUEST['taxonomies'] : array();
			$options_pages = ! empty( $_REQUEST['options_pages'] ) ? $_REQUEST['options_pages'] : array();
			$result        = array();

			if ( ! empty( $post_types ) ) {
				$result['posts'] = $this->export_sample_posts( $post_types );
			}

			if ( ! empty( $taxonomies ) ) {
				$result['terms'] = $this->export_sample_terms( $taxonomies );
			}

			if ( ! empty( $options_pages ) ) {
				$result['options'] = $this->export_sample_options( $options_pages );
			}

			return $result;
		}

		/**
		 * Export sample posts
		 *
		 * @return [type] [description]
		 */
		public function export_sample_posts( $post_types ) {

			$slugs = array();

			foreach ( jet_engine()->cpt->get_items() as $post_type ) {
				if ( in_array( $post_type['id'], $post_types ) ) {
					$slugs[] = $post_type['slug'];
				}
			}

			$result = array();

			foreach ( $slugs as $slug ) {

				$posts = get_posts( array(
					'post_type'      => $slug,
					'posts_per_page' => 1,
				) );

				if ( empty( $posts ) ) {
					continue;
				}

				$post       = $posts[0];
				$meta_input = array(
					'_thumbnail_id' => array(
						'media' => true,
						'url'   => get_the_post_thumbnail_url( $post->ID, 'full' )
					),
				);

				$meta_fields = jet_engine()->cpt->get_meta_fields_for_object( $slug );

				if ( ! empty( $meta_fields ) ) {
					foreach ( $meta_fields as $field ) {
						if ( 'media' === $field['type'] ) {
							$img_id = get_post_meta( $post->ID, $field['name'], true );
							if ( $img_id ) {
								$meta_input[ $field['name'] ] = array(
									'media' => true,
									'url'   => wp_get_attachment_image_url( $img_id, 'full' )
								);
							}
						} else {
							$meta_input[ $field['name'] ] = get_post_meta( $post->ID, $field['name'], true );
						}
					}
				}

				$result[] = array(
					'post_title'   => $post->post_title,
					'post_type'    => $post->post_type,
					'post_name'    => $post->post_name,
					'post_content' => $post->post_content,
					'post_excerpt' => $post->post_excerpt,
					'meta_input'   => $meta_input,
				);

			}

			return $result;

		}

		/**
		 * Export sample terms
		 *
		 * @return [type] [description]
		 */
		public function export_sample_terms( $taxonomies ) {

			$slugs = array();

			foreach ( jet_engine()->taxonomies->get_items() as $tax ) {
				if ( in_array( $tax['id'], $taxonomies ) ) {
					$slugs[] = $tax['slug'];
				}
			}

			foreach ( $slugs as $slug ) {

				$terms = get_terms( array(
					'taxonomy'   => $slug,
					'hide_empty' => false,
				) );

				if ( empty( $terms ) ) {
					continue;
				}

				$term       = $terms[0];
				$meta_input = array();

				$meta_fields = jet_engine()->taxonomies->get_meta_fields_for_object( $slug );

				if ( ! empty( $meta_fields ) ) {
					foreach ( $meta_fields as $field ) {
						if ( 'media' === $field['type'] ) {
							$img_id = get_term_meta( $term->term_id, $field['name'], true );
							if ( $img_id ) {
								$meta_input[ $field['name'] ] = array(
									'media' => true,
									'url'   => wp_get_attachment_image_url( $img_id, 'full' )
								);
							}
						} else {
							$meta_input[ $field['name'] ] = get_term_meta( $term->term_id, $field['name'], true );
						}
					}
				}

				$result[] = array(
					'name'        => $term->name,
					'slug'        => $term->slug,
					'taxonomy'    => $slug,
					'description' => $term->description,
					'meta_input'  => $meta_input,
				);

			}

			return $result;
		}

		/**
		 * Export sample options.
		 *
		 * @param  array $options_pages
		 * @return array
		 */
		public function export_sample_options( $options_pages ) {

			$options = array();

			foreach ( jet_engine()->options_pages->get_items() as $page ) {
				if ( in_array( $page['id'], $options_pages ) ) {
					$options[] = array(
						'slug' => $page['slug'],
						'name' => $page['labels']['name'],
					);

				}
			}

			$result = array();

			foreach ( $options as $option ) {
				$result[] = array(
					'name'  => $option['name'],
					'slug'  => $option['slug'],
					'value' => get_option( $option['slug'] ),
				);
			}

			return $result;
		}

		/**
		 * Export listings
		 *
		 * @return array
		 */
		public function export_listings( $listings ) {

			$query = get_posts( array(
				'post_type'      => jet_engine()->post_type->slug(),
				'post__in'       => $listings,
				'posts_per_page' => -1,
			) );

			$this->id .= implode( '', $listings );

			if ( empty( $query ) ) {
				return array();
			}

			$result = array();

			foreach ( $query as $post ) {
				$listing_type = get_post_meta( $post->ID, '_listing_type', true );
				$listing_type = ! empty( $listing_type ) ? $listing_type : 'elementor';

				$result[] = array(
					'title'    => $post->post_title,
					'slug'     => $post->post_name,
					'type'     => $listing_type,
					'settings' => get_post_meta( $post->ID, '_elementor_page_settings', true ),
					'content'  => ( 'elementor' === $listing_type ) ? get_post_meta( $post->ID, '_elementor_data', true ) : $post->post_content,
				);
			}

			return $result;

		}

		/**
		 * Add export data to dashboard page config
		 */
		public function export_config() {

			$post_types    = jet_engine()->cpt->get_items();
			$taxonomies    = jet_engine()->taxonomies->get_items();
			$meta_boxes    = jet_engine()->meta_boxes->get_items();
			$listing_items = jet_engine()->listings->get_listings();
			$relations     = jet_engine()->relations->get_items();
			$options_pages = jet_engine()->options_pages->get_items();

			$post_types    = ! empty( $post_types ) ? $post_types : array();
			$taxonomies    = ! empty( $taxonomies ) ? $taxonomies : array();
			$meta_boxes    = ! empty( $meta_boxes ) ? $meta_boxes : array();
			$listing_items = ! empty( $listing_items ) ? $listing_items : array();
			$relations     = ! empty( $relations ) ? $relations : array();
			$options_pages = ! empty( $options_pages ) ? $options_pages : array();

			array_walk( $post_types, function( &$item ) {

				$new_item = array(
					'value' => $item['id'],
					'label' => $item['labels']['name'],
				);

				$item = $new_item;
			} );

			array_walk( $taxonomies, function( &$item ) {

				$new_item = array(
					'value' => $item['id'],
					'label' => $item['labels']['name'],
				);

				$item = $new_item;
			} );

			array_walk( $meta_boxes, function( &$item ) {

				$new_item = array(
					'value' => $item['id'],
					'label' => $item['args']['name'],
				);

				$item = $new_item;
			} );

			array_walk( $relations, function( &$item ) {

				$new_item = array(
					'value' => $item['id'],
					'label' => $item['name'],
				);

				$item = $new_item;
			} );

			array_walk( $listing_items, function( &$item ) {

				$new_item = array(
					'value' => $item->ID,
					'label' => $item->post_title,
				);

				$item = $new_item;
			} );

			array_walk( $options_pages, function( &$item ) {

				$new_item = array(
					'value' => $item['id'],
					'label' => $item['labels']['name'],
				);

				$item = $new_item;
			} );

			$config = array();

			$config['post_types']    = array_values( $post_types );
			$config['taxonomies']    = array_values( $taxonomies );
			$config['meta_boxes']    = array_values( $meta_boxes );
			$config['listing_items'] = array_values( $listing_items );
			$config['relations']     = array_values( $relations );
			$config['options_pages'] = array_values( $options_pages );
			$config['base_url']      = add_query_arg(
				array( 'export_skin' => 1 ),
				jet_engine()->dashboard->dashboard_url()
			);

			return $config;
		}

		/**
		 * Export component template
		 *
		 * @return void
		 */
		public function print_templates() {

			ob_start();
			include jet_engine()->get_template( 'admin/pages/dashboard/export.php' );
			$content = ob_get_clean();

			printf( '<script type="text/x-template" id="jet_engine_skin_export">%s</script>', $content );

		}

	}

}
