<?php
namespace ElementorPro\Modules\Forms\Actions;

use Elementor\Controls_Manager;
use ElementorPro\Modules\Forms\Classes\Ajax_Handler;
use ElementorPro\Modules\Forms\Classes\Form_Record;
use ElementorPro\Modules\Forms\Classes\Integration_Base;
use ElementorPro\Modules\Forms\Classes\Mailchimp_Handler;
use Elementor\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Mailchimp extends Integration_Base {

	const OPTION_NAME_API_KEY = 'pro_mailchimp_api_key';

	/**
	 * @var string - Mailchimp API key.
	 */
	private $api_key;

	private function get_global_api_key() {
		return get_option( 'elementor_' . self::OPTION_NAME_API_KEY );
	}

	public function get_name() {
		return 'mailchimp';
	}

	public function get_label() {
		return __( 'MailChimp', 'elementor-pro' );
	}

	public function register_settings_section( $widget ) {
		$widget->start_controls_section(
			'section_mailchimp',
			[
				'label' => __( 'MailChimp', 'elementor-pro' ),
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);

		self::global_api_control(
			$widget,
			$this->get_global_api_key(),
			'MailChimp API Key',
			[
				'mailchimp_api_key_source' => 'default',
			],
			$this->get_name()
		);

		$widget->add_control(
			'mailchimp_api_key_source',
			[
				'label' => __( 'API Key', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'options' => [
					'default' => 'Default',
					'custom' => 'Custom',
				],
				'default' => 'default',
			]
		);

		$widget->add_control(
			'mailchimp_api_key',
			[
				'label' => __( 'Custom API Key', 'elementor-pro' ),
				'type' => Controls_Manager::TEXT,
				'condition' => [
					'mailchimp_api_key_source' => 'custom',
				],
				'description' => __( 'Use this field to set a custom API Key for the current form', 'elementor-pro' ),
			]
		);

		$widget->add_control(
			'mailchimp_list',
			[
				'label' => __( 'Audience', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'options' => [],
				'render_type' => 'none',
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'mailchimp_api_key',
							'operator' => '!==',
							'value' => '',
						],
						[
							'name' => 'mailchimp_api_key_source',
							'operator' => '=',
							'value' => 'default',
						],
					],
				],
			]
		);

		$widget->add_control(
			'mailchimp_groups',
			[
				'label' => __( 'Groups', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT2,
				'options' => [],
				'label_block' => true,
				'multiple' => true,
				'render_type' => 'none',
				'condition' => [
					'mailchimp_list!' => '',
				],
			]
		);

		$widget->add_control(
			'mailchimp_tags',
			[
				'label' => __( 'Tags', 'elementor-pro' ),
				'description' => __( 'Add comma separated tags', 'elementor-pro' ),
				'type' => Controls_Manager::TEXT,
				'render_type' => 'none',
				'condition' => [
					'mailchimp_list!' => '',
				],
			]
		);

		$widget->add_control(
			'mailchimp_double_opt_in',
			[
				'label' => __( 'Double Opt-In', 'elementor-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'condition' => [
					'mailchimp_list!' => '',
				],
			]
		);

		$this->register_fields_map_control( $widget );

		$widget->end_controls_section();
	}

	public function on_export( $element ) {
		unset(
			$element['settings']['mailchimp_api_key_source'],
			$element['settings']['mailchimp_api_key'],
			$element['settings']['mailchimp_list'],
			$element['settings']['mailchimp_groups'],
			$element['settings']['mailchimp_fields_map']
		);

		return $element;
	}

	public function run( $record, $ajax_handler ) {
		$form_settings = $record->get( 'form_settings' );

		if ( 'default' === $form_settings['mailchimp_api_key_source'] ) {
			$this->api_key = $this->get_global_api_key();
		} else {
			$this->api_key = $form_settings['mailchimp_api_key'];
		}

		// Data from the form in the frontend.
		$subscriber_data = $this->map_fields( $record );

		// Create or update a subscriber.
		$subscriber = $this->create_or_update_subscriber( $subscriber_data, $form_settings );

		// Parse the Mailchimp tags.
		$tags = $this->parse_tags( $form_settings['mailchimp_tags'] );

		// Set the subscriber tags only if he doesn't have them already.
		if ( ! $this->subscriber_has_tags( $subscriber, $tags ) ) {
			$this->set_subscriber_tags( $subscriber, $tags );
		}
	}

	/**
	 * @param string $tags - List of comma separated tags from the forms settings ( i.e. 'tag-1, tag-2' ).
	 *
	 * @return array|string[] - Array of tags that were extracted from the input ( i.e. [ 'tag-1', 'tag-2' ] ).
	 */
	private function parse_tags( $tags ) {
		$parsed_tags = [];

		if ( ! empty( $tags ) ) {
			$parsed_tags = explode( ',', trim( $tags ) );

			// Remove empty tags.
			$parsed_tags = array_filter( $parsed_tags );

			// Trim tags.
			$parsed_tags = array_map( 'trim', $parsed_tags );
		}

		return $parsed_tags;
	}

	/**
	 * Determine if a subscriber has specific tags, and ONLY those tags.
	 *
	 * @param array $subscriber - Subscriber data from an API response.
	 * @param array $tags - List of tags to check ( i.e. [ 'tag-1', 'tag-2' ] ).
	 *
	 * @return bool
	 */
	private function subscriber_has_tags( array $subscriber, array $tags ) {
		// Extract current tags.
		$subscriber_tags = [];

		foreach ( $subscriber['tags'] as $tag ) {
			$subscriber_tags[] = $tag['name'];
		}

		return array_diff( $tags, $subscriber_tags ) === array_diff( $subscriber_tags, $tags );
	}

	/**
	 * Set Mailchimp subscriber tags.
	 *
	 * @param array $subscriber - Subscriber data from a create/update request.
	 * @param array $tags - List of tags to set.
	 *
	 * @return void
	 */
	private function set_subscriber_tags( array $subscriber, array $tags ) {
		// Build the request tags.
		$request_tags = [];

		// Set current tags to inactive.
		foreach ( $subscriber['tags'] as $tag ) {
			$request_tags[] = [
				'name' => $tag['name'],
				'status' => 'inactive',
			];
		}

		// Set new tags to active.
		foreach ( $tags as $tag ) {
			$request_tags[] = [
				'name' => $tag,
				'status' => 'active',
			];
		}

		// Send the API request.
		$endpoint = sprintf( 'lists/%s/members/%s/tags', $subscriber['list_id'], md5( strtolower( $subscriber['email_address'] ) ) );
		$args = [
			'tags' => $request_tags,
		];

		$handler = new Mailchimp_Handler( $this->api_key );
		$response = $handler->post( $endpoint, $args );

		if ( 204 !== $response['code'] ) {
			$error = ! empty( $response['body']['detail'] ) ? $response['body']['detail'] : '';
			$code = $response['code'];

			throw new \Exception( "HTTP {$code} - {$error}" );
		}
	}

	/**
	 * Create or update a Mailchimp subscriber.
	 *
	 * @param array $subscriber - Subscriber data from the form in the frontend.
	 * @param array $form_settings - Settings from the editor.
	 *
	 * @return array - An array that contains the newly created subscriber's data.
	 */
	private function create_or_update_subscriber( array $subscriber, array $form_settings ) {
		if ( ! empty( $form_settings['mailchimp_groups'] ) ) {
			$subscriber['interests'] = [];
		}

		if ( is_array( $form_settings['mailchimp_groups'] ) ) {
			foreach ( $form_settings['mailchimp_groups'] as $mailchimp_group ) {
				$subscriber['interests'][ $mailchimp_group ] = true;
			}
		}

		if ( ! empty( $form_settings['mailchimp_tags'] ) ) {
			$subscriber['tags'] = explode( ',', trim( $form_settings['mailchimp_tags'] ) );
		}

		$handler = new Mailchimp_Handler( $this->api_key );

		$subscriber['status_if_new'] = 'yes' === $form_settings['mailchimp_double_opt_in'] ? 'pending' : 'subscribed';
		$subscriber['status'] = 'subscribed';

		$end_point = sprintf( 'lists/%s/members/%s', $form_settings['mailchimp_list'], md5( strtolower( $subscriber['email_address'] ) ) );

		$response = $handler->post( $end_point, $subscriber, [
			'method' => 'PUT', // Add or Update
		] );

		if ( 200 !== $response['code'] ) {
			$error = ! empty( $response['body']['detail'] ) ? $response['body']['detail'] : '';
			$code = $response['code'];

			throw new \Exception( "HTTP {$code} - {$error}" );
		}

		return $response['body'];
	}

	/**
	 * @param Form_Record $record
	 *
	 * @return array
	 */
	private function map_fields( $record ) {
		$subscriber = [];
		$fields = $record->get( 'fields' );

		// Other form has a field mapping
		foreach ( $record->get_form_settings( 'mailchimp_fields_map' ) as $map_item ) {
			if ( empty( $fields[ $map_item['local_id'] ]['value'] ) ) {
				continue;
			}

			$value = $fields[ $map_item['local_id'] ]['value'];
			if ( 'email' === $map_item['remote_id'] ) {
				$subscriber['email_address'] = $value;
			} else {
				$subscriber['merge_fields'][ $map_item['remote_id'] ] = $value;
			}
		}

		return $subscriber;
	}

	public function ajax_validate_api_token() {
		check_ajax_referer( self::OPTION_NAME_API_KEY, '_nonce' );
		if ( ! isset( $_POST['api_key'] ) ) {
			wp_send_json_error();
		}
		try {
			new Mailchimp_Handler( $_POST['api_key'] );
		} catch ( \Exception $exception ) {
			wp_send_json_error();
		}
		wp_send_json_success();
	}

	/**
	 * @param array $data
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function handle_panel_request( array $data ) {
		if ( ! empty( $data['use_global_api_key'] ) && 'default' === $data['use_global_api_key'] ) {
			$api_key = $this->get_global_api_key();
		} elseif ( ! empty( $data['api_key'] ) ) {
			$api_key = $data['api_key'];
		}

		if ( empty( $api_key ) ) {
			throw new \Exception( '`api_key` is required', 400 );
		}

		$handler = new Mailchimp_Handler( $api_key );

		if ( 'lists' === $data['mailchimp_action'] ) {
			return $handler->get_lists();
		}

		return $handler->get_list_details( $data['mailchimp_list'] );
	}

	public function register_admin_fields( Settings $settings ) {
		$settings->add_section( Settings::TAB_INTEGRATIONS, 'mailchimp', [
			'callback' => function() {
				echo '<hr><h2>' . esc_html__( 'MailChimp', 'elementor-pro' ) . '</h2>';
			},
			'fields' => [
				self::OPTION_NAME_API_KEY => [
					'label' => __( 'API Key', 'elementor-pro' ),
					'field_args' => [
						'type' => 'text',
						'desc' => sprintf( __( 'To integrate with our forms you need an <a href="%s" target="_blank">API Key</a>.', 'elementor-pro' ), 'https://kb.mailchimp.com/integrations/api-integrations/about-api-keys' ),
					],
				],
				'validate_api_data' => [
					'field_args' => [
						'type' => 'raw_html',
						'html' => sprintf( '<button data-action="%s" data-nonce="%s" class="button elementor-button-spinner" id="elementor_pro_mailchimp_api_key_button">%s</button>', self::OPTION_NAME_API_KEY . '_validate', wp_create_nonce( self::OPTION_NAME_API_KEY ), __( 'Validate API Key', 'elementor-pro' ) ),
					],
				],
			],
		] );
	}

	public function __construct() {
		if ( is_admin() ) {
			add_action( 'elementor/admin/after_create_settings/' . Settings::PAGE_ID, [ $this, 'register_admin_fields' ], 14 );
		}
		add_action( 'wp_ajax_' . self::OPTION_NAME_API_KEY . '_validate', [ $this, 'ajax_validate_api_token' ] );
	}

	protected function get_fields_map_control_options() {
		return [
			'condition' => [
				'mailchimp_list!' => '',
			],
		];
	}
}
