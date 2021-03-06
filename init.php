<?php
/**
 * Initialize plugin functionality
 *
 * @package    SPR_Core
 * @subpackage Init
 * @category   Core
 * @since      1.0.0
 */

namespace SPR_Core;

// Alias namespaces.
use
SPR_Core\Classes            as Classes,
SPR_Core\Classes\Core       as Core,
SPR_Core\Classes\Settings   as Settings,
SPR_Core\Classes\Tools      as Tools,
SPR_Core\Classes\Media      as Media,
SPR_Core\Classes\Users      as Users,
SPR_Core\Classes\Admin      as Admin,
SPR_Core\Classes\Front      as Front,
SPR_Core\Classes\Front\Meta as Meta,
SPR_Core\Classes\Vendor     as Vendor;

// Restrict direct access.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Initialization function
 *
 * Loads PHP classes and text domain.
 * Instantiates various classes.
 * Adds settings link in the plugin row.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function init() {

	// Standard plugin installation.
	load_plugin_textdomain(
		'spr-core',
		false,
		dirname( SPRC_BASENAME ) . '/languages'
	);

	// If this is in the must-use plugins directory.
	load_muplugin_textdomain(
		'spr-core',
		dirname( SPRC_BASENAME ) . '/languages'
	);

	/**
	 * Class autoloader
	 *
	 * The autoloader registers plugin classes for later use,
	 * such as running new instances below.
	 */
	require_once SPRC_PATH . 'includes/autoloader.php';

	// Get compatibility functions.
	require SPRC_PATH . 'includes/vendor/compatibility.php';

	// Instantiate core classes.
	new Core\Remove_Blog;
	new Core\Type_Tax;
	new Core\Register_Featured;
	new Core\Register_Location;
	new Core\Register_Rental;
	new Core\Register_Staff;
	new Core\Register_Property_Type;
	new Core\Register_Location_Tax;
	new Core\Register_Admin;

	// If the Customizer is disabled in the system config file.
	if ( ( defined( 'SPRC_ALLOW_CUSTOMIZER' ) && false == SPRC_ALLOW_CUSTOMIZER ) && ! current_user_can( 'develop' ) ) {
		new Core\Remove_Customizer;
	}

	/**
	 * Editor options for WordPress
	 *
	 * Not run for ClassicPress.
	 * The `classicpress_version()` function checks for ClassicPress.
	 *
	 * Not run if the Classic Editor plugin is active.
	 */
	if ( ! function_exists( 'classicpress_version' ) ) {
		if ( ! is_plugin_active( 'classic-editor/classic-editor.php' ) ) {
			new Core\Editor_Options;
		}
	}

	// Instantiate tools classes.
	new Tools\Tools;

	// Instantiate media classes.
	new Media\Media;

	// Instantiate third-party classes.
	new Vendor\ACF;
	new Vendor\ACFE;

	// Instantiate backend classes.
	if ( is_admin() ) {
		new Admin\Admin;
	}

	// Instantiate users classes.
	new Users\Users;

	// Instantiate frontend classes.
	if ( ! is_admin() ) {
		new Front\Frontend;
		new Meta\Meta_Data;
		new Meta\Meta_Tags;
	}

	// Disable WordPress administration email verification prompt.
	add_filter( 'admin_email_check_interval', '__return_false' );

	// Disable Site Health notifications.
	if ( defined( 'SPRC_ALLOW_SITE_HEALTH' ) && ! SPRC_ALLOW_SITE_HEALTH ) {
		add_filter( 'wp_fatal_error_handler_enabled', '__return_false' );
	}

	/**
	 * Allow links manager
	 *
	 * @todo Put into an option.
	 */
	if ( defined( 'SPRC_ALLOW_LINKS_MANAGER' ) && SPRC_ALLOW_LINKS_MANAGER ) {
		add_filter( 'pre_option_link_manager_enabled', '__return_true' );
	}

	// Remove the Draconian capital P filters.
	remove_filter( 'the_title', 'capital_P_dangit', 11 );
	remove_filter( 'the_content', 'capital_P_dangit', 11 );
	remove_filter( 'comment_text', 'capital_P_dangit', 31 );

	/**
	 * Disable emoji script
	 *
	 * Emojis will still work in modern browsers. This removes the script
	 * that makes emojis work in old browsers.
	 */
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );

	// System email filters.
	add_filter( 'wp_mail_from_name', function( $name ) {
		return apply_filters( 'sprc_mail_from_name', get_bloginfo( 'name' ) );
	} );

	// Login footer credit.
	add_action( 'login_footer', function() {
		printf(
			'<p>%s %s <a href="%s" target="_blank" rel="nofollow">%s</a></p>',
			get_bloginfo( 'name' ),
			__( 'website designed & developed by', 'spr-core' ),
			esc_url( 'http://ccdzine.com' ),
			__( 'Controlled Chaos Design', 'spr-core' )
		);
	} );
}

// Run initialization function.
init();

/**
 * Admin initialization function
 *
 * Instantiates various classes.
 *
 * @since  1.0.0
 * @access public
 * @global $pagenow Get the current admin screen.
 * @return void
 */
function admin_init() {

	// Access current admin page.
	global $pagenow;
}
add_action( 'admin_init', __NAMESPACE__ . '\admin_init' );
