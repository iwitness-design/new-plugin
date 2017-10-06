<?php
/**
 * Plugin Name: Plugin Name
 * Plugin URL: http://iwitnessdesign.com
 * Description: The plugin description
 * Version: 1.0.0
 * Author: iWitness Design
 * Author URI: https://iwitnessdesign.com
 * Text Domain: txt
 * Domain Path: languages
 */

class NewPlugin {

	/**
	 * @var
	 */
	protected static $_instance;

	/**
	 * @var string
	 */
	protected static $_version = '1.0.0';

	/**
	 * Only make one instance of self
	 *
	 * @return self
	 */
	public static function get_instance() {
		if ( ! self::$_instance instanceof self ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Add Hooks and Actions
	 */
	protected function __construct() {
		add_action( 'plugins_loaded', array( $this, 'maybe_setup' ), -9999 );
	}

	/**
	 * Includes
	 */
	protected function includes() {
		require_once( $this->get_plugin_dir() . 'vendor/autoload.php' );
	}

	/**
	 * Actions and Filters
	 */
	protected function actions() {
		add_action( 'init', array( $this, 'load_textdomain' ) );
	}

	/** Actions **************************************/

	/**
	 * Setup the plugin
	 */
	public function maybe_setup() {
		if ( ! $this->check_required_plugins() ) {
			return;
		}

		$this->includes();
		$this->actions();
	}

	/**
	 * Required Plugins notice
	 */
	public function required_plugins() {
		printf( '<div class="error"><p>%s</p></div>', __( 'Something is required for the %s add-on to function.',  $this->get_id(), $this->get_plugin_name() ) );
	}

	/**
	 * Load the text domain
	 *
	 * @since  1.0.0
	 */
	public function load_textdomain() {
		// Set filter for plugin's languages directory
		$lang_dir = dirname( plugin_basename( $this->get_plugin_file() ) ) . '/languages/';
		$lang_dir = apply_filters( $this->get_id() . '_languages_directory', $lang_dir );


		// Traditional WordPress plugin locale filter

		$get_locale = get_locale();

		if ( function_exists( 'get_user_locale' ) ) {
			$get_locale = get_user_locale();
		}

		/**
		 * Defines the plugin language locale used.
		 *
		 * @var string $get_locale The locale to use. Uses get_user_locale()` in WordPress 4.7 or greater,
		 *                  otherwise uses `get_locale()`.
		 */
		$locale = apply_filters( 'plugin_locale', $get_locale, $this->get_id() );
		$mofile = sprintf( '%1$s-%2$s.mo', $this->get_id(), $locale );

		// Setup paths to current locale file
		$mofile_local  = $lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/' . $this->get_id() . '/' . $mofile;

		if ( file_exists( $mofile_global ) ) {
			load_textdomain( $this->get_id(), $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			load_textdomain( $this->get_id(), $mofile_local );
		} else {
			load_plugin_textdomain( $this->get_id(), false, $lang_dir );
		}
	}

	/** Helper Methods **************************************/

	/**
	 * Make sure RCP is active
	 * @return bool
	 */
	protected function check_required_plugins() {

		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		if ( function_exists( '' ) || is_plugin_active( '' ) ) {
			return true;
		}

		add_action( 'admin_notices', array( $this, 'required_plugins' ) );

		return false;
	}

	/**
	 * Return the version of the plugin
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public function get_version() {
		return self::$_version;
	}

	/**
	 * Returns the plugin name, localized
	 *
	 * @since 1.0.0
	 * @return string the plugin name
	 */
	public function get_plugin_name() {
		return __( 'NewPlugin', $this->get_id() );
	}

	/**
	 * Returns the plugin ID. Used in the textdomain
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public function get_id() {
		return 'newplugin';
	}

	/**
	 * Get the plugin directory path
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public function get_plugin_dir() {
		return plugin_dir_path( $this->get_plugin_file() );
	}

	/**
	 * Get the plugin directory url
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public function get_plugin_url() {
		return plugin_dir_url( $this->get_plugin_file() );
	}

	/**
	 * Get the plugin file
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public function get_plugin_file() {
		return __FILE__;
	}

}

/**
 * @return NewPlugin
 */
function newplugin() {
	return NewPlugin::get_instance();
}

newplugin();
