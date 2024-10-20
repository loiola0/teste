<?php
namespace Jet_Engine\Modules\Maps_Listings;

class Render extends \Jet_Engine_Render_Listing_Grid {

	private $source = null;

	public function get_name() {
		return 'jet-engine-maps-listing';
	}

	public function default_settings() {
		return array(
			'lisitng_id'                 => '',
			'address_field'              => '',
			'add_lat_lng'                => '',
			'lat_lng_address_field'      => '',
			'posts_num'                  => 6,
			'auto_center'                => true,
			'max_zoom'                   => '',
			'custom_center'              => '',
			'custom_zoom'                => 11,
			'zoom_control'               => 'auto',
			'zoom_controls'              => 'true',
			'fullscreen_control'         => 'true',
			'street_view_controls'       => 'true',
			'map_type_controls'          => 'true',
			'posts_query'                => array(),
			'meta_query_relation'        => 'AND',
			'tax_query_relation'         => 'AND',
			'hide_widget_if'             => '',
			'popup_width'                => 320,
			'popup_offset'               => 40,
			'marker_type'                => 'image',
			'marker_image'               => null,
			'marker_icon'                => null,
			'marker_label_type'          => 'post_title',
			'marker_label_field'         => '',
			'marker_label_field_custom'  => '',
			'marker_label_text'          => '',
			'marker_label_format_cb'     => 0,
			'marker_label_custom'        => false,
			'marker_label_custom_output' => '%s',
			'marker_image_field'         => '',
			'marker_image_field_custom'  => '',
			'multiple_marker_types'      => false,
			'multiple_markers'           => array(),
			'marker_clustering'          => 'true',
			'popup_pin'                  => false,
			'popup_preloader'            => false,
		);
	}

	/**
	 * Get posts
	 *
	 * @param  array $settings
	 * @return array
	 */
	public function get_posts( $settings ) {

		$args  = $this->build_posts_query_args_array( $settings );
		$query = new \WP_Query( $args );

		$this->query_vars['page']    = $query->get( 'paged' ) ? $query->get( 'paged' ) : 1;
		$this->query_vars['pages']   = $query->max_num_pages;
		$this->query_vars['request'] = $args;

		return $query->posts;

	}

	/**
	 * Returns encoded map data
	 *
	 * @param  array  $settings [description]
	 * @return [type]           [description]
	 */
	public function get_map_data( $settings = array() ) {

		$result = array(
			'zoomControl'       => isset( $settings['zoom_controls'] ) ? filter_var( $settings['zoom_controls'], FILTER_VALIDATE_BOOLEAN ) : true,
			'fullscreenControl' => isset( $settings['fullscreen_control'] ) ? filter_var( $settings['fullscreen_control'], FILTER_VALIDATE_BOOLEAN ) : true,
			'streetViewControl' => isset( $settings['street_view_controls'] ) ? filter_var( $settings['street_view_controls'], FILTER_VALIDATE_BOOLEAN ) : true,
			'mapTypeControl'    => isset( $settings['map_type_controls'] ) ? filter_var( $settings['map_type_controls'], FILTER_VALIDATE_BOOLEAN ) : true,
		);

		return htmlspecialchars( json_encode( $result ) );

	}

	/**
	 * Returns map markers list
	 *
	 * @param  array  $query    [description]
	 * @param  array  $settings [description]
	 * @return [type]           [description]
	 */
	public function get_map_markers( $query = array(), $settings = array(), $json = true ) {

		$result          = array();
		$address_field   = ! empty( $settings['address_field'] ) ? $settings['address_field'] : false;
		$add_lat_lng     = ! empty( $settings['add_lat_lng'] ) ? $settings['add_lat_lng'] : false;
		$add_lat_lng     = filter_var( $add_lat_lng, FILTER_VALIDATE_BOOLEAN );
		$lat_lng_address = ! empty( $settings['lat_lng_address_field'] ) ? $settings['lat_lng_address_field'] : false;

		if ( $address_field || ( $add_lat_lng && $lat_lng_address ) ) {

			foreach ( $query as $post ) {

				if ( null === $this->source ) {
					$this->set_source( $post, $settings );
				}

				$address = false;

				if ( $address_field ) {

					$fields = explode( '+', $address_field );

					if ( 1 === count( $fields ) ) {
						$address = Module::instance()->lat_lng->get_address_from_field( $post, $address_field );
					} else {
						$address = Module::instance()->lat_lng->get_address_from_fields_group( $post, $fields );
					}

				}

				if ( ! $address && $add_lat_lng ) {

					$fields = explode( '+', $lat_lng_address );

					if ( 1 === count( $fields ) ) {
						$address = Module::instance()->lat_lng->get_address_from_field( $post, $lat_lng_address );

						if ( $address ) {

							$address = explode( ',', $address );

							if ( 2 !== count( $address ) ) {
								$address = false;
							} else {
								$address = array(
									'lat' => trim( $address[0] ),
									'lng' => trim( $address[1] ),
								);
							}

						}

					} else {

						$miss_fields = false;
						$keys        = array( 'lat', 'lng' );
						$address     = array();

						for ( $i = 0; $i < 2; $i++ ) {

							$field = isset( $fields[ $i ] ) ? $fields[ $i ] : false;
							$key   = $keys[ $i ];

							if ( ! $field ) {
								$miss_fields = true;
							} else {

								$address_val = Module::instance()->lat_lng->get_address_from_field( $post, $field );

								if ( ! $address_val ) {
									$miss_fields = true;
								}

								$address[ $key ] = $address_val;

							}

						}

						if ( $miss_fields ) {
							$address = false;
							continue;
						}

					}

				}

				if ( empty( $address ) ) {
					continue;
				}

				$latlang = Module::instance()->lat_lng->get( $post, $address );

				if ( empty( $latlang ) ) {
					continue;
				}

				$class = get_class( $post );

				switch ( $class ) {
					case 'WP_Post':
					case 'WP_User':
						$post_id = $post->ID;
						break;

					case 'WP_Term':
						$post_id = $post->term_id;
						break;

					default:
						$post_id = apply_filters( 'jet-engine/listing/custom-post-id', get_the_ID(), $post );
				}

				$result[] = array(
					'id'            => $post_id,
					'latLang'       => $latlang,
					'label'         => $this->get_marker_label( $post, $settings ),
					'custom_marker' => $this->get_custom_marker( $post, $settings ),
				);

			}

		}

		$result = apply_filters( 'jet-engine/maps-listing/map-markers', $result );

		if ( $json ) {
			return htmlspecialchars( json_encode( $result ) );
		} else {
			return $result;
		}

	}

	public function set_source( $obj, $settings ) {

		$source = apply_filters(
			'jet-engine/listing/grid/source',
			jet_engine()->listings->data->get_listing_source(),
			$settings,
			$this
		);

		if ( 'query' === $source ) {
			$class = get_class( $obj );

			switch ( $class ) {
				case 'WP_Post':
					$source = 'posts';
					break;

				case 'WP_User':
					$source = 'users';
					break;

				case 'WP_Term':
					$source = 'terms';
					break;

				default:
					$source = apply_filters( 'jet-engine/maps-listing/source', $source, $obj );
			}
		}

		$this->source = $source;
	}

	/**
	 * Returns custom marker HTML (for dynamic image source or if dynamic markers is configured) or false
	 */
	public function get_custom_marker( $post, $settings ) {

		$conditional_marker = $this->get_conditional_marker( $post, $settings );

		if ( $conditional_marker ) {
			return $conditional_marker;
		}

		$type = ! empty( $settings['marker_type'] ) ? $settings['marker_type'] : 'image';

		switch ( $type ) {
			case 'dynamic_image':
				$field = ! empty( $settings['marker_image_field'] ) ? $settings['marker_image_field'] : false;
				$field = ! empty( $settings['marker_image_field_custom'] ) ? $settings['marker_image_field_custom'] : $field;

				if ( ! $field )  {
					return false;
				}

				$image = jet_engine()->listings->data->get_meta( $field, $post );
				break;

			default:
				$image = apply_filters( 'jet-engine/maps-listing/custom-marker/' . $type, false, $post, $settings );
		}

		if ( empty( $image ) ) {
			return false;
		}

		if ( is_array( $image ) && isset( $image['id'] ) ) {
			$image = $image['id'];
		}

		$image_id = absint( $image );

		if ( $image_id ) {

			$image_cache_key = 'jet_engine_marker_' . $image_id;
			$image_url = wp_cache_get( $image_cache_key );

			if ( ! $image_url ) {
				$image_url = wp_get_attachment_image_url( $image_id, 'full' );
				wp_cache_set( $image_cache_key, $image_url );
			}
		} else {
			$image_url = $image;
		}

		return sprintf( '<img src="%1$s" class="jet-map-marker-image" alt="" style="cursor: pointer;">', $image_url );

	}

	/**
	 * Check if we need apply to this marker condiional marker data
	 */
	public function get_conditional_marker( $post, $settings ) {

		if ( 'WP_Post' !== get_class( $post ) ) {
			return false;
		}

		$post_id = $post->ID;

		$multiple_marker_types = ! empty( $settings['multiple_marker_types'] ) ? $settings['multiple_marker_types'] : false;
		$multiple_marker_types = filter_var( $multiple_marker_types, FILTER_VALIDATE_BOOLEAN );

		if ( ! $multiple_marker_types ) {
			return false;
		}

		$marker_types = ! empty( $settings['multiple_markers'] ) ? $settings['multiple_markers'] : array();

		if ( empty( $marker_types ) ) {
			return false;
		}

		foreach ( $marker_types as $marker ) {

			$condition_met = false;
			$apply_type    = ! empty( $marker['apply_type'] ) ? $marker['apply_type'] : false;

			switch ( $apply_type ) {

				case 'meta_field':

					$field = ! empty( $marker['field_name'] ) ? $marker['field_name'] : false;
					$field = ! empty( $marker['field_name_custom'] ) ? $marker['field_name_custom'] : $field;

					if ( $field ) {

						$field_value = ! empty( $marker['field_value'] ) ? $marker['field_value'] : false;
						$saved_value = get_post_meta( $post_id, $field, true );

						if ( is_array( $saved_value ) && ! empty( $saved_value ) ) {
							if ( in_array( 'true', $saved_value ) || in_array( 'false', $saved_value ) ) {
								$condition_met = ( isset( $saved_value[ $field_value ] ) && true === filter_var( $saved_value[ $field_value ], FILTER_VALIDATE_BOOLEAN ) );
							} else {
								$condition_met = in_array( $field_value, $saved_value );
							}
						} else {
							$condition_met = ( $field_value == $saved_value );
						}

					}

					break;

				case 'post_term':

					$taxonomy = ! empty( $marker['tax_name'] ) ? $marker['tax_name'] : false;
					$term     = ! empty( $marker['term_name'] ) ? $marker['term_name'] : false;

					if ( $taxonomy && $term ) {
						$condition_met = has_term( $term, $taxonomy, $post_id );
					}

					break;

			}

			if ( $condition_met ) {

				$result = $this->get_marker_data( $marker );

				if ( $result && ! empty( $result['html'] ) ) {
					return $result['html'];
				} elseif ( $result && ! empty( $result['url'] ) ) {
					return sprintf( '<img src="%1$s" class="jet-map-marker-image" alt="" style="cursor: pointer;">', $result['url'] );
				}

			}

		}

		return false;

	}

	/**
	 * Returns marker label
	 *
	 * @param  object $post     Item object
	 * @param  array  $settings Settings
	 * @return string
	 */
	public function get_marker_label( $post, $settings = array() ) {

		$type = ! empty( $settings['marker_type'] ) ? $settings['marker_type'] : 'image';

		if ( 'text' !== $type ) {
			return false;
		}

		$label_type = ! empty( $settings['marker_label_type'] ) ? $settings['marker_label_type'] : 'post_title';
		$result     = '';

		switch ( $label_type ) {
			case 'post_title':
				$result = get_the_title( $post->ID );
				break;

			case 'meta_field':

				$field = ! empty( $settings['marker_label_field'] ) ? $settings['marker_label_field'] : null;

				if ( ! empty( $settings['marker_label_field_custom'] ) ) {
					$field = $settings['marker_label_field_custom'];
				}

				if ( $field ) {
					$result = jet_engine()->listings->data->get_meta( $field, $post );
				}

				break;

			case 'static_text':
				$result = ! empty( $settings['marker_label_text'] ) ? $settings['marker_label_text'] : '';
				break;

			default:
				$result = apply_filters( 'jet-engine/maps-listing/marker-label/' . $label_type, '', $post, $settings );
		}

		$callback = ! empty( $settings['marker_label_format_cb'] ) ? $settings['marker_label_format_cb'] : false;

		if ( $callback ) {
			$result = jet_engine()->listings->apply_callback( $result, $callback, $settings, $this );
		}

		$customize = ! empty( $settings['marker_label_custom'] ) ? $settings['marker_label_custom'] : false;
		$customize = filter_var( $customize, FILTER_VALIDATE_BOOLEAN );

		if ( $customize && ! empty( $settings['marker_label_custom_output'] ) ) {
			$result = sprintf( $settings['marker_label_custom_output'], $result );
		}

		return $result;

	}

	/**
	 * Returns marker data
	 *
	 * @return [type] [description]
	 */
	public function get_marker_data( $settings = array() ) {

		$type   = ! empty( $settings['marker_type'] ) ? $settings['marker_type'] : 'image';
		$result = array( 'type' => null );

		switch ( $type ) {

			case 'image':

				$image          = ! empty( $settings['marker_image'] ) ? $settings['marker_image'] : false;
				$result['type'] = 'image';

				if ( ! $image ) {
					return false;
				} elseif ( is_array( $image ) && empty( $image['url'] ) ) {
					return false;
				} elseif ( is_array( $image ) ) {
					$result['url'] = $image['url'];
					return $result;
				} else {
					$result['url'] = $image;
					return $result;
				}

			case 'icon':

				$icon           = ! empty( $settings['marker_icon'] ) ? $settings['marker_icon'] : false;
				$result['type'] = 'icon';

				if ( ! $icon ) {
					return false;
				} else {
					$icon_html      = \Jet_Engine_Tools::render_icon( $icon, 'jet-map-marker', array( 'style' => 'cursor:pointer;' ) );
					$result['html'] = $icon_html;
					return $result;
				}

			case 'text':

				$result['type'] = 'text';
				$result['html'] = '<div class="jet-map-marker-wrap" style="cursor: pointer;">_marker_label_</div>';

				return $result;

		}

		return false;

	}

	public function enqueue_deps( $listing_id ) {

		if ( ! $listing_id ) {
			return;
		}

		$document = \Elementor\Plugin::$instance->documents->get( $listing_id );

		if ( $document ) {
			$elements_data = $document->get_elements_raw_data();
			$this->enqueue_elements_deps( $elements_data );
		}

		$this->print_listing_css( $listing_id );

	}

	public function print_listing_css( $listing_id ) {

		if ( jet_engine()->blocks_views->is_blocks_listing( $listing_id ) ) {

			$style = get_post_meta( $listing_id, '_jet_sm_ready_style', true );

			if ( $style ) {
				printf( '<style>%s</style>', $style );
			}

		}
	}

	public function enqueue_elements_deps( $elements_data ) {

		foreach ( $elements_data as $element_data ) {

			if ( 'widget' === $element_data['elType'] ) {

				$widget = \Elementor\Plugin::$instance->elements_manager->create_element_instance( $element_data );

				$widget_script_depends = $widget->get_script_depends();
				$widget_style_depends  = $widget->get_style_depends();

				if ( ! empty( $widget_script_depends ) ) {
					foreach ( $widget_script_depends as $script_handler ) {
						wp_enqueue_script( $script_handler );
					}
				}

				if ( ! empty( $widget_style_depends ) ) {
					foreach ( $widget_style_depends as $style_handler ) {
						wp_enqueue_style( $style_handler );
					}
				}

			} else {

				$element  = \Elementor\Plugin::$instance->elements_manager->create_element_instance( $element_data );
				$children = $element->get_children();

				foreach ( $children as $key => $child ) {
					$children_data[ $key ] = $child->get_raw_data();
					$this->enqueue_elements_deps( $children_data );
				}
			}
		}

	}

	/**
	 * Render posts template.
	 * Moved to separate function to be rewritten by other layouts
	 *
	 * @param  array  $query    Query array.
	 * @param  array  $settings Settings array.
	 * @return void
	 */
	public function posts_template( $query, $settings ) {

		$map_data    = $this->get_map_data( $settings );
		$map_markers = $this->get_map_markers( $query, $settings );

		$marker_clustering = isset( $settings['marker_clustering'] ) ? filter_var( $settings['marker_clustering'], FILTER_VALIDATE_BOOLEAN ) : true;

		jet_engine()->frontend->set_listing( $settings['lisitng_id'] );

		if ( $marker_clustering ) {
			wp_enqueue_script( 'jet-markerclustererplus' );
		}

		wp_enqueue_script( 'jet-maps-listings' );

		$listing_id      = ! empty( $settings['lisitng_id'] ) ? absint( $settings['lisitng_id'] ) : false;
		$auto_center     = ! empty( $settings['auto_center'] ) ? $settings['auto_center'] : false;
		$auto_center     = filter_var( $auto_center, FILTER_VALIDATE_BOOLEAN );
		$custom_center   = ! empty( $settings['custom_center'] ) ? $settings['custom_center'] : false;
		$custom_zoom     = ! empty( $settings['custom_zoom'] ) ? $settings['custom_zoom'] : 11;
		$popup_preloader = ! empty( $settings['popup_preloader'] ) ? $settings['popup_preloader'] : false;
		$popup_preloader = filter_var( $popup_preloader, FILTER_VALIDATE_BOOLEAN );


		if ( ! $auto_center && $custom_center ) {
			$custom_center = Module::instance()->lat_lng->get_from_transient( $custom_center );
		}

		$permalink_structure = get_option( 'permalink_structure' );

		$general = array(
			'api'              => jet_engine()->api->get_route( 'get-map-marker-info', true ),
			'restNonce'        => wp_create_nonce( 'wp_rest' ),
			'listingID'        => $listing_id,
			'source'           => $this->source,
			'width'            => ! empty( $settings['popup_width'] ) ? absint( $settings['popup_width'] ) : 320,
			'offset'           => isset( $settings['popup_offset'] ) ? absint( $settings['popup_offset'] ) : 40,
			'clustererImg'     => jet_engine()->plugin_url( 'assets/lib/markerclustererplus/img/m' ),
			'marker'           => $this->get_marker_data( $settings ),
			'autoCenter'       => $auto_center,
			'maxZoom'          => ! empty( $settings['max_zoom'] ) ? absint( $settings['max_zoom'] ) : false,
			'customCenter'     => $custom_center,
			'customZoom'       => $custom_zoom,
			'popupPreloader'   => $popup_preloader,
			'querySeparator'   => ! empty( $permalink_structure ) ? '?' : '&',
			'markerClustering' => $marker_clustering,
			'advanced'         => array(
				'zoom_control' => ! empty( $settings['zoom_control'] ) ? $settings['zoom_control'] : 'auto',
			),
		);

		if ( ! empty( $settings['custom_style'] ) ) {
			$general['styles'] = json_decode( $settings['custom_style'] );
		}

		$this->enqueue_deps( $listing_id );

		$general = htmlspecialchars( json_encode( $general ) );

		$classes = array( 'jet-map-listing' );

		if ( ! empty( $settings['popup_pin'] ) ) {
			$classes[] = 'popup-has-pin';
		}

		$failures_message = Module::instance()->lat_lng->failures_message();

		if ( $failures_message && current_user_can( 'manage_options' ) ) {
			echo $failures_message;
		}

		$custom_css = '';

		if ( ! empty( $settings['map_height'] ) && ! is_array( $settings['map_height'] ) ) {
			$custom_css = ' style="height:' . $settings['map_height'] . 'px;"';
		}

		printf(
			'<div class="%4$s" data-init="%1$s" data-markers="%2$s" data-general="%3$s"%5$s></div>',
			$map_data,
			$map_markers,
			$general,
			implode( ' ', $classes ),
			$custom_css
		);

	}

}
