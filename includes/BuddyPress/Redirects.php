<?php

namespace TheroCustom\BuddyPress;

class Redirects {

	/**
	 * @var
	 */
	protected static $_instance;

	/**
	 * Only make one instance of self
	 *
	 * @return self
	 */
	public static function get_instance() {
		if ( ! self::$_instance instanceof Redirects ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Add Hooks and Actions
	 */
	protected function __construct() {
		add_action( 'template_redirect', array( $this, 'redirect_visitor' ) );
		add_action( 'template_redirect', array( $this, 'redirect_welcome' ) );

		add_action( 'bp_active_external_pages', array( $this, 'welcome_pages' ) );
		add_action( 'bp_active_external_pages', array( $this, 'restricted_redirect_pages' ) );
		add_action( 'bp_admin_init', array( $this, 'save_redirect_pages' ), 9 );
	}

	/**
	 * Redirect visitors away from BuddyPress components
	 *
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public function redirect_visitor() {

		if ( is_user_logged_in() ) {
			return;
		}

		// make sure BuddyPress is setup
		if ( ! function_exists( 'buddypress' ) ) {
			return;
		}

		if ( is_front_page() ) {
			return;
		}

		if ( bp_is_register_page() || bp_is_activation_page() ) {
			return;
		}

		// Make sure we are on a BuddyPress Component
		if ( ! bp_current_component() ) {
			return;
		}

		$redirect_pages = $this->get_restricted_redirect_pages();

		if ( empty( $redirect_pages['visitor'] ) ) {
			return;
		}

		wp_safe_redirect( get_the_permalink( $redirect_pages['visitor'] ) );
		die();

	}

	/**
	 * Redirect user's to the welcome page for their member type
	 *
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public function redirect_welcome() {

		if ( ! is_user_logged_in() ) {
			return;
		}

		$user = wp_get_current_user();

		if ( get_user_meta( $user->ID, 'thero_welcome_is_viewed', true ) ) {
			return;
		}

		// only apply this to members who have registered after this plugin was activated
		if ( get_option( therocustom()->get_id() . '_activated', 0 ) > $user->user_registered ) {
			return;
		}

		$redirect_pages = $this->get_welcome_pages();

		foreach( $redirect_pages as $member_type => $page_id ) {
			if ( bp_has_member_type( $user->ID, $member_type ) ) {
				update_user_meta( $user->ID, 'thero_welcome_is_viewed', $page_id );
				wp_safe_redirect( get_the_permalink( $page_id ) );
				die();
			}
		}

		if ( empty( $redirect_pages['default'] ) ) {
			return;
		}

		update_user_meta( $user->ID, 'thero_welcome_is_viewed', $redirect_pages['default'] );
		wp_safe_redirect( get_the_permalink( $redirect_pages['default'] ) );
		die();

	}

	/**
	 * Get member type welcome pages
	 *
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public function welcome_pages() {

		if ( ! $types = bp_get_member_types( array(), 'all' ) ) {
			return;
		}

		$existing_pages = $this->get_welcome_pages(); ?>

		<tr valign="top">
			<th scope="row" colspan="2">
				<h3><?php _e( 'Welcome Pages for Member Types', therocustom()->get_id() ); ?></h3>
				<p><?php _e( 'Specify which page a member should be redirected to when they first log in.', therocustom()->get_id() ); ?></p>
			</th>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label for="bp_welcome_pages[default]"><?php _e( 'Default', therocustom()->get_id() ) ?></label>
			</th>

			<td>

				<?php if ( ! bp_is_root_blog() ) {
					switch_to_blog( bp_get_root_blog_id() );
				} ?>

				<?php echo wp_dropdown_pages( array(
					'name'             => 'bp_welcome_pages[default]',
					'echo'             => false,
					'show_option_none' => __( '- None -', 'buddypress' ),
					'selected'         => ! empty( $existing_pages[ 'default' ] ) ? $existing_pages[ 'default' ] : false
				) ) ?>

				<?php if ( ! empty( $existing_pages[ 'default' ] ) ) : ?>

					<a href="<?php echo get_permalink( $existing_pages[ 'default' ] ); ?>" class="button-secondary" target="_bp"><?php _e( 'View', 'buddypress' ); ?></a>

				<?php endif; ?>

				<?php if ( ! bp_is_root_blog() ) {
					restore_current_blog();
				} ?>

			</td>
		</tr>

		<?php foreach( $types as $type ) : ?>

			<tr valign="top">
				<th scope="row">
					<label for="bp_welcome_pages[<?php echo esc_attr( $type->name ) ?>]"><?php echo esc_html( $type->labels['name'] ) ?></label>
				</th>

				<td>

					<?php if ( ! bp_is_root_blog() ) switch_to_blog( bp_get_root_blog_id() ); ?>

					<?php echo wp_dropdown_pages( array(
						'name'             => 'bp_welcome_pages[' . esc_attr( $type->name ) . ']',
						'echo'             => false,
						'show_option_none' => __( '- None -', 'buddypress' ),
						'selected'         => !empty( $existing_pages[ $type->name ] ) ? $existing_pages[ $type->name ] : false
					) ) ?>

					<?php if ( !empty( $existing_pages[ $type->name ] ) ) : ?>

						<a href="<?php echo get_permalink( $existing_pages[ $type->name ] ); ?>" class="button-secondary" target="_bp"><?php _e( 'View', 'buddypress' ); ?></a>

					<?php endif; ?>

					<?php if ( ! bp_is_root_blog() ) restore_current_blog(); ?>

				</td>
			</tr>

		<?php endforeach;
	}

	/**
	 * Specify the page that visitors should be taken to when they try to access BP pages
	 *
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public function restricted_redirect_pages() {

		if ( ! $types = bp_get_member_types( array(), 'all' ) ) {
			return;
		}

		$existing_pages = $this->get_restricted_redirect_pages(); ?>

		<tr valign="top">
			<th scope="row" colspan="2">
				<h3><?php _e( 'BuddyPress Component Redirect', therocustom()->get_id() ); ?></h3>
				<p><?php _e( 'Specify which page a visitor should be redirected to when try to access a BuddyPress component.', therocustom()->get_id() ); ?></p>
			</th>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label for="bp_redirect_pages[visitor]"><?php _e( 'Visitors', therocustom()->get_id() ); ?></label>
			</th>

			<td>

				<?php if ( ! bp_is_root_blog() ) switch_to_blog( bp_get_root_blog_id() ); ?>

				<?php echo wp_dropdown_pages( array(
					'name'             => 'bp_redirect_pages[visitor]',
					'echo'             => false,
					'show_option_none' => __( '- None -', 'buddypress' ),
					'selected'         => !empty( $existing_pages['visitor'] ) ? $existing_pages['visitor'] : false
				) ) ?>

				<?php if ( ! empty( $existing_pages['visitor'] ) ) : ?>

					<a href="<?php echo get_permalink( $existing_pages['visitor'] ); ?>" class="button-secondary" target="_bp"><?php _e( 'View', 'buddypress' ); ?></a>

				<?php endif; ?>

				<?php if ( ! bp_is_root_blog() ) restore_current_blog(); ?>

			</td>
		</tr>

		<?php
	}

	/**
	 * Get pages that should be returned for the welcome page
	 *
	 * @since  1.0.0
	 *
	 * @return array
	 * @author Tanner Moushey
	 */
	public function get_welcome_pages() {
		return get_option( 'bp_welcome_pages', array() );
	}

	/**
	 * Get pages that should be returned for restricted page redirect
	 *
	 * @since  1.0.0
	 *
	 * @return array
	 * @author Tanner Moushey
	 */
	public function get_restricted_redirect_pages() {
		return get_option( 'bp_restricted_redirect_pages', array() );
	}

	/**
	 * Save redirect pages
	 *
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public function save_redirect_pages() {

		if ( empty( $_POST['bp-admin-pages-submit'] ) ) {
			return;
		}

		if ( ! check_admin_referer( 'bp-admin-pages-setup' ) ) {
			return;
		}

		$welcome_pages = $restricted_pages = array();

		if ( isset( $_POST['bp_welcome_pages'] ) ) {
			foreach( (array) $_POST['bp_welcome_pages'] as $key => $value ) {
				$welcome_pages[ $key ] = absint( $value );
			}
		}

		if ( isset( $_POST['bp_redirect_pages'] ) ) {
			foreach( (array) $_POST['bp_redirect_pages'] as $key => $value ) {
				$restricted_pages[ $key ] = absint( $value );
			}
		}

		update_option( 'bp_welcome_pages', $welcome_pages );
		update_option( 'bp_restricted_redirect_pages', $restricted_pages );

	}
}