<?php
/**
 * Plugin Name: LearningCommonsFramework
 * Plugin URI:
 * Description:
 * Version:     0.1.0
 * Author:      10up
 * Author URI:  https://10up.com
 * Text Domain: learning-commons-framework
 * Domain Path: /languages
 *
 * @package LearningCommonsFramework
 */

// Useful global constants.
define( 'LEARNING_COMMONS_FRAMEWORK_VERSION', '0.1.0' );
define( 'LEARNING_COMMONS_FRAMEWORK_URL', plugin_dir_url( __FILE__ ) );
define( 'LEARNING_COMMONS_FRAMEWORK_PATH', plugin_dir_path( __FILE__ ) );
define( 'LEARNING_COMMONS_FRAMEWORK_INC', LEARNING_COMMONS_FRAMEWORK_PATH . 'includes/' );

// Include files.
require_once LEARNING_COMMONS_FRAMEWORK_INC . 'functions/core.php';

// Activation/Deactivation.
register_activation_hook( __FILE__, '\LearningCommonsFramework\Core\activate' );
register_deactivation_hook( __FILE__, '\LearningCommonsFramework\Core\deactivate' );

// Bootstrap.
LearningCommonsFramework\Core\setup();

// Require Composer autoloader if it exists.
if ( file_exists( LEARNING_COMMONS_FRAMEWORK_PATH . '/vendor/autoload.php' ) ) {
	require_once LEARNING_COMMONS_FRAMEWORK_PATH . 'vendor/autoload.php';
}
