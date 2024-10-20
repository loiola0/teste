<?php
namespace ElementorPro\Modules\VideoPlaylist\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Utils;
use ElementorPro\Base\Base_Widget;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Video_Playlist extends Base_Widget {

	public function get_name() {
		return 'video-playlist';
	}

	public function get_title() {
		return __( 'Video Playlist', 'elementor-pro' );
	}

	public function get_icon() {
		return 'eicon-video-playlist';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_playlist',
			[
				'label' => __( 'Playlist', 'elementor-pro' ),
			]
		);

		$this->add_control(
			'tabs_direction',
			[
				'label' => __( 'Position', 'elementor-pro' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'vertical',
				'options' => [
					'horizontal' => __( 'Horizontal', 'elementor-pro' ),
					'vertical' => __( 'Vertical', 'elementor-pro' ),
				],
				'prefix_class' => 'e-tabs-view-',
			]
		);

		$this->add_control(
			'playlist_title',
			[
				'label' => __( 'Playlist Name', 'elementor-pro' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Playlist', 'elementor-pro' ),
				'placeholder' => __( 'Playlist', 'elementor-pro' ),
				'frontend_available' => true,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'type',
			[
				'label' => __( 'Type', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'youtube',
				'options' => [
					'youtube' => __( 'YouTube', 'elementor-pro' ),
					'hosted' => __( 'Self Hosted', 'elementor-pro' ),
					'section' => __( 'Section', 'elementor-pro' ),
				],
			]
		);

		$repeater->add_control(
			'youtube_url',
			[
				'label' => __( 'Link', 'elementor-pro' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'Paste URL', 'elementor-pro' ) . ' (YouTube)',
				'label_block' => true,
				'condition' => [
					'type' => 'youtube',
				],
			]
		);

		$repeater->add_control(
			'fetch_data',
			[
				'type' => Controls_Manager::BUTTON,
				'label_block' => true,
				'text' => __( 'Get Video Data', 'elementor-pro' ),
				'separator' => 'none',
				'event' => 'elementorPlaylistWidget:fetchData',
				'condition' => [
					'type' => 'youtube',
				],
			]
		);

		$repeater->add_control(
			'is_external_url',
			[
				'label' => __( 'External URL', 'elementor-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => [
					'type' => 'hosted',
				],
			]
		);

		$repeater->add_control(
			'hosted_url',
			[
				'label' => __( 'Choose File', 'elementor-pro' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
					'categories' => [
						TagsModule::MEDIA_CATEGORY,
					],
				],
				'media_type' => 'video',
				'condition' => [
					'type' => 'hosted',
					'is_external_url' => '',
				],
			]
		);

		$repeater->add_control(
			'external_url',
			[
				'label' => __( 'URL', 'elementor-pro' ),
				'type' => Controls_Manager::URL,
				'autocomplete' => false,
				'options' => false,
				'label_block' => true,
				'show_label' => false,
				'dynamic' => [
					'active' => true,
					'categories' => [
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					],
				],
				'media_type' => 'video',
				'placeholder' => __( 'Enter your URL', 'elementor-pro' ),
				'condition' => [
					'type' => 'hosted',
					'is_external_url' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'title',
			[
				'label' => __( 'Title', 'elementor-pro' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __( 'Title', 'elementor-pro' ),
				'placeholder' => __( 'Add Your Text Here', 'elementor-pro' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'duration',
			[
				'label' => __( 'Duration', 'elementor-pro' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => '1:05',
				'default' => '',
				'condition' => [
					'type' => [ 'youtube', 'hosted' ],
				],
			]
		);

		$repeater->add_control(
			'thumbnail',
			[
				'label' => __( 'Thumbnail', 'elementor-pro' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'type' => [ 'youtube', 'hosted' ],
				],
			]
		);

		$repeater->add_control(
			'inner_tab_is_content_visible',
			[
				'label' => __( 'Contents Tabs ', 'elementor-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'elementor-pro' ),
				'label_off' => __( 'Hide', 'elementor-pro' ),
				'return_value' => 'show',
				'default' => '',
				'condition' => [
					'type' => [ 'youtube', 'hosted' ],
				],
			]
		);

		$repeater->start_controls_tabs( 'video_tabs' );

		$repeater->start_controls_tab(
			'inner_tab_1',
			[
				'label' => __( 'Tab #1', 'elementor-pro' ),
				'condition' => [
					'type' => [ 'youtube', 'hosted' ],
					'inner_tab_is_content_visible' => 'show',
				],
			]
		);

		$repeater->add_control(
			'inner_tab_content_1',
			[
				'label' => '',
				'type' => Controls_Manager::WYSIWYG,
				'default' => '<p>' . __( 'Add some content for each one of your videos, like a description, transcript or external links.To add, remove or edit tab names, go to Tabs.', 'elementor-pro' ) . '</p>',
				'condition' => [
					'type' => [ 'youtube', 'hosted' ],
					'inner_tab_is_content_visible' => 'show',
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'inner_tab_2',
			[
				'label' => __( 'Tab #2', 'elementor-pro' ),
				'condition' => [
					'type' => [ 'youtube', 'hosted' ],
					'inner_tab_is_content_visible' => 'show',
				],
			]
		);

		$repeater->add_control(
			'inner_tab_content_2',
			[
				'label' => '',
				'type' => Controls_Manager::WYSIWYG,
				'condition' => [
					'type' => [ 'youtube', 'hosted' ],
					'inner_tab_is_content_visible' => 'show',
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control(
			'tabs',
			[
				'label' => __( 'Playlist Items', 'elementor-pro' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'title' => __( 'Sample Video', 'elementor-pro' ),
						'youtube_url' => 'https://www.youtube.com/watch?v=XHOmBV4js_E',
						'duration' => '0:16',
						'thumbnail' => [ 'url' => 'http://img.youtube.com/vi/XHOmBV4js_E/maxresdefault.jpg' ],

					],
					[
						'title' => __( 'Sample Video', 'elementor-pro' ),
						'youtube_url' => 'https://www.youtube.com/watch?v=XHOmBV4js_E',
						'duration' => '0:16',
						'thumbnail' => [ 'url' => 'http://img.youtube.com/vi/XHOmBV4js_E/maxresdefault.jpg' ],

					],
					[
						'title' => __( 'Sample Video', 'elementor-pro' ),
						'youtube_url' => 'https://www.youtube.com/watch?v=XHOmBV4js_E',
						'duration' => '0:16',
						'thumbnail' => [ 'url' => 'http://img.youtube.com/vi/XHOmBV4js_E/maxresdefault.jpg' ],

					],
				],
				'frontend_available' => true,
				'title_field' => '{{{ title }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_inner_tab',
			[
				'label' => __( 'Tabs', 'elementor-pro' ),
			]
		);

		$this->add_control(
			'inner_tab_title_1',
			[
				'label' => __( 'Tab 1 Name', 'elementor-pro' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Tab #1', 'elementor-pro' ),
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'Name', 'elementor-pro' ),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'inner_tab_title_2',
			[
				'label' => __( 'Tab 2 Name', 'elementor-pro' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Tab #2', 'elementor-pro' ),
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'Name', 'elementor-pro' ),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'inner_tab_is_content_collapsible',
			[
				'label' => __( 'Collapsible', 'elementor-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'elementor-pro' ),
				'label_off' => __( 'Hide', 'elementor-pro' ),
				'return_value' => 'collapsible',
				'default' => '',
				'separator' => 'before',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'inner_tab_label_show_more',
			[
				'label' => __( 'Read More Label', 'elementor-pro' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'Show More', 'elementor-pro' ),
				'default' => __( 'Show More', 'elementor-pro' ),
				'condition' => [
					'inner_tab_is_content_collapsible' => 'collapsible',
				],
			]
		);

		$this->add_control(
			'inner_tab_label_show_less',
			[
				'label' => __( 'Read Less Label', 'elementor-pro' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'Show Less', 'elementor-pro' ),
				'default' => __( 'Show Less', 'elementor-pro' ),
				'condition' => [
					'inner_tab_is_content_collapsible' => 'collapsible',
				],
			]
		);

		$this->add_responsive_control(
			'inner_tab_collapsible_height',
			[
				'label' => __( 'Height', 'elementor-pro' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => '54',
				],
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 54,
						'max' => 500,
					],
				],
				'render_type' => 'template',
				'selectors' => [
					'{{WRAPPER}} .collapsible .e-inner-tab-text' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'inner_tab_is_content_collapsible' => 'collapsible',
				],
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_image_overlay',
			[
				'label' => __( 'Image Overlay', 'elementor-pro' ),
			]
		);

		$this->add_control(
			'show_image_overlay',
			[
				'label' => __( 'Image Overlay', 'elementor-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'elementor-pro' ),
				'label_on' => __( 'Show', 'elementor-pro' ),
			]
		);

		$this->add_control(
			'image_overlay',
			[
				'label' => __( 'Choose Image', 'elementor-pro' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'show_image_overlay' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'image_overlay',
				'default' => 'full',
				'separator' => 'none',
				'condition' => [
					'show_image_overlay' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_play_icon',
			[
				'label' => __( 'Play Icon', 'elementor-pro' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => [
					'value' => 'far fa-play-circle',
					'library' => 'fa-regular',
				],
				'label_block' => false,
				'skin' => 'inline',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_additional_options',
			[
				'label' => __( 'Additional Options', 'elementor-pro' ),
			]
		);

		$this->add_control(
			'tabs_alignment',
			[
				'label' => __( 'Layout', 'elementor-pro' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'right',
				'options' => [
					'start' => [
						'title' => __( 'Left', 'elementor-pro' ),
						'icon' => 'eicon-h-align-left',
					],
					'end' => [
						'title' => __( 'Right', 'elementor-pro' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'prefix_class' => 'elementor-layout-',
			]
		);

		$this->add_control(
			'heading_autoplay',
			[
				'label' => __( 'Autoplay', 'elementor-pro' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'autoplay_on_load',
			[
				'label' => __( 'On Load', 'elementor-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'elementor-pro' ),
				'label_off' => __( 'Hide', 'elementor-pro' ),
				'frontend_available' => true,
			]
		);
		$this->add_control(
			'autoplay_next',
			[
				'label' => __( 'Next Up', 'elementor-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'elementor-pro' ),
				'label_off' => __( 'Hide', 'elementor-pro' ),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'show_watched_indication',
			[
				'label' => __( 'Indicate Watched', 'elementor-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'show_video_count',
			[
				'label' => __( 'Video Count', 'elementor-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_duration',
			[
				'label' => __( 'Duration', 'elementor-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'elementor-pro' ),
				'label_off' => __( 'Hide', 'elementor-pro' ),
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_thumbnail',
			[
				'label' => __( 'Thumbnails', 'elementor-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'elementor-pro' ),
				'label_off' => __( 'Hide', 'elementor-pro' ),
				'default' => 'yes',
			]
		);

		$this->add_control(
			'play_icon',
			[
				'label' => __( 'Play Icon', 'elementor-pro' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => [
					'value' => 'far fa-play-circle',
					'library' => 'fa-regular',
				],
				'label_block' => false,
				'skin' => 'inline',
			]
		);

		$this->add_control(
			'watched_icon',
			[
				'label' => __( 'Watched Icon', 'elementor-pro' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => [
					'value' => 'far fa-check-circle',
					'library' => 'fa-regular',
				],
				'label_block' => false,
				'skin' => 'inline',
			]
		);

		$this->add_control(
			'lazy_load',
			[
				'label' => __( 'Lazy Load', 'elementor-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'return_value' => true,
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_layout',
			[
				'label' => __( 'Layout', 'elementor-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'layout_height',
			[
				'label' => __( 'Height', 'elementor-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh', 'vw' ],
				'range' => [
					'px' => [
						'min' => 200,
						'max' => 1200,
					],
					'vh' => [
						'min' => 10,
						'max' => 100,
					],
					'vw' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .e-tabs .e-tabs-main-area' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_top_bar',
			[
				'label' => __( 'Top Bar', 'elementor-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_playlist_name',
			[
				'label' => __( 'Playlist Name', 'elementor-pro' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'playlist_name_background',
			[
				'label' => __( 'Background', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-tabs-header' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'playlist_name_color',
			[
				'label' => __( 'Color', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .e-tabs-header .e-tabs-title' => 'color: {{VALUE}};',
				],
				'global' => [
					'default' => Global_Colors::COLOR_TEXT,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'playlist_name_typography',
				'selector' => '{{WRAPPER}} .e-tabs-header .e-tabs-title',
			]
		);

		$this->add_control(
			'heading_videos_amount',
			[
				'label' => __( 'Videos Amount', 'elementor-pro' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'videos_amount_color',
			[
				'label' => __( 'Color', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .e-tabs-header .e-tabs-videos-count' => 'color: {{VALUE}};',
				],
				'global' => [
					'default' => Global_Colors::COLOR_TEXT,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'videos_amount_typography',
				'selector' => '{{WRAPPER}} .e-tabs-header .e-tabs-videos-count',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_videos',
			[
				'label' => __( 'Videos', 'elementor-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'playlist_tabs' );

		$this->start_controls_tab(
			'playlist_tabs_normal',
			[
				'label' => __( 'Normal', 'elementor-pro' ),
			]
		);

		$this->add_control(
			'heading_tab_normal',
			[
				'label' => __( 'Item', 'elementor-pro' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'normal_background',
			[
				'label' => __( 'Background', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-tab-title' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .e-tabs-items-wrapper' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .e-tabs-items-wrapper .shadow-bottom' => 'background: linear-gradient(180deg, transparent 0%, {{VALUE}} 100%)',
					'{{WRAPPER}} .e-tabs-items-wrapper .shadow-top' => 'background: linear-gradient(0deg, transparent 0%, {{VALUE}} 100%);',
				],
			]
		);

		$this->add_control(
			'normal_color',
			[
				'label' => __( 'Color', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-tab-title .e-tab-title-text' => 'color: {{VALUE}};',
					'{{WRAPPER}} .e-tab-title .e-tab-title-text a' => 'color: {{VALUE}};',
				],
				'global' => [
					'default' => Global_Colors::COLOR_TEXT,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'normal_typography',
				'selector' => '{{WRAPPER}} .e-tab-title .e-tab-title-text',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_control(
			'heading_duration_normal',
			[
				'label' => __( 'Duration', 'elementor-pro' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'normal_duration_color',
			[
				'label' => __( 'Color', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-tab-title .e-tab-duration' => 'color: {{VALUE}};',
				],
				'global' => [
					'default' => Global_Colors::COLOR_TEXT,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'normal_duration_typography',
				'selector' => '{{WRAPPER}} .e-tab-title .e-tab-duration',
			]
		);

		$this->add_control(
			'heading_icon_normal',
			[
				'label' => __( 'Icon', 'elementor-pro' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'normal_icon_color',
			[
				'label' => __( 'Color', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-tab-title i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .e-tab-title svg' => 'color: {{VALUE}};',
					'{{WRAPPER}} .e-tab-title svg path' => 'fill: {{VALUE}};',
				],
			]
		);

		// Default shadow values for the icon.
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'normal_icon_top_text_shadow',
				'fields_options' => [
					'text_shadow_type' => [
						'label' => _x( 'Shadow', 'Text Shadow Control', 'elementor-pro' ),
					],
				],
				'selector' => '{{WRAPPER}} .e-tab-title i, {{WRAPPER}} .e-tab-title svg',
			]
		);

		$this->add_responsive_control(
			'normal_icon_size',
			[
				'label' => __( 'Size', 'elementor-pro' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => '16',
				],
				'range' => [
					'min' => 10,
					'max' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .e-tab-title i' => 'font-size: {{SIZE}}px',
					'{{WRAPPER}} .e-tab-title svg' => 'width: {{SIZE}}px;height: {{SIZE}}px;',
				],
			]
		);

		$this->add_control(
			'heading_separator_normal',
			[
				'label' => __( 'Separator', 'elementor-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'normal_separator_style',
			[
				'label' => __( 'Style', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => __( 'None', 'elementor-pro' ),
					'solid' => _x( 'Solid', 'Border Control', 'elementor-pro' ),
					'double' => _x( 'Double', 'Border Control', 'elementor-pro' ),
					'dotted' => _x( 'Dotted', 'Border Control', 'elementor-pro' ),
					'dashed' => _x( 'Dashed', 'Border Control', 'elementor-pro' ),
					'groove' => _x( 'Groove', 'Border Control', 'elementor-pro' ),
				],
				'selectors' => [
					'{{WRAPPER}} .e-tab-title' => 'border-style: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'normal_separator_weight',
			[
				'label' => __( 'Weight', 'elementor-pro' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'min' => 0,
					'max' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .e-tab-title' => 'border-width: 0 0 {{SIZE}}px 0;',
				],
				'condition' => [
					'normal_separator_style!' => '',
				],
			]
		);

		$this->add_control(
			'normal_separator_color',
			[
				'label' => __( 'Color', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-tab-title' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'normal_separator_style!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'playlist_tabs_active',
			[
				'label' => __( 'Active', 'elementor-pro' ),
			]
		);

		$this->add_control(
			'heading_tab_active',
			[
				'label' => __( 'Item', 'elementor-pro' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'active_background',
			[
				'label' => __( 'Background', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-tabs-items-wrapper .e-tabs-items .e-tab-title:where( .e-active, :hover )' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'active_color',
			[
				'label' => __( 'Color', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#556068',
				'selectors' => [
					'{{WRAPPER}} .e-tabs-items-wrapper .e-tab-title:where( .e-active, :hover ) .e-tab-title-text' => 'color: {{VALUE}};',
					'{{WRAPPER}} .e-tabs-items-wrapper .e-tab-title:where( .e-active, :hover ) .e-tab-title-text a' => 'color: {{VALUE}};',
				],
				'global' => [
					'default' => Global_Colors::COLOR_TEXT,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'active_typography',
				'selector' => '{{WRAPPER}} .e-tabs-items-wrapper .e-tab-title:where( .e-active, :hover ) .e-tab-title-text',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_control(
			'heading_duration_active',
			[
				'label' => __( 'Duration', 'elementor-pro' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'active_duration_color',
			[
				'label' => __( 'Color', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .e-tabs-items-wrapper .e-tab-title:where( .e-active, :hover ) .e-tab-duration' => 'color: {{VALUE}};',
				],
				'global' => [
					'default' => Global_Colors::COLOR_TEXT,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'active_duration_typography',
				'selector' => '{{WRAPPER}} .e-tabs-items-wrapper .e-tab-title:where( .e-active, :hover ) .e-tab-duration',
			]
		);

		$this->add_control(
			'heading_icon_active',
			[
				'label' => __( 'Icon', 'elementor-pro' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'active_icon_color',
			[
				'label' => __( 'Color', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-tabs-items-wrapper .e-tab-title:where( .e-active, :hover ) i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .e-tabs-items-wrapper .e-tab-title:where( .e-active, :hover ) svg' => 'color: {{VALUE}};',
					'{{WRAPPER}} .e-tabs-items-wrapper .e-tab-title:where( .e-active, :hover ) svg path' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'active_icon_top_text_shadow',
				'fields_options' => [
					'text_shadow_type' => [
						'label' => _x( 'Shadow', 'Text Shadow Control', 'elementor-pro' ),
					],
				],
				'selector' => '{{WRAPPER}} .e-tab-title:where( .e-active, :hover ) i, {{WRAPPER}} .e-tab-title:where( .e-active, :hover ) svg',
			]
		);

		$this->add_responsive_control(
			'active_icon_size',
			[
				'label' => __( 'Size', 'elementor-pro' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => '16',
				],
				'range' => [
					'min' => 10,
					'max' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .e-tab-title:where( .e-active, :hover ) span i' => 'font-size: {{SIZE}}px',
					'{{WRAPPER}} .e-tab-title:where( .e-active, :hover ) span svg' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
				],
			]
		);

		$this->add_control(
			'heading_separator_active',
			[
				'label' => __( 'Separator', 'elementor-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'active_separator_style',
			[
				'label' => __( 'Style', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => __( 'None', 'elementor-pro' ),
					'solid' => _x( 'Solid', 'Border Control', 'elementor-pro' ),
					'double' => _x( 'Double', 'Border Control', 'elementor-pro' ),
					'dotted' => _x( 'Dotted', 'Border Control', 'elementor-pro' ),
					'dashed' => _x( 'Dashed', 'Border Control', 'elementor-pro' ),
					'groove' => _x( 'Groove', 'Border Control', 'elementor-pro' ),
				],
				'selectors' => [
					'{{WRAPPER}} .e-tabs-items-wrapper .e-tab-title.e-active' => 'border-style: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'active_separator_weight',
			[
				'label' => __( 'Weight', 'elementor-pro' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'min' => 0,
					'max' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .e-tabs-items-wrapper .e-tab-title.e-active' => 'border-width: 0 0 {{SIZE}}px 0;',
				],
				'condition' => [
					'active_separator_style!' => '',
				],
			]
		);

		$this->add_control(
			'active_separator_color',
			[
				'label' => __( 'Color', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-tabs-items-wrapper .e-tabs-items .e-tab-title.e-active' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'active_separator_style!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_sections',
			[
				'label' => __( 'Sections', 'elementor-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_section',
			[
				'label' => __( 'Section', 'elementor-pro' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'section_background',
			[
				'label' => __( 'Background', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-tabs-items-wrapper .e-section-title' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'section_color',
			[
				'label' => __( 'Color', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .e-tabs-items-wrapper .e-section-title' => 'color: {{VALUE}};',
				],
				'global' => [
					'default' => Global_Colors::COLOR_TEXT,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'section_typography',
				'selector' => '{{WRAPPER}} .e-tabs-items-wrapper .e-section-title',
			]
		);

		$this->add_control(
			'section_border_type',
			[
				'label' => __( 'Border Type', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
					'' => __( 'None', 'elementor-pro' ),
					'solid' => _x( 'Solid', 'Border Control', 'elementor-pro' ),
					'double' => _x( 'Double', 'Border Control', 'elementor-pro' ),
					'dotted' => _x( 'Dotted', 'Border Control', 'elementor-pro' ),
					'dashed' => _x( 'Dashed', 'Border Control', 'elementor-pro' ),
					'groove' => _x( 'Groove', 'Border Control', 'elementor-pro' ),
				],
				'selectors' => [
					'{{WRAPPER}} .e-tabs-items-wrapper .e-section-title' => 'border-style: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'section_border_width',
			[
				'label' => __( 'Width', 'elementor-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .e-tabs-items-wrapper .e-section-title' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'section_border_color',
			[
				'label' => __( 'Color', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-tabs-items-wrapper .e-section-title' => 'border-color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_inner_tab_style',
			[
				'label' => __( 'Tabs', 'elementor-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'inner_tab_border_width',
			[
				'label' => __( 'Border Width', 'elementor-pro' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'min' => 0,
					'max' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .e-tabs-inner-tabs .e-inner-tabs-wrapper' => '--inner-tabs-border-height: {{SIZE}}px;',
					'{{WRAPPER}} .e-tabs-inner-tabs .e-inner-tabs-wrapper .e-inner-tab-title.e-inner-tab-active' => 'border-width: 0 0 {{SIZE}}px 0;',
				],
			]
		);

		$this->add_control(
			'inner_tab_border_color',
			[
				'label' => __( 'Border Color', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-tabs-inner-tabs .e-inner-tabs-wrapper' => '--inner-tabs-border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'inner_tab_background_color',
			[
				'label' => __( 'Background Color', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-tabs-inner-tabs .e-inner-tabs-wrapper .e-inner-tab-active' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .e-tabs-inner-tabs .e-inner-tabs-content-wrapper .e-inner-tab-content' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .e-tabs-inner-tabs .e-inner-tabs-content-wrapper .e-inner-tab-title' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'heading_inner_tab_title',
			[
				'label' => __( 'Title', 'elementor-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'inner_tab_title_color',
			[
				'label' => __( 'Color', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-tabs-inner-tabs .e-inner-tabs-wrapper .e-inner-tab-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'inner_tab_active_title_color',
			[
				'label' => __( 'Active Color', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-tabs-inner-tabs .e-inner-tabs-wrapper .e-inner-tab-title.e-inner-tab-active a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .e-tabs-inner-tabs .e-inner-tabs-wrapper .e-inner-tab-title.e-inner-tab-active' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'inner_tab_title_typography',
				'selector' => '{{WRAPPER}} .e-tabs-inner-tabs .e-inner-tabs-wrapper .e-inner-tab-title a',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_control(
			'heading_inner_tab_content',
			[
				'label' => __( 'Content', 'elementor-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'inner_tab_content_color',
			[
				'label' => __( 'Color', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-tabs-inner-tabs .e-inner-tabs-content-wrapper .e-inner-tab-content .e-inner-tab-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'inner_tab_content_typography',
				'selector' => '{{WRAPPER}} .e-tabs-inner-tabs .e-inner-tabs-content-wrapper .e-inner-tab-content .e-inner-tab-text',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_responsive_control(
			'inner_tab_content_padding',
			[
				'label' => __( 'Padding', 'elementor-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .e-tabs-inner-tabs .e-inner-tabs-content-wrapper .e-inner-tab-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'heading_inner_tab_show_more',
			[
				'label' => __( 'Show More', 'elementor-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs( 'inner_tab_show_more_color' );

		$this->start_controls_tab(
			'inner_tab_normal_show_more',
			[
				'label' => __( 'Normal', 'elementor-pro' ),
			]
		);

		$this->add_control(
			'inner_tab_normal_show_more_color',
			[
				'label' => __( 'Color', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-tabs-inner-tabs .e-inner-tabs-content-wrapper .e-inner-tab-content button' => 'color: {{VALUE}};',
				],
				'global' => [
					'default' => Global_Colors::COLOR_TEXT,
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'inner_tab_hover_show_more',
			[
				'label' => __( 'Hover', 'elementor-pro' ),
			]
		);

		$this->add_control(
			'inner_tab_hover_show_more_color',
			[
				'label' => __( 'Color', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-tabs-inner-tabs .e-inner-tabs-content-wrapper .e-inner-tab-content button:hover' => 'color: {{VALUE}};',
				],
				'global' => [
					'default' => Global_Colors::COLOR_TEXT,
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'inner_tab_show_more_typography',
				'selector' => '{{WRAPPER}} .e-tabs-inner-tabs .e-inner-tabs-content-wrapper .e-inner-tab-content button',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
			]
		);

		$this->end_controls_section();
	}

	private function create_playlist_item_tabs_array( $playlist_item, $playlist_item_index, $tab_1_title, $tab_2_title ) {
		$playlist_item_tabs_array = [];
		$playlist_item_tabs_content = [];
		$playlist_item_tabs_title = [];
		$is_already_activated_tab = false;

		if ( $playlist_item['inner_tab_content_1'] ) {
			$playlist_item_tabs_content[] = $playlist_item['inner_tab_content_1'];
			$playlist_item_tabs_title[] = $tab_1_title;
		}

		if ( $playlist_item['inner_tab_content_2'] ) {
			$playlist_item_tabs_content[] = $playlist_item['inner_tab_content_2'];
			$playlist_item_tabs_title[] = $tab_2_title;
		}

		foreach ( $playlist_item_tabs_content as $index => $playlist_item_tab_content ) {
			$playlist_item_tab_content_object = new \stdClass();
			$playlist_item_tab_content_object->tab_title = $playlist_item_tabs_title[ $index ];
			$playlist_item_tab_content_object->tab_content = $playlist_item_tab_content;

			if ( $playlist_item_tab_content ) {
				$playlist_item_tab_content_object->tab_attribute = ! $is_already_activated_tab ? '' : 'hidden';
				$playlist_item_tab_content_object->tab_class = ! $is_already_activated_tab ? 'e-inner-tab-active' : '';
				$is_already_activated_tab = true;

				$tab_index = $index + 1;
				$playlist_item_tab_content_object->tab_content_setting_key = $this->get_repeater_setting_key( 'tab_content_' . $tab_index, 'items', $playlist_item_index );

			}

			$playlist_item_tabs_array [] = $playlist_item_tab_content_object;
		}

		return $playlist_item_tabs_array;
	}

	private function create_playlist_item_video_attributes( $index, $id_int, $playlist_item, $video_url = '' ) {
		$html_attributes_object = new \stdClass();
		$tab_count = $index + 1;

		// The first tab will be activated and not hidden like the second tab.
		$hidden = 1 === $tab_count ? false : 'hidden';

		$html_attributes_object->attributes = [
			'id' => 'e-tab-content-' . $id_int . $tab_count,
			'class' => [ 'e-tab-content', 'elementor-clearfix' ],
			'data-tab' => $tab_count,
			'role' => 'tabpanel',
			'aria-labelledby' => 'e-tab-title-' . $id_int . $tab_count,
			'tabindex' => '0',
			'data-video-url' => $video_url,
			'data-video-type' => $playlist_item['type'],
			'data-video-title' => $playlist_item['title'],
			'data-video-duration' => $playlist_item['duration'],
		];

		if ( $hidden ) {
			$html_attributes_object->attributes['hidden'] = $hidden;
		}

		return $html_attributes_object;
	}

	private function create_playlist_item_title_attributes( $index, $id_int ) {
		$html_attributes_object = new \stdClass();
		$tab_count = $index + 1;

		$html_attributes_object->attributes = [
			'id' => 'e-tab-title-' . $id_int . $tab_count,
			'class' => [ 'e-tab-title', 'e-tab-desktop-title' ],
			'aria-selected' => 1 === $tab_count ? 'true' : 'false',
			'data-tab' => $tab_count,
			'role' => 'tab',
			'tabindex' => 1 === $tab_count ? '0' : '-1',
			'aria-controls' => 'e-tab-content-' . $id_int . $tab_count,
		];

		return $html_attributes_object;
	}

	private function create_playlist_items_array( $playlist_items ) {
		$playlist_items_array = array();
		$id_int = substr( $this->get_id_int(), 0, 3 );

		foreach ( $playlist_items as $index => $playlist_item ) {
			$playlist_item_object = new \stdClass();
			$playlist_item_object->video_url = '';
			$playlist_item_object->show_overlay_image = false;
			$playlist_item_object->is_inner_tabs_visible = $playlist_item['inner_tab_is_content_visible'];

			switch ( $playlist_item['type'] ) {
				case 'youtube':
				case 'hosted':
					$playlist_item_object->type = $playlist_item['type'];
					$playlist_item_object->video_title = $playlist_item['title'];

					if ( $playlist_item['youtube_url'] && 'youtube' === $playlist_item['type'] ) {
						$playlist_item_object->video_url = $playlist_item['youtube_url'];
					} elseif ( $playlist_item['external_url'] && 'hosted' === $playlist_item['type'] && 'yes' === $playlist_item['is_external_url'] ) {
						$playlist_item_object->video_url = $playlist_item['external_url']['url'];
					} elseif ( $playlist_item['hosted_url'] && 'hosted' === $playlist_item['type'] && 'yes' !== $playlist_item['is_external_url'] ) {
						$playlist_item_object->video_url = $playlist_item['hosted_url']['url'];
					}

					$playlist_item_object->tab_collapsible = $this->get_settings_for_display( 'inner_tab_is_content_collapsible' );
					$playlist_item_object->read_more_label = $this->get_settings_for_display( 'inner_tab_label_show_more' );
					$playlist_item_object->read_less_label = $this->get_settings_for_display( 'inner_tab_label_show_less' );

					$playlist_item_object->video_duration = '';
					if ( $this->get_settings_for_display( 'show_duration' ) ) {
						$playlist_item_object->video_duration = $playlist_item['duration'];
					}

					$playlist_item_object->video_thumbnail = '';
					if ( $this->get_settings_for_display( 'show_thumbnail' ) ) {
						$playlist_item_object->video_thumbnail = $playlist_item['thumbnail']['url'];
					}

					$playlist_item_object->html_attributes_title = $this->create_playlist_item_title_attributes( $index, $id_int );

					$playlist_item_object->tabs = [];
					if ( $playlist_item['inner_tab_content_1'] || $playlist_item['inner_tab_content_2'] ) {
						$tab_1_title = $this->get_settings_for_display( 'inner_tab_title_1' );
						$tab_2_title = $this->get_settings_for_display( 'inner_tab_title_2' );
						if ( $playlist_item_object->is_inner_tabs_visible ) {
							$playlist_item_object->tabs = $this->create_playlist_item_tabs_array( $playlist_item, $index, $tab_1_title, $tab_2_title );
						}
					}

					if ( 0 === $index && 'yes' !== $this->get_settings_for_display( 'autoplay_on_load' ) && $this->get_settings_for_display( 'show_image_overlay' ) ) {
						$playlist_item_object->show_overlay_image = true;
					}

					break;
				case 'section':
					$playlist_item_object->type = $playlist_item['type'];
					$playlist_item_object->section_title = $playlist_item['title'];
					$playlist_item_object->video_title = '';
					break;
			}

			$playlist_item_object->html_attributes_video = $this->create_playlist_item_video_attributes( $index, $id_int, $playlist_item, $playlist_item_object->video_url );

			$playlist_items_array [] = $playlist_item_object;
		}

		return $playlist_items_array;
	}

	private function count_video_items( $playlist_items ) {
		$filtered_playlist_items = $playlist_items;
		foreach ( $filtered_playlist_items as $key => $tab ) {
			if ( 'section' === $tab['type'] ) {
				unset( $filtered_playlist_items[ $key ] );
			}
		}
		return count( $filtered_playlist_items );
	}

	private function prepare_video_playlist_data_object() {
		$settings = $this->get_settings_for_display();
		$playlist_items = $settings['tabs'];

		$playlist_object = new \stdClass();
		$playlist_object->playlist_name = $settings['playlist_title'];
		$playlist_object->is_show_video_count = $settings['show_video_count'];

		if ( $playlist_object->is_show_video_count ) {
			$playlist_object->video_count = $this->count_video_items( $playlist_items );
		}

		$playlist_object->show_thumbnails = $settings['show_thumbnail'];
		$playlist_object->play_icon = $settings['play_icon'];
		$playlist_object->watched_icon = $settings['watched_icon'];
		$playlist_object->playlist_items = $this->create_playlist_items_array( $playlist_items );
		$playlist_object->is_image_overlay = $settings['show_image_overlay'];
		$playlist_object->image_overlay_icon = $settings['show_play_icon'];

		$image_overlay = $settings['image_overlay'];
		if ( ! empty( $image_overlay['url'] ) ) {
			$playlist_object->image_overlay_image = Group_Control_Image_Size::get_attachment_image_src( $image_overlay['id'], 'image_overlay', $settings );
			if ( ! $playlist_object->image_overlay_image[1] ) {
				$playlist_object->image_overlay_image = $image_overlay['url'];
			}
		}

		return $playlist_object;
	}

	protected function render() {
		$playlist_object = $this->prepare_video_playlist_data_object();
		?>

		<div class="e-tabs">
			<div class="e-tabs-main-area">
				<div class="e-tabs-wrapper">
					<div class="e-tabs-header">
						<h2 class="e-tabs-title"><?php echo $playlist_object->playlist_name; ?></h2>
						<div class="e-tabs-header-right-side">
							<?php if ( $playlist_object->is_show_video_count ) : ?>
								<span class="e-tabs-videos-count"><?php echo $playlist_object->video_count; ?> <?php echo __( 'Videos', 'elementor-pro' ); ?></span>
							<?php endif; ?>
							<i class="e-tabs-toggle-videos-display-button eicon-caret-down rotate-down" aria-hidden="true"></i>
						</div>
					</div>
					<div class="e-tabs-items-wrapper">
						<div class="e-tabs-items" role="tablist">
							<?php
							foreach ( $playlist_object->playlist_items as $item ) : ?>
								<?php if ( 'section' === $item->type ) : ?>
									<h3 class="e-section-title">
										<?php echo $item->section_title; ?>
									</h3>
								<?php else : ?>
									<div <?php echo Utils::render_html_attributes( $item->html_attributes_title->attributes ); ?>>
										<?php if ( $playlist_object->show_thumbnails ) : ?>
											<div class="e-tab-thumbnail">
												<?php if ( $item->video_thumbnail ) : ?>
													<img src="<?php echo $item->video_thumbnail; ?>" />
												<?php endif; ?>
												<span class="icon-play"><?php Icons_Manager::render_icon( $playlist_object->play_icon, [ 'aria-hidden' => 'true' ] ); ?></span>
												<span class="icon-watched"><?php Icons_Manager::render_icon( $playlist_object->watched_icon, [ 'aria-hidden' => 'true' ] ); ?></span>
											</div>
										<?php else : ?>
											<span class="icon-play"><?php Icons_Manager::render_icon( $playlist_object->play_icon, [ 'aria-hidden' => 'true' ] ); ?></span>
											<span class="icon-watched"><?php Icons_Manager::render_icon( $playlist_object->watched_icon, [ 'aria-hidden' => 'true' ] ); ?></span>
										<?php endif; ?>
										<h4 class="e-tab-title-text" title="<?php echo $item->video_title; ?>">
											<a href="">
												<?php echo $item->video_title; ?>
											</a>
										</h4>
										<?php if ( $item->video_duration ) : ?>
											<span class="e-tab-duration"><?php echo $item->video_duration; ?></span>
										<?php endif; ?>
									</div>
								<?php endif; ?>
							<?php endforeach; ?>
						</div>
						<div class="shadow shadow-top" aria-hidden="true"></div>
						<div class="shadow shadow-bottom" aria-hidden="true"></div>
					</div>
				</div>

				<div class="e-tabs-content-wrapper" role="tablist" aria-orientation="vertical">
					<?php foreach ( $playlist_object->playlist_items as $item ) : ?>

						<div <?php echo Utils::render_html_attributes( $item->html_attributes_video->attributes ); ?>>
							<div></div>
							<?php if ( $item->show_overlay_image ) : ?>
								<div class="elementor-custom-embed-image-overlay elementor-clickable" style="background-image: url('<?php echo $playlist_object->image_overlay_image; ?>');">
									<?php if ( ! empty( $playlist_object->image_overlay_icon['value'] ) ) : ?>
										<div class="elementor-custom-embed-play" role="button">
											<?php Icons_Manager::render_icon( $playlist_object->image_overlay_icon, [ 'aria-hidden' => 'true' ] ); ?>
											<span class="elementor-screen-only"><?php echo __( 'Play Video', 'elementor-pro' ); ?></span>
										</div>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="e-tabs-inner-tabs" >
				<?php foreach ( $playlist_object->playlist_items as $item ) : ?>
					<?php if ( $item->is_inner_tabs_visible ) : ?>
						<div <?php echo Utils::render_html_attributes( $item->html_attributes_video->attributes ); ?>>
							<?php if ( count( $item->tabs ) > 0 ) : ?>
								<div class="e-inner-tabs-wrapper">
									<?php foreach ( $item->tabs as $tab ) :
										if ( $tab->tab_content ) { ?>
											<div class="e-inner-tab-title <?php echo $tab->tab_class; ?>">
												<a href=""> <?php echo $tab->tab_title; ?> </a>
											</div>
										<?php } ?>
									<?php endforeach; ?>
								</div>
								<div class="e-inner-tabs-content-wrapper">
									<?php foreach ( $item->tabs as $tab ) :
										if ( $tab->tab_content ) { ?>
											<div class="e-inner-tab-title e-tab-mobile-title <?php echo $tab->tab_class; ?>">
												<?php echo $tab->tab_title; ?>
											</div>
											<div <?php echo $tab->tab_attribute; ?> class="e-inner-tab-content <?php echo $tab->tab_class; ?> <?php echo $item->tab_collapsible; ?>">
												<div class="e-inner-tab-text">
													<?php $this->add_inline_editing_attributes( $tab->tab_content_setting_key, 'advanced' ); ?>
													<div <?php echo $this->get_render_attribute_string( $tab->tab_content_setting_key ); ?>>
														<?php echo $this->parse_text_editor( $tab->tab_content ); ?>
													</div>
												</div>
												<div class="e-inner-tab-buttons">
													<button class="show-button"><?php echo $item->read_more_label; ?></button>
													<button><?php echo $item->read_less_label; ?></button>
												</div>
											</div>
										<?php } ?>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	protected function content_template() {
		?>
		<#

		function createPlaylistItemTabsArray( playlistItem, playlistItemIndex, tab1Title, tab2Title ) {
			var playlistItemTabsArray = [];
			var playlistItemTabsContent = [];
			var playlistItemTabsTitle = [];
			var isAlreadyActivatedTab = false;

			if ( playlistItem.inner_tab_content_1 ) {
				playlistItemTabsContent.push( playlistItem.inner_tab_content_1 );
				playlistItemTabsTitle.push( tab1Title );
			}

			if ( playlistItem.inner_tab_content_2 ) {
				playlistItemTabsContent.push( playlistItem.inner_tab_content_2 );
				playlistItemTabsTitle.push( tab2Title );
			}

			playlistItemTabsContent.forEach( function( playlistItemTabContent, index ) {
				var playlistItemTabContentObject = {};
				playlistItemTabContentObject.tab_title = playlistItemTabsTitle[ index ];
				playlistItemTabContentObject.tab_content = playlistItemTabContent;

				if ( playlistItemTabContent ) {
					playlistItemTabContentObject.tabAttribute = ! isAlreadyActivatedTab ? '' : 'hidden';
					playlistItemTabContentObject.tabClass = ! isAlreadyActivatedTab ? 'e-inner-tab-active' : '';
					isAlreadyActivatedTab = true;

					var tabIndex = index + 1;
					playlistItemTabContentObject.tabContentSettingKey = view.getRepeaterSettingKey( 'tab_content_' + tabIndex, 'items', playlistItemIndex );
				}

				playlistItemTabsArray.push(playlistItemTabContentObject);
			} );

			return playlistItemTabsArray;
		}

		function createPlaylistItemVideoAttributes( index, id, playlistItem, videoUrl ) {
			var videoUrl = videoUrl ? videoUrl : '';
			var htmlAttributesObject = {};
			var tabCount = index + 1;

			// The first tab will be activated and not hidden like the second tab.
			var hidden = 1 === tabCount ? false : 'hidden';

			htmlAttributesObject.attributes = {
				'id' : 'e-tab-content-' + id + tabCount,
				'class' : [ 'e-tab-content', 'elementor-clearfix' ],
				'data-tab' : tabCount,
				'role' : 'tabpanel',
				'aria-labelledby' : 'e-tab-title-' + id + tabCount,
				'tabindex' : '0',
				'data-video-url' : videoUrl,
				'data-video-type' : playlistItem.type,
				'data-video-title' : playlistItem.title,
				'data-video-duration' : playlistItem.duration,
			};

			if ( hidden ) {
				htmlAttributesObject.attributes.hidden = hidden;
			}

			return htmlAttributesObject;
		}

		function createPlaylistItemTitleAttributes( index, id ) {
			var htmlAttributesObject = {};
			var tabCount = index + 1;

			htmlAttributesObject.attributes = {
				'id' : 'e-tab-title-' + id + tabCount,
				'class' : [ 'e-tab-title', 'e-tab-desktop-title' ],
				'aria-selected' : 1 === tabCount ? 'true' : 'false',
				'data-tab' : tabCount,
				'role' : 'tab',
				'tabindex' : 1 === tabCount ? '0' : '-1',
				'aria-controls' : 'e-tab-content-' + id + tabCount,
			};

			return htmlAttributesObject;
		}

		function createPlaylistItemsArray( playlistItems ) {
			var playlistItemsArray = [];
			id = view.getIDInt().toString().substr( 0, 3 )

			playlistItems.forEach( function( playlistItem, index ) {
				var playlistItemObject = {};
				playlistItemObject.videoUrl = '';
				playlistItemObject.showOverlayImage = false;
				playlistItemObject.isInnerTabsVisible = playlistItem.inner_tab_is_content_visible;

				switch ( playlistItem.type ) {
					case 'youtube':
					case 'hosted':
						playlistItemObject.type = playlistItem.type;
						playlistItemObject.videoTitle = playlistItem.title;

						if ( playlistItem.youtube_url && 'youtube' === playlistItem.type ) {
							playlistItemObject.videoUrl = playlistItem.youtube_url;
						} else if ( playlistItem.external_url && 'hosted' === playlistItem.type && 'yes' === playlistItem.is_external_url ) {
							playlistItemObject.videoUrl = playlistItem.external_url.url;
						} else if ( playlistItem.hosted_url && 'hosted' === playlistItem.type && 'yes' !== playlistItem.is_external_url ) {
							playlistItemObject.videoUrl = playlistItem.hosted_url.url;
						}

						playlistItemObject.tabCollapsible = settings.inner_tab_is_content_collapsible;
						playlistItemObject.readMoreLabel = settings.inner_tab_label_show_more;
						playlistItemObject.readLessLabel = settings.inner_tab_label_show_less;

						playlistItemObject.videoDuration = '';
						if ( settings.show_duration ) {
							playlistItemObject.videoDuration = playlistItem.duration;
						}

						playlistItemObject.videoThumbnail = '';
						if ( settings.show_thumbnail ) {
							playlistItemObject.videoThumbnail = playlistItem.thumbnail.url;
						}

						playlistItemObject.htmlAttributesTitle = createPlaylistItemTitleAttributes( index, id );

						playlistItemObject.tabs = [];
						if ( playlistItem.inner_tab_content_1 || playlistItem.inner_tab_content_2 ) {
							var tab1Title = settings.inner_tab_title_1;
							var tab2Title = settings.inner_tab_title_2;
							if ( playlistItemObject.isInnerTabsVisible ) {
								playlistItemObject.tabs = createPlaylistItemTabsArray( playlistItem, index, tab1Title, tab2Title );
							}
						}

						if ( (0 === index) && (settings.autoplay_on_load !== 'yes') && settings.show_image_overlay ) {
							playlistItemObject.showOverlayImage = true;
						}

					break;
					case 'section':
						playlistItemObject.type = playlistItem.type;
						playlistItemObject.sectionTitle = playlistItem.title;
					break;
				}

				playlistItemObject.htmlAttributesVideo = createPlaylistItemVideoAttributes( index, id, playlistItem, playlistItemObject.videoUrl );

				playlistItemsArray.push(playlistItemObject);
			} );

			return playlistItemsArray;
		}

		function countVideoItems( playlistItems ) {
			var filteredPlaylistItems = playlistItems.filter(function(playlistItem) {
				return 'section' !== playlistItem.type
			});

			return filteredPlaylistItems.length;
		}

		function prepare_video_playlist_data_object() {
			var playlistItems = settings.tabs

			playlistObject = {};
			playlistObject.playlistName = settings.playlist_title;
			playlistObject.isShowVideoCount = settings.show_video_count;

			if( playlistObject.isShowVideoCount ) {
				playlistObject.videoCount = countVideoItems( playlistItems );
			}

			playlistObject.showThumbnails = settings.show_thumbnail;
			playlistObject.playIcon = settings.play_icon;
			playlistObject.watchedIcon = settings.watched_icon;
			playlistObject.playlistItems = createPlaylistItemsArray( playlistItems );
			playlistObject.isImageOverlay = !settings.autoplay_on_load && settings.show_image_overlay ? true : false;
			playlistObject.imageOverlayIcon = settings.show_play_icon;

			playlistObject.imageOverlayImage = '';
			imageOverlay = settings.image_overlay;
			if ( imageOverlay.url && settings.image_overlay.id && playlistObject.isImageOverlay ) {
				var image_overlay_object = {
				id: settings.image_overlay.id,
				url: settings.image_overlay.url,
				size: settings.image_overlay_size,
				dimension: settings.image_overlay_custom_dimension,
				model: view.getEditModel()
				};

				playlistObject.imageOverlayImage = elementor.imagesManager.getImageUrl( image_overlay_object );
			} else {
				playlistObject.imageOverlayImage = imageOverlay.url;
			}

			return playlistObject;
		}

		var playlistObject = prepare_video_playlist_data_object();

		var watchedIconHTML = elementor.helpers.renderIcon( view, playlistObject.watchedIcon, { 'aria-hidden': true }, 'i' , 'object' );
		var playIconHTML = elementor.helpers.renderIcon( view, playlistObject.playIcon, { 'aria-hidden': true }, 'i' , 'object' );
		var overlayImagePlayIconHTML = elementor.helpers.renderIcon( view, playlistObject.imageOverlayIcon, { 'aria-hidden': true }, 'i' , 'object' );
		#>
		<div class="e-tabs" role="tablist" aria-orientation="vertical">
			<div class="e-tabs-main-area">
				<div class="e-tabs-wrapper">
					<div class="e-tabs-header">
						<h2 class="e-tabs-title">{{{ playlistObject.playlistName }}}</h2>
						<div class="e-tabs-header-right-side">
							<# if ( playlistObject.isShowVideoCount ) { #>
								<span class="e-tabs-videos-count">{{{ playlistObject.videoCount }}} Videos</span>
							<# } #>
							<i class="e-tabs-toggle-videos-display-button eicon-caret-down rotate-down" aria-hidden="true"></i>
						</div>
					</div>
					<div class="e-tabs-items-wrapper">
						<div class="e-tabs-items" role="tablist">
							<# _.each( playlistObject.playlistItems, function( item, index ) { #>
								<# if ( 'section' === item.type ) { #>
									<h3 class="e-section-title">
										{{{ item.sectionTitle }}}
									</h3>
								<# } else { #>
									<#
										var tabTitleKey = item.htmlAttributesTitle.attributes.id;
										view.addRenderAttribute(tabTitleKey, item.htmlAttributesTitle.attributes);
									#>
									<div {{{ view.getRenderAttributeString( tabTitleKey ) }}}>
										<# if ( playlistObject.showThumbnails ) { #>
											<div class="e-tab-thumbnail">
												<# if ( item.videoThumbnail ) { #>
													<img src="{{{ item.videoThumbnail }}}" />
												<# } #>
												<span class="icon-play">{{{ playIconHTML.value }}}</span>
												<span class="icon-watched">{{{ watchedIconHTML.value }}}</span>
											</div>
										<# } else { #>
										<span class="icon-play">{{{ playIconHTML.value }}}</span>
										<span class="icon-watched">{{{ watchedIconHTML.value }}}</span>
										<# } #>
										<h4 class="e-tab-title-text" title="{{{ item.videoTitle }}}">
											<a href="">{{{ item.videoTitle }}}</a>
										</h4>
										<# if ( item.videoDuration ) { #>
										<span class="e-tab-duration">{{{ item.videoDuration }}}</span>
										<# } #>
									</div>
								<# } #>
							<# } ); #>
						</div>
						<div class="shadow shadow-top" aria-hidden="true"></div>
						<div class="shadow shadow-bottom" aria-hidden="true"></div>
					</div>
				</div>

				<div class="e-tabs-content-wrapper">
					<# _.each( playlistObject.playlistItems, function( item, index ) { #>
					<div class="e-tab-title e-tab-mobile-title" role="tab">{{{ item.title }}}</div>
					<#
						var tabContentKey = view.getRepeaterSettingKey( 'tab_content', 'tabs', index );
						view.addRenderAttribute(tabContentKey, item.htmlAttributesVideo.attributes);
					#>
					<div {{{ view.getRenderAttributeString( tabContentKey ) }}}>
						<div></div>
						<# if ( item.showOverlayImage ) { #>
						<div class="elementor-custom-embed-image-overlay elementor-clickable" style="background-image: url( {{ playlistObject.imageOverlayImage }} );">
							<# if ( playlistObject.imageOverlayIcon.value ) { #>
							<div class="elementor-custom-embed-play" role="button">
								{{{ overlayImagePlayIconHTML.value }}}
							</div>
							<# } #>
						</div>
						<# } #>
					</div>
					<# } ); #>
				</div>
			</div>

			<div class="e-tabs-inner-tabs">
				<# _.each( playlistObject.playlistItems, function( item, index ) { #>
					<# if ( item.isInnerTabsVisible ) { #>
					<#
						var tabContentKey = view.getRepeaterSettingKey( 'tab_content', 'inner-tabs', index );
						view.addRenderAttribute(tabContentKey, item.htmlAttributesVideo.attributes);
					#>
						<div {{{ view.getRenderAttributeString( tabContentKey ) }}}>
							<# if ( item.tabs.length > 0 ) { #>
								<div class="e-inner-tabs-wrapper">
									<# _.each( item.tabs, function( tab ) { #>
										<# if ( tab.tab_content ) { #>
											<div class="e-inner-tab-title {{{ tab.tabClass }}}">
												<a href=""> {{{ tab.tab_title }}} </a>
											</div>
										<# } #>
									<# }); #>
								</div>
								<div class="e-inner-tabs-content-wrapper">
									<# _.each( item.tabs, function( tab ) { #>
										<div class="e-inner-tab-title e-tab-mobile-title {{{ tab.tabClass }}}">
											{{{ tab.tab_title }}}
										</div>
										<div {{{ tab.tabAttribute }}} class="e-inner-tab-content {{{ tab.tabClass }}} {{{ item.tabCollapsible }}}">
											<div class="e-inner-tab-text">
												<div {{{ view.getRenderAttributeString( tab.tabContentSettingKey ) }}}>
													{{{ tab.tab_content }}}
												</div>
											</div>
											<div class="e-inner-tab-buttons">
												<button class="show-button">{{{ item.readMoreLabel }}}</button>
												<button>{{{ item.readLessLabel }}}</button>
											</div>
										</div>
									<# } ); #>
								</div>
							<# } #>
						</div>
					<# } #>
				<# } ); #>
			</div>
		</div>
		<?php
	}
}
