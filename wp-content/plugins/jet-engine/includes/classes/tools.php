<?php
/**
 * Tools class
 */

class Jet_Engine_Tools {

	/**
	 * Process
	 * @param  [type] $filename [description]
	 * @param  string $file     [description]
	 * @return [type]           [description]
	 */
	public static function file_download( $filename = null, $file = '', $type = 'application/json' ) {

		set_time_limit( 0 );

		@session_write_close();

		if( function_exists( 'apache_setenv' ) ) {
			$variable = 'no-gzip';
			$value = 1;
			@apache_setenv($variable, $value);
		}

		@ini_set( 'zlib.output_compression', 'Off' );

		nocache_headers();

		header( "Robots: none" );
		header( "Content-Type: " . $type );
		header( "Content-Description: File Transfer" );
		header( "Content-Disposition: attachment; filename=\"" . $filename . "\";" );
		header( "Content-Transfer-Encoding: binary" );

		// Set the file size header
		header( "Content-Length: " . strlen( $file ) );

		echo $file;
		die();

	}

	public static function add_query_args_by_settings( $url = null, $settings = array() ) {

		if ( empty( $settings['add_query_args'] ) || empty( $settings['query_args'] ) ) {
			return $url;
		}

		$query_args = $settings['query_args'];
		$query_args = preg_split( '/\r\n|\r|\n/', $query_args );

		if ( empty( $query_args ) || ! is_array( $query_args ) ) {
			return $url;
		}

		$final_query_args = array();

		foreach ( $query_args as $arg ) {
			$arg = explode( '=', $arg );

			if ( 1 < count( $arg ) ) {
				$final_query_args[ $arg[0] ] = jet_engine()->listings->macros->do_macros( $arg[1], $url );
			}

		}

		if ( ! empty( $final_query_args ) ) {
			$url = add_query_arg( $final_query_args, $url );
		}

		return $url;

	}

	public static function sanitize_html_tag( $input ) {
		$available_tags = array( 'div', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'span', 'a', 'section', 'header', 'footer', 'main', 'b', 'em', 'i', 'nav', 'article', 'aside', 'tr', 'ul', 'ol', 'li' );
		return in_array( strtolower( $input ), $available_tags ) ? $input : 'div';
	}

	/**
	 * Returns all post types list to use in JS components
	 *
	 * @return [type] [description]
	 */
	public static function get_post_types_for_js( $placeholder = false ) {

		$post_types = get_post_types( array(), 'objects' );
		$types_list = self::prepare_list_for_js( $post_types, 'name', 'label' );

		if ( $placeholder && is_array( $placeholder ) ) {
			$types_list = array_merge( array( $placeholder ), $types_list );
		}

		return $types_list;
	}

	/**
	 * Return all taxonomies list to use in JS components
	 *
	 * @return [type] [description]
	 */
	public static function get_taxonomies_for_js() {
		$taxonomies = get_taxonomies( array(), 'objects' );
		return self::prepare_list_for_js( $taxonomies, 'name', 'label' );
	}

	/**
	 * Returns all registeredroles for JS
	 */
	public static function get_user_roles_for_js() {

		$roles = self::get_user_roles();
		$result = array();

		foreach ( $roles as $role => $label ) {
			if ( ! isset( $result[ $role ] ) ) {
				$result[ $role ] = array(
					'value' => $role,
					'label' => $label,
				);
			}
		}

		return array_values( $result );

	}

	/**
	 * Returns all registered user roles
	 *
	 * @return [type] [description]
	 */
	public static function get_user_roles() {

		if ( ! function_exists( 'get_editable_roles' ) ) {
			return array();
		} else {
			$roles  = get_editable_roles();
			$result = array();

			foreach ( $roles as $role => $data ) {
				$result[ $role ] = $data['name'];
			}

			return $result;
		}

	}

	/**
	 * Prepare passed array for using in JS options
	 *
	 * @return [type] [description]
	 */
	public static function prepare_list_for_js( $array = array(), $value_key = null, $label_key = null ) {

		$result = array();

		if ( ! is_array( $array ) || empty( $array ) ) {
			return $result;
		}

		foreach ( $array as $item ) {

			$value = null;
			$label = null;

			if ( is_object( $item ) ) {
				$value = $item->$value_key;
				$label = $item->$label_key;
			} elseif ( is_array( $item ) ) {
				$value = $item[ $value_key ];
				$label = $item[ $label_key ];
			} else {
				$value = $item;
				$label = $item;
			}

			$result[] = array(
				'value' => $value,
				'label' => $label,
			);
		}

		return $result;

	}

	/**
	 * Render new elementor icons
	 *
	 * @return [type] [description]
	 */
	public static function render_icon( $icon, $icon_class, $custom_atts = array() ) {

		$custom_atts_string = '';

		if ( ! empty( $custom_atts ) ) {
			foreach ( $custom_atts as $key => $value ) {
				$custom_atts_string .= sprintf( ' %1$s="%2$s"', $key, $value );
			}
		}

		static $total = 0;

		if ( ! is_array( $icon ) && is_numeric( $icon ) ) {

			ob_start();

			echo '<div class="' . $icon_class . ' is-svg-icon"' . $custom_atts_string . '>';

				$mime = get_post_mime_type( $icon );

				if ( 'image/svg+xml' === $mime ) {
					$file = get_attached_file( $icon );

					if ( file_exists( $file ) ) {
						include $file;
					}

				} else {
					echo wp_get_attachment_image( $icon, 'full' );
				}

			echo '</div>';

			return ob_get_clean();
		}

		if ( empty( $icon['value'] ) ) {
			return false;
		}

		$is_new = class_exists( 'Elementor\Icons_Manager' ) && Elementor\Icons_Manager::is_migration_allowed();

		if ( $is_new ) {
			ob_start();

			if ( 'svg' === $icon['library'] ) {
				echo '<div class="' . $icon_class . ' is-svg-icon"' . $custom_atts_string . '>';
			}

			$custom_atts['class'] = $icon_class;
			$custom_atts['aria-hidden'] = 'true';

			Elementor\Icons_Manager::render_icon( $icon, $custom_atts );

			if ( 'svg' === $icon['library'] ) {
				echo '</div>';
			}

			return ob_get_clean();

		} else {
			return false;
		}

	}

	/**
	 * Get html attributes string.
	 *
	 * @param  array $attrs
	 * @return string
	 */
	public static function get_attr_string( $attrs ) {
		$result_array = array();

		foreach ( $attrs as $key => $value ) {
			if ( is_array( $value ) ) {
				$value = join( ' ', $value );
			}

			$result_array[] = sprintf( '%1$s="%2$s"', $key, esc_attr( $value ) );
		}

		return join( ' ', $result_array );
	}

	/**
	 * Check if is valid timestamp
	 *
	 * @param  mixed $timestamp
	 * @return boolean
	 */
	public static function is_valid_timestamp( $timestamp ) {
		return ( ( string ) ( int ) $timestamp === $timestamp || ( int ) $timestamp === $timestamp )
			&& ( $timestamp <= PHP_INT_MAX )
			&& ( $timestamp >= ~PHP_INT_MAX );
	}

	/**
	 * Checks a value for being empty.
	 *
	 * @param  mixed $source
	 * @param  bool|string $key
	 * @return bool
	 */
	public static function is_empty( $source, $key = false ) {

		if ( is_array( $source ) && $key ) {

			if ( ! isset( $source[ $key ] ) ) {
				return true;
			}

			$source = $source[ $key ];
		}

		return empty( $source ) && '0' !== $source;
	}

	/**
	 * Determines whether the current request is a REST API request.
	 *
	 * @return bool
	 */
	public static function wp_doing_rest() {
		return apply_filters( 'jet-engine/wp_doing_rest', defined( 'REST_REQUEST' ) && REST_REQUEST );
	}

	/**
	 * Returns allowed operatos list in the given format
	 *
	 * @param  array  $exclude excluded operators list
	 * @param  [type] $format  ARRAY_N or ARRAY_A
	 * @return [type]          [description]
	 */
	public static function operators_list( $exclude = array(), $format = ARRAY_N ) {

		$operators = array(
			'='           => __( 'Equal (=)', 'jet-engine' ),
			'!='          => __( 'Not equal (!=)', 'jet-engine' ),
			'>'           => __( 'Greater than (>)', 'jet-engine' ),
			'>='          => __( 'Greater or equal (>=)', 'jet-engine' ),
			'<'           => __( 'Less than (<)', 'jet-engine' ),
			'<='          => __( 'Less or equal (<=)', 'jet-engine' ),
			'LIKE'        => __( 'Like', 'jet-engine' ),
			'NOT LIKE'    => __( 'Not like', 'jet-engine' ),
			'IN'          => __( 'In the list', 'jet-engine' ),
			'NOT IN'      => __( 'Not in the list', 'jet-engine' ),
			'BETWEEN'     => __( 'Between', 'jet-engine' ),
			'NOT BETWEEN' => __( 'Not between', 'jet-engine' ),
			'EXISTS'      => __( 'Exists', 'jet-engine' ),
			'NOT EXISTS'  => __( 'Not exists', 'jet-engine' ),
		);

		$allowed = array_diff( array_keys( $operators ), $exclude );
		$result  = array();

		foreach ( $allowed as $operator ) {
			switch ( $format ) {
				case ARRAY_N:
					$result[] = array(
						'value' => $operator,
						'label' => $operators[ $operator ],
					);
					break;

				case ARRAY_A:
					$result[ $operator ] = $operators[ $operator ];
					break;
			}
		}

		return $result;

	}

	/**
	 * Returns allowed data tpes list in the given format
	 *
	 * @param  [type] $format  ARRAY_N or ARRAY_A
	 * @return [type]          [description]
	 */
	public static function data_types_list( $format = ARRAY_N ) {

		$data_types = array(
			'CHAR'     => __( 'Char', 'jet-engine' ),
			'NUMERIC'  => __( 'Numeric', 'jet-engine' ),
			'DATE'     => __( 'Date', 'jet-engine' ),
			'DATETIME' => __( 'Datetime', 'jet-engine' ),
			'DECIMAL'  => __( 'Decimal', 'jet-engine' ),
			'TIME'     => __( 'Time', 'jet-engine' ),
			'BINARY'   => __( 'Binary', 'jet-engine' ),
			'SIGNED'   => __( 'Signed', 'jet-engine' ),
			'UNSIGNED' => __( 'Unsigned', 'jet-engine' ),
		);

		if ( ARRAY_N === $format ) {

			$result = array();

			foreach ( $data_types as $type => $label ) {
				$result[] = array(
					'value' => $type,
					'label' => $label,
				);
			}

			return $result;

		} else {
			return $data_types;
		}

	}

	public static function get_post_statuses_for_js() {

		return array(
			array(
				'value' => 'any',
				'label' => __( 'Any', 'jet-engine' ),
			),
			array(
				'value' => 'publish',
				'label' => __( 'Publish', 'jet-engine' ),
			),
			array(
				'value' => 'pending',
				'label' => __( 'Pending', 'jet-engine' ),
			),
			array(
				'value' => 'draft',
				'label' => __( 'Draft', 'jet-engine' ),
			),
			array(
				'value' => 'future',
				'label' => __( 'Future', 'jet-engine' ),
			),
			array(
				'value' => 'private',
				'label' => __( 'Private', 'jet-engine' ),
			),
			array(
				'value' => 'trash',
				'label' => __( 'Trash', 'jet-engine' ),
			)
		);
		
	}

	/**
	 * Get attachment image data array from raw data.
	 *
	 * @param mixed  $img_data Image data(id, url, array('id'=>'','url'=>'')).
	 * @param string $include  Includes keys(id, url, all).
	 *
	 * @return array|bool
	 */
	public static function get_attachment_image_data_array( $img_data = null, $include = 'all' ) {

		$result = false;

		if ( empty( $img_data ) ) {
			return $result;
		}

		if ( is_numeric( $img_data ) ) {

			switch ( $include ) {
				case 'id':
					$result = array(
						'id' => $img_data,
					);
					break;

				case 'url':
					$result = array(
						'url' => wp_get_attachment_url( $img_data ),
					);
					break;

				default:
					$result = array(
						'id'  => $img_data,
						'url' => wp_get_attachment_url( $img_data ),
					);
			}

		} elseif ( filter_var( $img_data, FILTER_VALIDATE_URL ) ) {

			switch ( $include ) {
				case 'id':
					$result = array(
						'id' => attachment_url_to_postid( $img_data ),
					);
					break;

				case 'url':
					$result = array(
						'url' => $img_data,
					);
					break;

				default:
					$result = array(
						'id'  => attachment_url_to_postid( $img_data ),
						'url' => $img_data,
					);
			}

		} elseif ( is_array( $img_data ) && isset( $img_data['id'] ) && isset( $img_data['url'] ) ) {

			switch ( $include ) {
				case 'id':
					$result = array(
						'id' => $img_data['id'],
					);
					break;

				case 'url':
					$result = array(
						'url' => $img_data['url'],
					);
					break;

				default:
					$result = $img_data;
			}

		}

		return $result;
	}

}
