<?php
namespace Elementor;

use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Jet_Listing_Dynamic_Field_Widget extends Widget_Base {

	public function get_name() {
		return 'jet-listing-dynamic-field';
	}

	public function get_title() {
		return __( 'Dynamic Field', 'jet-engine' );
	}

	public function get_icon() {
		return 'jet-engine-icon-dynamic-field';
	}

	public function get_categories() {
		return array( 'jet-listing-elements' );
	}

	public function get_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetengine-dynamic-field-widget-overview-how-to-use-filter-field-output/?utm_source=jetengine&utm_medium=dynamic-field&utm_campaign=need-help';
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'section_general',
			array(
				'label' => __( 'Content', 'jet-engine' ),
			)
		);

		$this->add_control(
			'dynamic_field_source',
			array(
				'label'   => __( 'Source', 'jet-engine' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'object',
				'options' => jet_engine()->listings->data->get_field_sources(),
			)
		);

		$this->add_control(
			'dynamic_field_relation_type',
			array(
				'label'   => __( 'Get', 'jet-engine' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'grandparents',
				'options' => array(
					'grandparents'  => __( 'Grandparent Posts', 'jet-engine' ),
					'grandchildren' => __( 'Grandchildren Posts', 'jet-engine' ),
				),
				'condition' => array(
					'dynamic_field_source' => 'relations_hierarchy',
				),
			)
		);

		$this->add_control(
			'dynamic_field_relation_post_type',
			array(
				'label'   => __( 'From Post Type', 'jet-engine' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => jet_engine()->listings->get_post_types_for_options(),
				'condition' => array(
					'dynamic_field_source' => 'relations_hierarchy',
				),
			)
		);

		$this->add_control(
			'dynamic_field_post_object',
			array(
				'label'     => __( 'Object Field', 'jet-engine' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'post_title',
				'groups'    => $this->get_object_fields(),
				'condition' => array(
					'dynamic_field_source' => 'object',
				),
			)
		);

		$this->add_control(
			'dynamic_field_wp_excerpt',
			array(
				'label'     => __( 'Automatically generated excerpt', 'jet-engine' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => '',
				'condition' => array(
					'dynamic_field_source'      => 'object',
					'dynamic_field_post_object' => 'post_excerpt',
				),
			)
		);

		$this->add_control(
			'dynamic_excerpt_more',
			array(
				'label'     => __( 'More string', 'jet-engine' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '...',
				'condition' => array(
					'dynamic_field_source'      => 'object',
					'dynamic_field_post_object' => 'post_excerpt',
				),
			)
		);

		$this->add_control(
			'dynamic_excerpt_length',
			array(
				'label'     => __( 'Custom length', 'jet-engine' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 300,
				'default'   => 0,
				'condition' => array(
					'dynamic_field_source'      => 'object',
					'dynamic_field_post_object' => 'post_excerpt',
				),
			)
		);

		$meta_fields = $this->get_meta_fields_for_post_type();

		if ( ! empty( $meta_fields ) ) {

			$this->add_control(
				'dynamic_field_post_meta',
				array(
					'label'     => __( 'Meta Field', 'jet-engine' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => '',
					'groups'    => $meta_fields,
					'condition' => array(
						'dynamic_field_source' => 'meta',
					),
				)
			);

		}

		if ( jet_engine()->options_pages ) {

			$options_pages_select = jet_engine()->options_pages->get_options_for_select( 'plain' );

			if ( ! empty( $options_pages_select ) ) {
				$this->add_control(
					'dynamic_field_option',
					array(
						'label'     => __( 'Option', 'jet-engine' ),
						'type'      => Controls_Manager::SELECT,
						'default'   => '',
						'groups'    => $options_pages_select,
						'condition' => array(
							'dynamic_field_source' => 'options_page',
						),
					)
				);
			}

		}

		/**
		 * Add 3rd-party controls for sources
		 */
		do_action( 'jet-engine/listings/dynamic-field/source-controls', $this );


		$this->add_control(
			'dynamic_field_post_meta_custom',
			array(
				'label'       => __( 'Custom meta field/repeater key', 'jet-engine' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
				'description' => __( 'Note: this field will override Meta Field value', 'jet-engine' ),
				'condition'   => array(
					'dynamic_field_source!' => 'object',
				),
			)
		);

		if ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '2.6.0', '>=' ) ) {
			$this->add_control(
				'selected_field_icon',
				array(
					'label'            => __( 'Field Icon', 'jet-engine' ),
					'type'             => Controls_Manager::ICONS,
					'label_block'      => true,
					'fa4compatibility' => 'field_icon',

				)
			);
		} else {
			$this->add_control(
				'field_icon',
				array(
					'label'       => __( 'Field Icon', 'jet-engine' ),
					'type'        => Controls_Manager::ICON,
					'label_block' => true,
					'file'        => '',
					'default'     => '',
				)
			);
		}

		$this->add_control(
			'field_tag',
			array(
				'label'   => __( 'HTML tag', 'jet-engine' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'div',
				'options' => array(
					'div'  => 'DIV',
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'p'    => 'P',
					'span' => 'SPAN',
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
			'field_fallback',
			array(
				'label'       => esc_html__( 'Fallback', 'jet-engine' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
				'description' => __( 'Show this if field value is empty', 'jet-engine' ),
				'condition'   => array(
					'hide_if_empty' => '',
				),
			)
		);

		$this->add_control(
			'dynamic_field_filter',
			array(
				'label'        => esc_html__( 'Filter field output', 'jet-engine' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-engine' ),
				'label_off'    => esc_html__( 'No', 'jet-engine' ),
				'return_value' => 'yes',
				'default'      => '',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'filter_callback',
			array(
				'label'     => __( 'Callback', 'jet-engine' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => jet_engine()->listings->get_allowed_callbacks(),
				'condition' => array(
					'dynamic_field_filter' => 'yes',
				),
			)
		);

		$glossaries = array(
			'' => __( 'Select glossary...', 'jet-engine' ),
		);

		foreach ( jet_engine()->glossaries->settings->get() as $glossary ) {
			$glossaries[ $glossary['id']] = $glossary['name'];
		}

		$this->add_control(
			'labels_by_glossary',
			array(
				'label'     => __( 'Select glossary', 'jet-engine' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => $glossaries,
				'condition' => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => array( 'jet_engine_label_by_glossary' ),
				),
			)
		);

		$this->add_control(
			'date_format',
			array(
				'label'       => esc_html__( 'Format', 'jet-engine' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'F j, Y',
				'condition'   => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => array( 'date', 'date_i18n' ),
				),
				'description' => sprintf( '<a href="https://codex.wordpress.org/Formatting_Date_and_Time" target="_blank">%s</a>', __( 'Documentation on date and time formatting', 'jet-engine' ) ),
			)
		);

		$this->add_control(
			'num_dec_point',
			array(
				'label'       => esc_html__( 'Decimal point', 'jet-engine' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '.',
				'description' => __( 'Sets the separator for the decimal point', 'jet-engine' ),
				'condition'   => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => 'number_format',
				),
			)
		);

		$this->add_control(
			'num_thousands_sep',
			array(
				'label'       => esc_html__( 'Thousands separator', 'jet-engine' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => ',',
				'condition'   => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => 'number_format',
				),
			)
		);

		$this->add_control(
			'num_decimals',
			array(
				'label'       => esc_html__( 'Decimal points', 'jet-engine' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 0,
				'max'         => 10,
				'step'        => 1,
				'default'     => 2,
				'description' => __( 'Sets the number of visible decimal points', 'jet-engine' ),
				'condition'   => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => 'number_format',
				),
			)
		);

		$this->add_control(
			'human_time_diff_from_key',
			array(
				'label'       => esc_html__( 'Additional meta key', 'jet-engine' ),
				'description' => esc_html__( 'Pass additional date meta key for calculating time diff.', 'jet-engine' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'condition'   => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => 'human_time_diff',
				),
			)
		);

		$this->add_control(
			'zeroise_threshold',
			array(
				'label'       => esc_html__( 'Threshold', 'jet-engine' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 0,
				'max'         => 10,
				'step'        => 1,
				'default'     => 3,
				'description' => __( 'Digit places number needs to be to not have zeros added', 'jet-engine' ),
				'condition'   => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => 'zeroise',
				),
			)
		);

		$this->add_control(
			'child_path',
			array(
				'label'       => __( 'Child item name', 'jet-engine' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => '',
				'description' => __( 'Name of the child item to get. Or path to the nested child item. Separate nesting levels with "/". For example - level-1-name/level-2-name/child-item-name', 'jet-engine' ),
				'condition'   => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => 'jet_engine_get_child',
				),
			)
		);

		$this->add_control(
			'related_list_is_single',
			array(
				'label'        => esc_html__( 'Single value', 'jet-engine' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-engine' ),
				'label_off'    => esc_html__( 'No', 'jet-engine' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => 'jet_related_posts_list',
				),
			)
		);

		$this->add_control(
			'related_list_is_linked',
			array(
				'label'        => esc_html__( 'Add links to related posts', 'jet-engine' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-engine' ),
				'label_off'    => esc_html__( 'No', 'jet-engine' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => 'jet_related_posts_list',
				),
			)
		);

		$this->add_control(
			'related_list_tag',
			array(
				'label'   => __( 'Related list HTML tag', 'jet-engine' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'ul',
				'options' => array(
					'ul'   => 'UL',
					'ol'   => 'OL',
					'div'  => 'DIV',
				),
				'condition'   => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => 'jet_related_posts_list',
				),
			)
		);

		$this->add_control(
			'multiselect_delimiter',
			array(
				'label'       => esc_html__( 'Delimiter', 'jet-engine' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => ', ',
				'condition'   => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => array( 'jet_engine_render_multiselect', 'jet_related_posts_list', 'jet_engine_render_post_titles', 'jet_engine_render_checkbox_values', 'jet_engine_label_by_glossary' ),
				),
			)
		);

		$this->add_control(
			'switcher_true',
			array(
				'label'       => esc_html__( 'Text if enabled', 'jet-engine' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => '',
				'condition'   => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => 'jet_engine_render_switcher',
				),
			)
		);

		$this->add_control(
			'switcher_false',
			array(
				'label'       => esc_html__( 'Text if disabled', 'jet-engine' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => '',
				'condition'   => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => 'jet_engine_render_switcher',
				),
			)
		);

		$this->add_control(
			'checklist_cols_num',
			array(
				'label'       => __( 'Columns number', 'jet-engine' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 1,
				'min'         => 1,
				'max'         => 6,
				'step'        => 1,
				'condition'   => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => 'jet_engine_render_checklist',
				),
			)
		);

		$this->add_control(
			'checklist_divider',
			array(
				'label'        => esc_html__( 'Add divider between items', 'jet-engine' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-engine' ),
				'label_off'    => esc_html__( 'No', 'jet-engine' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => 'jet_engine_render_checklist',
				),
			)
		);

		$this->add_control(
			'checklist_divider_color',
			array(
				'label' => __( 'Divider color', 'jet-engine' ),
				'type' => Controls_Manager::COLOR,
				'condition'    => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => 'jet_engine_render_checklist',
					'checklist_divider'    => 'yes',
				),
				'selectors' => array(
					$this->css_selector( ' .jet-check-list__item' ) => 'border-color: {{VALUE}}',
				),
			)
		);

		/**
		 * Add custom controls for Callbacks
		 */
		do_action( 'jet-engine/listing/dynamic-field/callback-controls', $this );

		$this->add_control(
			'dynamic_field_custom',
			array(
				'label'        => esc_html__( 'Customize field output', 'jet-engine' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-engine' ),
				'label_off'    => esc_html__( 'No', 'jet-engine' ),
				'return_value' => 'yes',
				'default'      => '',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'dynamic_field_format',
			array(
				'label'       => __( 'Field format', 'jet-engine' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => '%s',
				'description' => __( '%s will be replaced with field value', 'jet-engine' ),
				'condition' => array(
					'dynamic_field_custom' => 'yes',
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
			'section_field_style',
			array(
				'label'      => __( 'Field', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_control(
			'field_color',
			array(
				'label' => __( 'Color', 'jet-engine' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->css_selector( '__content' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'field_typography',
				'selector' => $this->css_selector( '__content' ),
			)
		);

		$this->add_control(
			'field_width',
			array(
				'label'   => __( 'Field content width', 'jet-engine' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'auto',
				'options' => array(
					'auto' => __( 'Auto', 'jet-engine' ),
					'100%' => __( 'Fullwidth', 'jet-engine' ),
				),
				'selectors'  => array(
					$this->css_selector( ' .jet-listing-dynamic-field__inline-wrap' ) => 'width: {{VALUE}};',
					$this->css_selector( ' .jet-listing-dynamic-field__content' ) => 'width: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'field_display',
			array(
				'label'     => __( 'Display', 'jet-engine' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'inline',
				'options'   => array(
					'inline'    => __( 'Inline', 'jet-engine' ),
					'multiline' => __( 'Multiline', 'jet-engine' ),
				),
			)
		);

		$this->add_responsive_control(
			'field_alignment',
			array(
				'label'       => esc_html__( 'Widget Items Alignment', 'jet-engine' ),
				'type'        => Controls_Manager::CHOOSE,
				'default'     => 'flex-start',
				'options'     => array(
					'flex-start'    => array(
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
				'condition' => array(
					'field_display' => 'inline',
				),
				'description' => __( 'Icon and field value. Affects to single line field values.', 'jet-engine' ),
			)
		);

		$this->add_responsive_control(
			'content_alignment',
			array(
				'label'       => esc_html__( 'Field Content Alignment', 'jet-engine' ),
				'type'        => Controls_Manager::CHOOSE,
				'default'     => 'left',
				'options'     => array(
					'left'    => array(
						'title' => esc_html__( 'Left', 'jet-engine' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-engine' ),
						'icon'  => 'fa fa-align-center',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'jet-engine' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'selectors'  => array(
					$this->css_selector( '__content' ) => 'text-align: {{VALUE}};',
				),
				//'condition' => array(
				//	'field_display' => 'multiline',
				//),
				'description' => __( 'Field value. Affects to multiline field values.', 'jet-engine' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'field_bg',
				'selector' => $this->css_selector( '.display-multiline' ) . ', ' . $this->css_selector( '.display-inline .jet-listing-dynamic-field__inline-wrap' ),
			)
		);

		$this->add_responsive_control(
			'field_padding',
			array(
				'label'      => __( 'Padding', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					$this->css_selector( '.display-multiline' ) => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					$this->css_selector( '.display-inline .jet-listing-dynamic-field__inline-wrap' ) => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'field_margin',
			array(
				'label'      => __( 'Margin', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					$this->css_selector( '.display-multiline' ) => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					$this->css_selector( '.display-inline .jet-listing-dynamic-field__inline-wrap' ) => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'field_border',
				'label'          => __( 'Border', 'jet-engine' ),
				'placeholder'    => '1px',
				'selector'       => $this->css_selector( '.display-multiline' ) . ', ' . $this->css_selector( '.display-inline .jet-listing-dynamic-field__inline-wrap' ),
			)
		);

		$this->add_responsive_control(
			'field_border_radius',
			array(
				'label'      => __( 'Border Radius', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					$this->css_selector( '.display-multiline' ) => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					$this->css_selector( '.display-inline .jet-listing-dynamic-field__inline-wrap' ) => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'field_box_shadow',
				'selector' => $this->css_selector( '.display-multiline' ) . ', ' . $this->css_selector( '.display-inline .jet-listing-dynamic-field__inline-wrap' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_icon_style',
			array(
				'label'      => __( 'Icon', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label' => __( 'Color', 'jet-engine' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->css_selector( '__icon' ) => 'color: {{VALUE}}',
					$this->css_selector( '__icon svg path' ) => 'fill: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => __( 'Size', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'selectors'  => array(
					$this->css_selector( '__icon' ) => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_gap',
			array(
				'label'      => __( 'Gap', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'body:not(.rtl) ' . $this->css_selector( '__icon' ) => 'margin-right: {{SIZE}}{{UNIT}};',
					'body.rtl ' . $this->css_selector( '__icon' ) => 'margin-left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_misc_style',
			array(
				'label'      => __( 'Misc', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		/**
		 * Add custom controls for Callbacks
		 */
		do_action( 'jet-engine/listing/dynamic-field/misc-style-controls', $this );

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
	 * Returns object fileds option depends from source
	 *
	 * @return array
	 */
	public function get_object_fields() {
		return jet_engine()->listings->data->get_object_fields();
	}

	/**
	 * Get meta fields for post type
	 *
	 * @return array
	 */
	public function get_meta_fields_for_post_type() {

		if ( jet_engine()->meta_boxes ) {
			return jet_engine()->meta_boxes->get_fields_for_select( 'plain' );
		} else {
			return array();
		}

	}

	protected function render() {
		jet_engine()->listings->render_item( 'dynamic-field', $this->get_settings() );
	}

}
