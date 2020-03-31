<?php
/**
 * Plugin Name: Co-op Library Framework
 * Plugin URI: https://github.com/platform-coop-toolkit/coop-library-framework/
 * Description: Custom post type and metadata utilities for the Platform Co-op Resource Library.
 * Version: 1.0.0-rc.1
 * Author: Platform Cooperative Development Kit
 * Author URI:  https://github.com/platform-coop-toolkit/
 * Text Domain: coop-library-framework
 * Domain Path: /languages
 *
 * @package CoopLibraryFramework
 */

// Useful global constants.
define( 'COOP_LIBRARY_FRAMEWORK_VERSION', '1.0.0-rc.1' );
define( 'COOP_LIBRARY_FRAMEWORK_URL', plugin_dir_url( __FILE__ ) );
define( 'COOP_LIBRARY_FRAMEWORK_PATH', plugin_dir_path( __FILE__ ) );
define( 'COOP_LIBRARY_FRAMEWORK_INC', COOP_LIBRARY_FRAMEWORK_PATH . 'includes/' );

// Include files.
require_once COOP_LIBRARY_FRAMEWORK_INC . 'functions/core.php';
require_once COOP_LIBRARY_FRAMEWORK_INC . 'functions/internationalization.php';
require_once COOP_LIBRARY_FRAMEWORK_INC . 'functions/metadata.php';

// Activation/Deactivation.
register_activation_hook( __FILE__, '\CoopLibraryFramework\Core\activate' );
register_deactivation_hook( __FILE__, '\CoopLibraryFramework\Core\deactivate' );

// Bootstrap.
CoopLibraryFramework\Core\setup();
CoopLibraryFramework\Internationalization\setup();
CoopLibraryFramework\Metadata\setup();

// Require Composer autoloader if it exists.
if ( file_exists( COOP_LIBRARY_FRAMEWORK_PATH . '/vendor/autoload.php' ) ) {
	require_once COOP_LIBRARY_FRAMEWORK_PATH . 'vendor/autoload.php';
}

// Run dependency installer.
WP_Dependency_Installer::instance()->run( __DIR__ );
