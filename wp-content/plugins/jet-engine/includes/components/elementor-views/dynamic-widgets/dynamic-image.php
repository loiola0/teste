<?php
namespace Elementor;

use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Jet_Listing_Dynamic_Image_Widget extends Widget_Base {

	public function get_name() {
		return 'jet-listing-dynamic-image';
	}

	public function get_title() {
		return __( 'Dynamic Image', 'jet-engine' );
	}

	public function get_icon() {
		return 'jet-engine-icon-dynamic-image';
	}

	public function get_categories() {
		return array( 'jet-listing-elements' );
	}

	public function get_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetengine-dynamic-image-widget-overview/?utm_source=jetengine&utm_medium=dynamic-image&utm_campaign=need-help';
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'section_general',
			array(
				'label' => __( 'Content', 'jet-engine' ),
			)
		);

		$this->add_control(
			'dynamic_image_source',
			array(
				'label'   => __( 'Source', 'jet-engine' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'post_thumbnail',
				'groups'  => $this->get_dynamic_sources( 'media' ),
			)
		);

		if ( jet_engine()->options_pages ) {

			$options_pages_select = jet_engine()->options_pages->get_options_for_select( 'media' );

			if ( ! empty( $options_pages_select ) ) {
				$this->add_control(
					'dynamic_field_option',
					array(
						'label'     => __( 'Option', 'jet-engine' ),
						'type'      => Controls_Manager::SELECT,
						'default'   => '',
						'groups'    => $options_pages_select,
						'condition' => array(
							'dynamic_image_source' => 'options_page',
						),
					)
				);
			}

		}

		/**
		 * Add 3rd-party controls for sources
		 */
		do_action( 'jet-engine/listings/dynamic-image/source-controls', $this );

		$this->add_control(
			'dynamic_image_source_custom',
			array(
				'label'       => __( 'Custom meta field/repeater key', 'jet-engine' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
				'description' => __( 'Note: this filed will override Source value', 'jet-engine' ),
			)
		);

		$this->add_control(
			'image_url_prefix',
			array(
				'label'       => __( 'Image URL Prefix', 'jet-engine' ),
				'label_block' => true,
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'description' => __( 'Add prefix to the image URL. For example for the cases when source contains only part of the URL', 'jet-engine' ),
			)
		);

		$this->add_control(
			'dynamic_image_size',
			array(
				'label'       => __( 'Image Size', 'jet-engine' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'full',
				'options'     => jet_engine()->listings->get_image_sizes(),
				'condition'   => array(
					'dynamic_image_source!' => 'user_avatar',
				),
				'description' => __( 'Note: this option will work only if image stored as attachment ID', 'jet-engine' ),
			)
		);

		$this->add_control(
			'dynamic_avatar_size',
			array(
				'label'      => __( 'Image Size', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array(
					'size' => 50,
				),
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 500,
					),
				),
				'condition'   => array(
					'dynamic_image_source' => 'user_avatar',
				),
				'description' => __( 'Note: this option will work only if image stored as attachment ID', 'jet-engine' ),
			)
		);

		$this->add_control(
			'linked_image',
			array(
				'label'        => __( 'Linked image', 'jet-engine' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'jet-engine' ),
				'label_off'    => __( 'No', 'jet-engine' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'image_link_source',
			array(
				'label'     => __( 'Link Source', 'jet-engine' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '_permalink',
				'groups'    => $this->get_dynamic_sources( 'plain' ),
				'condition' => array(
					'linked_image' => 'yes',
				),
			)
		);

		if ( jet_engine()->options_pages ) {

			$options_pages_select = jet_engine()->options_pages->get_options_for_select( 'plain' );

			if ( ! empty( $options_pages_select ) ) {
				$this->add_control(
					'image_link_option',
					array(
						'label'     => __( 'Option', 'jet-engine' ),
						'type'      => Controls_Manager::SELECT,
						'default'   => '',
						'groups'    => $options_pages_select,
						'condition' => array(
							'linked_image'      => 'yes',
							'image_link_source' => 'options_page',
						),
					)
				);
			}

		}

		/**
		 * Add 3rd-party controls for sources
		 */
		do_action( 'jet-engine/listings/dynamic-image/link-source-controls', $this );

		$this->add_control(
			'image_link_source_custom',
			array(
				'label'       => __( 'Custom meta field/repeater key', 'jet-engine' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
				'description' => __( 'Note: this filed will override Meta Field value', 'jet-engine' ),
				'condition'   => array(
					'linked_image' => 'yes',
				),
			)
		);

		$this->add_control(
			'link_url_prefix',
			array(
				'label'       => __( 'Link URL Prefix', 'jet-engine' ),
				'label_block' => true,
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'description' => __( 'Add prefix to the URL, for example tel:, mailto: etc.', 'jet-engine' ),
				'condition'   => array(
					'linked_image' => 'yes',
				),
			)
		);

		$this->add_control(
			'open_in_new',
			array(
				'label'        => esc_html__( 'Open in new window', 'jet-engine' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-engine' ),
				'label_off'    => esc_html__( 'No', 'jet-engine' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array(
					'linked_image' => 'yes',
				),
			)
		);

		$this->add_control(
			'rel_attr',
			array(
				'label'   => __( 'Add "rel" attr', 'jet-engine' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
					''           => __( 'No', 'jet-engine' ),
					'alternate'  => __( 'Alternate', 'jet-engine' ),
					'author'     => __( 'Author', 'jet-engine' ),
					'bookmark'   => __( 'Bookmark', 'jet-engine' ),
					'external'   => __( 'External', 'jet-engine' ),
					'help'       => __( 'Help', 'jet-engine' ),
					'license'    => __( 'License', 'jet-engine' ),
					'next'       => __( 'Next', 'jet-engine' ),
					'nofollow'   => __( 'Nofollow', 'jet-engine' ),
					'noreferrer' => __( 'Noreferrer', 'jet-engine' ),
					'noopener'   => __( 'Noopener', 'jet-engine' ),
					'prev'       => __( 'Prev', 'jet-engine' ),
					'search'     => __( 'Search', 'jet-engine' ),
					'tag'        => __( 'Tag', 'jet-engine' ),
				),
				'condition'   => array(
					'linked_image' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'image_alignment',
			array(
				'label'   => __( 'Alignment', 'jet-engine' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'flex-start',
				'options' => array(
					'flex-start' => array(
						'title' => esc_html__( 'Start', 'jet-engine' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-left' : 'eicon-h-align-right',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-engine' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end' => array(
						'title' => esc_html__( 'End', 'jet-engine' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-right' : 'eicon-h-align-left',
					),
				),
				'selectors'  => array(
					$this->css_selector() => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hide_if_empty',
			array(
				'label'        => esc_html__( 'Hide if value is empty', 'jet-engine' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-engine' ),
				'label_off'    => esc_html__( 'No', 'jet-engine' ),
				'return_value' => 'yes',
				'default'      => '',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'fallback_image',
			array(
				'label'       => __( 'Fallback Image', 'jet-engine' ),
				'description' => __( 'This image will be shown if selected source field is empty', 'jet-engine' ),
				'type'        => Controls_Manager::MEDIA,
				'condition'   => array(
					'hide_if_empty' => '',
				),
			)
		);

		$this->add_control(
			'object_context',
			array(
				'label'     => __( 'Context', 'jet-engine' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'default_object',
				'options'   => jet_engine()->listings->allowed_context_list(),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_image_style',
			array(
				'label'      => __( 'Image', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'image_border',
				'label'          => __( 'Border', 'jet-engine' ),
				'placeholder'    => '1px',
				'selector'       => $this->css_selector( ' img' ),
			)
		);

		$this->add_responsive_control(
			'image_border_radius',
			array(
				'label'      => __( 'Border Radius', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					$this->css_selector( ' img' ) => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Returns CSS selector for nested element
	 *
	 * @param  [type] $el [description]
	 * @return [type]     [description]
	 */
	public function css_selector( $el = null ) {
		return sprintf( '{{WRAPPER}} .%1$s%2$s', $this->get_name(), $el );
	}

	/**
	 * Get meta fields for post type
	 *
	 * @return array
	 */
	public function get_dynamic_sources( $for = 'media' ) {

		if ( 'media' === $for ) {
			$default = array(
				'label'   => __( 'General', 'jet-engine' ),
				'options' => array(
					'post_thumbnail' => __( 'Post thumbnail', 'jet-engine' ),
					'user_avatar'    => __( 'User avatar', 'jet-engine' ),
				),
			);
		} else {
			$default = array(
				'label'   => __( 'General', 'jet-engine' ),
				'options' => array(
					'_permalink' => __( 'Permalink', 'jet-engine' ),
				),
			);

			if ( jet_engine()->modules->is_module_active( 'profile-builder' ) ) {
				$default['options']['profile_page'] = __( 'Profile Page', 'jet-engine' );
			}

		}

		$result      = array();
		$meta_fields = array();

		if ( jet_engine()->meta_boxes ) {
			$meta_fields = jet_engine()->meta_boxes->get_fields_for_select( $for );
		}

		if ( jet_engine()->options_pages ) {
			$default['options']['options_page'] = __( 'Options', 'jet-engine' );
		}

		$result = apply_filters(
			'jet-engine/listings/dynamic-image/fields',
			array_merge( array( $default ), $meta_fields ),
			$for
		);

		return $result;

	}

	protected function render() {
		jet_engine()->listings->render_item( 'dynamic-image', $this->get_settings() );
	}

}
