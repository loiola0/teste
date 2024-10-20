<?php
namespace Jet_Engine\Glossaries;

class Settings {

	public $items = false;
	public $nonce_key = 'jet-engine-glossaries';

	/**
	 * Constructor for the class
	 */
	public function __construct() {

		add_action( 'jet-engine/dashboard/tabs', array( $this, 'register_settings_tab' ), 99 );
		add_action( 'jet-engine/dashboard/assets', array( $this, 'register_settings_js' ) );
		add_action( 'wp_ajax_jet_engine_glossary_save', array( $this, 'save_item' ) );
		add_action( 'wp_ajax_jet_engine_glossary_delete', array( $this, 'delete_item' ) );

	}

	public function delete_item() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Access denied', 'jet-engine' ) ) );
		}

		$nonce = ! empty( $_REQUEST['nonce'] ) ? $_REQUEST['nonce'] : false;

		if ( ! $nonce || ! wp_verify_nonce( $nonce, $this->nonce_key ) ) {
			wp_send_json_error( array( 'message' => __( 'Nonce validation failed', 'jet-engine' ) ) );
		}

		$item_id = ! empty( $_REQUEST['item_id'] ) ? absint( $_REQUEST['item_id'] ) : false;

		if ( ! $item_id ) {
			wp_send_json_error( array( 'message' => __( 'Item ID not found in the request', 'jet-engine' ) ) );
		}

		jet_engine()->glossaries->data->set_request( array( 'id' => $item_id ) );

		if ( jet_engine()->glossaries->data->delete_item( false ) ) {
			return wp_send_json_success( array( 'message' => __( 'Item settings updated', 'jet-engine' ) ) );
		} else {
			return wp_send_json_error( Module::instance()->get_notices() );
		}

	}

	/**
	 * Ajax callback to save settings
	 *
	 * @return [type] [description]
	 */
	public function save_item() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Access denied', 'jet-engine' ) ) );
		}

		$nonce = ! empty( $_REQUEST['nonce'] ) ? $_REQUEST['nonce'] : false;

		if ( ! $nonce || ! wp_verify_nonce( $nonce, $this->nonce_key ) ) {
			wp_send_json_error( array( 'message' => __( 'Nonce validation failed', 'jet-engine' ) ) );
		}

		$item    = ! empty( $_REQUEST['item'] ) ? $_REQUEST['item'] : array();
		$item_id = ! empty( $_REQUEST['item_id'] ) ? absint( $_REQUEST['item_id'] ) : false;

		if ( $item_id ) {
			$item['id'] = $item_id;
		}

		jet_engine()->glossaries->data->set_request( $item );

		if ( ! $item_id ) {
			$done = jet_engine()->glossaries->data->create_item( false );
		} else {
			$done = jet_engine()->glossaries->data->edit_item( false );
		}

		if ( ! empty( $done ) ) {

			$message = __( 'Item settings updated', 'jet-engine' );

			wp_send_json_success( array(
				'item_id' => $done,
				'message' => $message,
			) );
		} else {

			$raw_notices = array();
			$notices     = jet_engine()->glossaries->get_notices();

			if ( ! empty( $notices ) ) {
				foreach ( $notices as $notice ) {
					$raw_notices[] = $notice['message'];
				}
			}

			wp_send_json_error( array(
				'message' => implode( ', ', $raw_notices ),
			) );
		}

	}

	/**
	 * Register settings JS file
	 *
	 * @return [type] [description]
	 */
	public function register_settings_js() {

		wp_enqueue_script(
			'jet-engine-glossaries',
			jet_engine()->glossaries->component_url( 'assets/js/admin/settings.js' ),
			array( 'cx-vue-ui' ),
			jet_engine()->get_version(),
			true
		);

		$items = $this->get();

		wp_localize_script(
			'jet-engine-glossaries',
			'JetEngineGlossariesConfig',
			array(
				'items'       => $items,
				'_nonce'      => wp_create_nonce( $this->nonce_key ),
				'save_label' => __( 'Save', 'jet-engine' ),
				'saving_label' => __( 'Saving...', 'jet-engine' ),
			)
		);

		add_action( 'admin_footer', array( $this, 'print_templates' ) );

	}

	/**
	 * Print VU template for maps settings
	 *
	 * @return [type] [description]
	 */
	public function print_templates() {
		?>
		<script type="text/x-template" id="jet_engine_glossaries">
			<div class="cx-vui-inner-panel">
				<div tabindex="0" class="cx-vui-repeater">
					<div class="cx-vui-repeater__items">
						<div :class="{ 'cx-vui-repeater-item': true, 'cx-vui-panel': true, 'cx-vui-repeater-item--is-collpased': editID !== item.id }" v-for="( item, index ) in items" :key="item.id">
							<div :class="{ 'cx-vui-repeater-item__heading': true, 'cx-vui-repeater-item__heading--is-collpased': editID !== item.id }">
								<div class="cx-vui-repeater-item__heading-start" @click="setEdit( item.id )">
									<svg v-if="editID !== item.id" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg" class="cx-vui-repeater-item__collapse cx-vui-repeater-item__collapse--is-collpased"><rect width="14" height="14" transform="matrix(1 0 0 -1 0 14)" fill="white"></rect><path d="M13 5.32911L7 11L1 5.32911L2.40625 4L7 8.34177L11.5938 4L13 5.32911Z"></path></svg>
									<svg v-else width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg" class="cx-vui-repeater-item__collapse"><rect width="14" height="14" transform="matrix(1 0 0 -1 0 14)" fill="white"></rect><path d="M13 5.32911L7 11L1 5.32911L2.40625 4L7 8.34177L11.5938 4L13 5.32911Z"></path></svg>
									<div class="cx-vui-repeater-item__title">{{ item.name }}</div>
									<div class="cx-vui-repeater-item__subtitle">{{ item.slug }}</div>
								</div>
								<div class="cx-vui-repeater-item__heading-end">
									<div class="cx-vui-repeater-item__clean" @click="deleteID = item.id">
										<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="16" height="16" transform="matrix(1 0 0 -1 0 16)" fill="white"></rect><path d="M2.28564 14.192V3.42847H13.7142V14.192C13.7142 14.6685 13.5208 15.0889 13.1339 15.4533C12.747 15.8177 12.3005 15.9999 11.7946 15.9999H4.20529C3.69934 15.9999 3.25291 15.8177 2.866 15.4533C2.4791 15.0889 2.28564 14.6685 2.28564 14.192Z"></path><path d="M14.8571 1.14286V2.28571H1.14282V1.14286H4.57139L5.56085 0H10.4391L11.4285 1.14286H14.8571Z"></path></svg>
										<div class="cx-vui-tooltip" v-if="deleteID === item.id">
											<?php _e( 'Are you sure?', 'jet-engine' ); ?>
											<br><span class="cx-vui-repeater-item__confrim-del" @click.stop="deleteItem( item.id, index )"><?php _e( 'Yes', 'jet-engine' ); ?></span>&nbsp;/&nbsp;<span class="cx-vui-repeater-item__cancel-del" @click.stop="deleteID = false"><?php _e( 'No', 'jet-engine' ); ?></span>
										</div>
									</div>
								</div>
							</div>
							<div :class="{ 'cx-vui-repeater-item__content': true, 'cx-vui-repeater-item__content--is-collpased': editID !== item.id }">
								<jet-engine-glossary :value="item"/>
							</div>
						</div>
					</div>
					<div class="cx-vui-repeater__actions">
						<cx-vui-button
							button-style="accent-border"
							size="mini"
							:disabled="isBusy"
							@click="newItem"
						>
							<span
								slot="label"
								v-html="'<?php _e( '+ New Glossary', 'jet-engine' ); ?>'"
							></span>
						</cx-vui-button>
					</div>
				</div>
			</div>
		</script>
		<script type="text/x-template" id="jet_engine_glossary">
			<div>
				<cx-vui-input
					label="<?php _e( 'Name', 'jet-engine' ); ?>"
					description="<?php _e( 'Human-readable name for the glossary', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					size="fullwidth"
					v-model="settings.name"
				></cx-vui-input>
				<cx-vui-component-wrapper
					:wrapper-css="[ 'fullwidth-control' ]"
				>
					<div class="cx-vui-inner-panel">
						<cx-vui-repeater
							:button-label="'<?php _e( 'New field', 'jet-engine' ); ?>'"
							:button-style="'accent'"
							:button-size="'mini'"
							v-model="settings.fields"
							@add-new-item="addNewField()"
						>
							<cx-vui-repeater-item
								v-for="( field, fieldIndex ) in settings.fields"
								:title="settings.fields[ fieldIndex ].label"
								:subtitle="settings.fields[ fieldIndex ].value"
								:collapsed="isCollapsed( field )"
								:index="fieldIndex"
								@clone-item="cloneField( $event, fieldIndex )"
								@delete-item="deleteField( $event, fieldIndex )"
								:key="'field' + fieldIndex"
							>
								<cx-vui-input
									:label="'<?php _e( 'Field Value', 'jet-engine' ); ?>'"
									:description="'<?php _e( 'This value will be saved into Database', 'jet-engine' ); ?>'"
									:wrapper-css="[ 'equalwidth' ]"
									:size="'fullwidth'"
									:value="settings.fields[ fieldIndex ].value"
									@input="setFieldProp( fieldIndex, 'value', $event )"
								></cx-vui-input>
								<cx-vui-input
									:label="'<?php _e( 'Field Label', 'jet-engine' ); ?>'"
									:description="'<?php _e( 'This will be shown for the user', 'jet-engine' ); ?>'"
									:wrapper-css="[ 'equalwidth' ]"
									:size="'fullwidth'"
									:value="settings.fields[ fieldIndex ].label"
									@input="setFieldProp( fieldIndex, 'label', $event )"
								></cx-vui-input>
								<cx-vui-switcher
									label="<?php _e( 'Is checked (selected)', 'jet-engine' ); ?>"
									description="<?php _e( 'Check this to make this field checked or selected by default.', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth' ]"
									:value="settings.fields[ fieldIndex ].is_checked"
									@input="setFieldProp( fieldIndex, 'is_checked', $event )"
								></cx-vui-switcher>
							</cx-vui-repeater-item>
						</cx-vui-repeater>
					</div>
				</cx-vui-component-wrapper>
				<cx-vui-component-wrapper
					:wrapper-css="[ 'equalwidth' ]"
				>
					<cx-vui-button
						button-style="accent"
						:loading="saving"
						:disabled="isDisabled()"
						@click="saveItem"
					>
						<span
							slot="label"
						>{{ buttonLabel() }}</span>
					</cx-vui-button>
				</cx-vui-component-wrapper>
			</div>
		</script>
		<?php
	}

	/**
	 * Returns all settings
	 *
	 * @return [type] [description]
	 */
	public function get( $endpoint_id = false ) {

		if ( false === $this->items ) {

			$this->items = jet_engine()->glossaries->data->get_item_for_register();

			if ( empty( $this->items ) ) {
				$this->items = array();
			}

			usort( $this->items, function( $a, $b ) {

				if ( $a['id'] === $b['id'] ) {
					return 0;
				}

				return ( $a['id'] < $b['id'] ) ? -1 : 1;
			} );

		}

		if ( false === $endpoint_id ) {
			return $this->items;
		} else {

			foreach ( $this->items as $item ) {
				if ( $item['id'] === $endpoint_id ) {
					return $item;
				}
			}

			return false;
		}


	}

	/**
	 * Register settings tab
	 *
	 * @return [type] [description]
	 */
	public function register_settings_tab() {
		?>
		<cx-vui-tabs-panel
			name="glossaries"
			label="<?php _e( 'Glossaries', 'jet-engine' ); ?>"
			key="glossaries"
		>
			<keep-alive>
				<jet-engine-glossaries></jet-engine-glossaries>
			</keep-alive>
		</cx-vui-tabs-panel>
		<?php
	}

}
