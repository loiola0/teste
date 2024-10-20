<?php
namespace Jet_Engine\Modules\Profile_Builder;

class Query {

	private $pagenow             = false;
	private $user                = false;
	private $subpagenow          = false;
	private $subpage             = array();
	private $is_account_page     = false;
	private $is_users_page       = false;
	private $is_single_user_page = false;
	private $queried_user_id     = null;
	private $queried_user        = null;

	/**
	 * Setup properties
	 */
	public function __construct() {
		add_action( 'wp', array( $this, 'setup_props' ), 99 );
	}

	/**
	 * Setupr query object properties
	 *
	 * @return [type] [description]
	 */
	public function setup_props() {

		$page    = get_query_var( Module::instance()->rewrite->page_var );
		$subpage = get_query_var( Module::instance()->rewrite->subpage_var );
		$user    = get_query_var( Module::instance()->rewrite->user_var );

		if ( $user ) {
			$this->user = $user;
		}

		if ( $subpage ) {
			$this->subpagenow = $subpage;
		}

		if ( $page ) {

			$this->pagenow = $page;

			switch ( $page ) {
				case 'account_page':
					$this->is_account_page = true;
					$this->setup_current_subpage_data();
					break;

				case 'users_page':
					$this->is_users_page = true;
					break;

				case 'single_user_page':
					$this->is_single_user_page = true;
					$this->setup_current_subpage_data();
					break;
			}
		}

		if ( $this->pagenow ) {
			do_action( 'jet-engine/profile-builder/query/setup-props', $this );
		}

	}

	/**
	 * Setup information about current subpage
	 *
	 * @return [type] [description]
	 */
	public function setup_current_subpage_data() {

		$settings = Module::instance()->settings->get();
		$pages    = array();
		$user     = false;

		if ( $this->is_account_page() ) {
			$pages = isset( $settings['account_page_structure'] ) ? $settings['account_page_structure'] : array();
			$user  = $this->setup_current_object();
		} elseif ( $this->is_single_user_page() ) {
			$pages = isset( $settings['user_page_structure'] ) ? $settings['user_page_structure'] : array();
			$user  = $this->setup_current_object( true );
		} else {
			// nothing to do...
			return;
		}

		if ( ! is_array( $pages ) ) {
			$pages = array();
		}

		if ( ! $this->subpagenow ) {
			$this->set_default_subpage( $pages, $user );
		} else {
			foreach ( $pages as $page ) {
				if ( ! empty( $page['slug'] ) && $this->subpagenow === $page['slug'] ) {
					$this->subpage = $page;
				}
			}
		}

	}

	public function set_default_subpage( $pages = array(), $user = false ) {

		if ( ! $user ) {
			return;
		}

		$pages = array_values( $pages );

		// If first page is accessible for any role - use it as defaut
		if ( ! empty( $pages[0] ) && empty( $pages[0]['roles'] ) ) {

			$this->subpage    = $pages[0];
			$this->subpagenow = $this->subpage['slug'];

			return;
		}

		// If not - find appropriate default page for the user role
		foreach ( $pages as $page ) {

			// It will be the first page accessible for any role
			if ( empty( $page['roles'] ) ) {
				$this->subpage    = $page;
				$this->subpagenow = $page['slug'];
				return;
			}

			// or first page available for the current (or queried) user
			$intersect = array_intersect( $page['roles'], $user->roles );

			if ( ! empty( $intersect ) ) {
				$this->subpage    = $page;
				$this->subpagenow = $page['slug'];
				return;
			}

		}
	}

	/**
	 * Setup current page main object for listings
	 *
	 * @return [type] [description]
	 */
	public function setup_current_object( $queried_user = false ) {

		if ( $queried_user ) {
			$user = $this->get_queried_user();
		} else {
			$user = wp_get_current_user();
		}

		jet_engine()->listings->data->set_current_object( $user );
		jet_engine()->listings->data->set_listing( jet_engine()->listings->get_new_doc( array(
			'listing_source'    => 'users',
			'listing_post_type' => 'page',
			'listing_tax'       => false,
			'is_main'           => true,
		), get_the_ID() ) );

		return $user;

	}

	/**
	 * Returns queried user object
	 *
	 * @return [type] [description]
	 */
	public function get_queried_user() {

		if ( null !== $this->queried_user ) {
			return $this->queried_user;
		}

		if ( ! $this->user ) {
			$this->queried_user = false;
			return $this->queried_user;
		}

		$user_rewrite = Module::instance()->settings->get( 'user_page_rewrite', 'login' );

		if ( 'user_nicename' === $user_rewrite ) {
			$user_rewrite = 'slug';
		}

		$this->queried_user = get_user_by( $user_rewrite, $this->user );

		return $this->queried_user;

	}

	/**
	 * Returns slug of currently queried user.
	 *
	 * Will return:
	 * - for user loops will return URL of apropriate user in loop
	 * - queried user slug for single user page
	 * - current user slug for other cases
	 *
	 * @return string
	 */
	public function get_queried_user_slug() {

		$listing = jet_engine()->listings->data->get_listing_source();
		$slug    = null;
		$user    = apply_filters( 'jet-engine/profile-builder/query/pre-get-queried-user', null );

		if ( ! $user ) {

			if ( 'users' === $listing ) {
				$user = jet_engine()->listings->data->get_current_object();
			} elseif ( $this->is_single_user_page() ) {
				$user = $this->get_queried_user();
			} else {
				$user = wp_get_current_user();
			}

		}

		if ( ! $user || ! ( $user instanceof \WP_User ) ) {
			return $slug;
		}

		switch ( Module::instance()->settings->get( 'user_page_rewrite' ) ) {

			case 'login':
				$slug = $user->data->user_login;
				break;

			case 'user_nicename':
				$slug = $user->data->user_nicename;
				break;

			case 'id':
				$slug = $user->data->ID;
				break;
		}

		return $slug;

	}

	/**
	 * Returns currently queried user ID or false
	 *
	 * @return [type] [description]
	 */
	public function get_queried_user_id() {

		if ( null !== $this->queried_user_id ) {
			return $this->queried_user_id;
		}

		$user = $this->get_queried_user();

		if ( ! $user || is_wp_error( $user ) ) {
			$this->queried_user_id = false;
			return $this->queried_user_id;
		}

		$this->queried_user_id = absint( $user->ID );

		return $this->queried_user_id;

	}

	/**
	 * Returns currently displaying page slug
	 *
	 * @return [type] [description]
	 */
	public function get_pagenow() {
		return $this->pagenow;
	}

	/**
	 * Returns currently displaying subpage slug
	 *
	 * @return [type] [description]
	 */
	public function get_subpage() {
		return $this->subpagenow;
	}

	/**
	 * Returns currently displaying subpage data
	 *
	 * @return [type] [description]
	 */
	public function get_subpage_data() {
		return $this->subpage;
	}

	/**
	 * Check if account page is currently displaying
	 *
	 * @return [type] [description]
	 */
	public function is_account_page() {
		return $this->is_account_page;
	}

	/**
	 * Check if users page is currently displaying
	 *
	 * @return [type] [description]
	 */
	public function is_users_page() {
		return $this->is_users_page;
	}

	/**
	 * Check if single user page is currently displaying
	 *
	 * @return [type] [description]
	 */
	public function is_single_user_page() {
		return $this->is_single_user_page;
	}

	/**
	 * Check passed subpage is visible
	 *
	 * @param  array   $subpage_item [description]
	 * @return boolean               [description]
	 */
	public function is_subpage_visible( $subpage_item = array() ) {

		if ( empty( $subpage_item['access'] ) || 'all' === $subpage_item['access'] ) {
			return true;
		}

		switch ( $subpage_item['access'] ) {
			case 'owner':
				return absint( get_current_user_id() ) === $this->get_queried_user_id();
		}

		return true;

	}

	/**
	 * Check if subpage is current ly displaying
	 *
	 * @param  [type]  $slug [description]
	 * @param  [type]  $page [description]
	 * @return boolean       [description]
	 */
	public function is_subpage_now( $slug = null, $page = 'account_page' ) {

		if ( $slug ) {

			if ( empty( $this->subpage ) ) {
				return false;
			} else {
				return $this->subpage['slug'] === $slug;
			}

		} elseif ( ! $this->subpagenow && 'account_page' === $page && $this->is_account_page() ) {
			return true;
		} elseif ( ! $this->subpagenow && 'single_user_page' === $page && $this->is_single_user_page() ) {
			return true;
		} else {
			return false;
		}

	}

}
