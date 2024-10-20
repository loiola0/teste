<?php
namespace ElementorPro\Modules\Forms\Classes;

use ElementorPro\Modules\Forms\Widgets\Form;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

abstract class Action_Base {

	abstract public function get_name();

	abstract public function get_label();

	/**
	 * @param Form_Record  $record
	 * @param Ajax_Handler $ajax_handler
	 */
	abstract public function run( $record, $ajax_handler );

	/**
	 * @param Form $form
	 */
	abstract public function register_settings_section( $form );

	/**
	 * @param array $element
	 */
	abstract public function on_export( $element );
}
