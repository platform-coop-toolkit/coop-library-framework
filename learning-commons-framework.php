<?php
/**
 * Plugin Name: Learning Commons Framework
 * Plugin URI: https://github.com/platform-coop-toolkit/learning-commons-framework/
 * Description: Block and Custom Post Type utilities for the Platform Cooperativism Learning Commons.
 * Version: 1.0.0-alpha.1
 * Author: Platform Cooperative Development Kit
 * Author URI:  https://github.com/platform-coop-toolkit/
 * Text Domain: learning-commons-framework
 * Domain Path: /languages
 *
 * @package LearningCommonsFramework
 */

// Useful global constants.
define( 'LEARNING_COMMONS_FRAMEWORK_VERSION', '1.0.0-alpha' );
define( 'LEARNING_COMMONS_FRAMEWORK_URL', plugin_dir_url( __FILE__ ) );
define( 'LEARNING_COMMONS_FRAMEWORK_PATH', plugin_dir_path( __FILE__ ) );
define( 'LEARNING_COMMONS_FRAMEWORK_INC', LEARNING_COMMONS_FRAMEWORK_PATH . 'includes/' );

// Include files.
require_once LEARNING_COMMONS_FRAMEWORK_INC . 'functions/core.php';
require_once LEARNING_COMMONS_FRAMEWORK_INC . 'functions/internationalization.php';
require_once LEARNING_COMMONS_FRAMEWORK_INC . 'functions/metadata.php';

// Activation/Deactivation.
register_activation_hook( __FILE__, '\LearningCommonsFramework\Core\activate' );
register_deactivation_hook( __FILE__, '\LearningCommonsFramework\Core\deactivate' );

// Bootstrap.
LearningCommonsFramework\Core\setup();
LearningCommonsFramework\Internationalization\setup();
LearningCommonsFramework\Metadata\setup();

// Require Composer autoloader if it exists.
if ( file_exists( LEARNING_COMMONS_FRAMEWORK_PATH . '/vendor/autoload.php' ) ) {
	require_once LEARNING_COMMONS_FRAMEWORK_PATH . 'vendor/autoload.php';
}

// Load CMB2.
require_once LEARNING_COMMONS_FRAMEWORK_PATH . 'vendor/cmb2/cmb2/init.php';

// Run dependency installer.
WP_Dependency_Installer::instance()->run( __DIR__ );
