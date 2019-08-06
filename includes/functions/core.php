<?php
/**
 * Core plugin functionality.
 *
 * @package LearningCommonsFramework
 */

namespace LearningCommonsFramework\Core;

use \WP_Error as WP_Error;

/**
 * Default setup routine
 *
 * @return void
 */
function setup() {
	$n = function( $function ) {
		return __NAMESPACE__ . "\\$function";
	};

	add_action( 'init', $n( 'i18n' ) );
	add_action( 'init', $n( 'init' ) );
	add_action( 'init', $n( 'resource_init' ) );
	add_action( 'wp_enqueue_scripts', $n( 'scripts' ) );
	add_action( 'wp_enqueue_scripts', $n( 'styles' ) );
	add_action( 'admin_enqueue_scripts', $n( 'admin_scripts' ) );
	add_action( 'admin_enqueue_scripts', $n( 'admin_styles' ) );
	add_action( 'enqueue_block_editor_assets', $n( 'block_scripts' ) );
	add_action( 'enqueue_block_editor_assets', $n( 'block_styles' ) );

	// Editor styles. add_editor_style() doesn't work outside of a theme.
	add_filter( 'mce_css', $n( 'mce_css' ) );
	// Hook to allow async or defer on asset loading.
	add_filter( 'script_loader_tag', $n( 'script_loader_tag' ), 10, 2 );

	do_action( 'learning_commons_framework_loaded' );
}

/**
 * Registers the default textdomain.
 *
 * @return void
 */
function i18n() {
	$locale = apply_filters( 'plugin_locale', get_locale(), 'learning-commons-framework' );
	load_textdomain( 'learning-commons-framework', WP_LANG_DIR . '/learning-commons-framework/learning-commons-framework-' . $locale . '.mo' );
	load_plugin_textdomain( 'learning-commons-framework', false, plugin_basename( LEARNING_COMMONS_FRAMEWORK_PATH ) . '/languages/' );
}

/**
 * Registers the `lc-resource` post type.
 *
 * @return void
 */
function resource_init() {
	register_extended_post_type(
		'lc_resource',
		array(
			'menu_position' => 5,
			'menu_icon'     => 'dashicons-archive',
			'rest_base'     => 'resources',
			'show_in_rest'  => true,
			'supports'      => [ 'title', 'editor', 'custom-fields' ],
		),
		array(
			'singular' => __( 'Resource', 'learning_commons_framework' ),
			'plural'   => __( 'Resources', 'learning_commons_framework' ),
			'slug'     => 'resource',
		)
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_publication_date',
		[
			'type'         => 'string',
			'description'  => 'The publication date of the resource in YYYY-MM-DD format.',
			'single'       => true,
			'show_in_rest' => true,
		]
	);

	$post_type_object           = get_post_type_object( 'lc_resource' );
	$post_type_object->template = array(
		array( 'learning-commons-framework/publication-date' ),
	);
}

/**
 * Initializes the plugin and fires an action other plugins can hook into.
 *
 * @return void
 */
function init() {
	do_action( 'learning_commons_framework_init' );
}

/**
 * Activate the plugin
 *
 * @return void
 */
function activate() {
	// First load the init scripts in case any rewrite functionality is being loaded
	init();
	flush_rewrite_rules();
}

/**
 * Deactivate the plugin
 *
 * Uninstall routines should be in uninstall.php
 *
 * @return void
 */
function deactivate() {

}


/**
 * The list of knows contexts for enqueuing scripts/styles.
 *
 * @return array
 */
function get_enqueue_contexts() {
	return [ 'admin', 'frontend', 'shared' ];
}

/**
 * Generate an URL to a script, taking into account whether SCRIPT_DEBUG is enabled.
 *
 * @param string $script Script file name (no .js extension)
 * @param string $context Context for the script ('admin', 'frontend', or 'shared')
 *
 * @return string|WP_Error URL
 */
function script_url( $script, $context ) {

	if ( ! in_array( $context, get_enqueue_contexts(), true ) ) {
		return new WP_Error( 'invalid_enqueue_context', 'Invalid $context specified in LearningCommonsFramework script loader.' );
	}

	return LEARNING_COMMONS_FRAMEWORK_URL . "dist/js/${script}.js";

}

/**
 * Generate an URL to a stylesheet, taking into account whether SCRIPT_DEBUG is enabled.
 *
 * @param string $stylesheet Stylesheet file name (no .css extension)
 * @param string $context Context for the script ('admin', 'frontend', or 'shared')
 *
 * @return string URL
 */
function style_url( $stylesheet, $context ) {

	if ( ! in_array( $context, get_enqueue_contexts(), true ) ) {
		return new WP_Error( 'invalid_enqueue_context', 'Invalid $context specified in LearningCommonsFramework stylesheet loader.' );
	}

	return LEARNING_COMMONS_FRAMEWORK_URL . "dist/css/${stylesheet}.css";

}

/**
 * Enqueue scripts for front-end.
 *
 * @return void
 */
function scripts() {

	wp_enqueue_script(
		'learning_commons_framework_shared',
		script_url( 'shared', 'shared' ),
		[],
		LEARNING_COMMONS_FRAMEWORK_VERSION,
		true
	);

	wp_enqueue_script(
		'learning_commons_framework_frontend',
		script_url( 'frontend', 'frontend' ),
		[],
		LEARNING_COMMONS_FRAMEWORK_VERSION,
		true
	);

}

/**
 * Enqueue scripts for admin.
 *
 * @return void
 */
function admin_scripts() {

	wp_enqueue_script(
		'learning_commons_framework_shared',
		script_url( 'shared', 'shared' ),
		[],
		LEARNING_COMMONS_FRAMEWORK_VERSION,
		true
	);

	wp_enqueue_script(
		'learning_commons_framework_admin',
		script_url( 'admin', 'admin' ),
		[],
		LEARNING_COMMONS_FRAMEWORK_VERSION,
		true
	);

}

/**
 * Enqueue scripts for blocks.
 *
 * @return void
 */
function block_scripts() {

	wp_enqueue_script(
		'learning_commons_framework_blocks',
		script_url( 'blocks', 'admin' ),
		[ 'wp-blocks', 'wp-element', 'wp-components' ],
		LEARNING_COMMONS_FRAMEWORK_VERSION,
		true
	);

}

/**
 * Enqueue styles for front-end.
 *
 * @return void
 */
function styles() {

	wp_enqueue_style(
		'learning_commons_framework_shared',
		style_url( 'shared-style', 'shared' ),
		[],
		LEARNING_COMMONS_FRAMEWORK_VERSION
	);

	if ( is_admin() ) {
		wp_enqueue_style(
			'learning_commons_framework_admin',
			style_url( 'admin-style', 'admin' ),
			[],
			LEARNING_COMMONS_FRAMEWORK_VERSION
		);
	} else {
		wp_enqueue_style(
			'learning_commons_framework_frontend',
			style_url( 'style', 'frontend' ),
			[],
			LEARNING_COMMONS_FRAMEWORK_VERSION
		);
	}

}

/**
 * Enqueue styles for admin.
 *
 * @return void
 */
function admin_styles() {

	wp_enqueue_style(
		'learning_commons_framework_shared',
		style_url( 'shared-style', 'shared' ),
		[],
		LEARNING_COMMONS_FRAMEWORK_VERSION
	);

	wp_enqueue_style(
		'learning_commons_framework_admin',
		style_url( 'admin-style', 'admin' ),
		[],
		LEARNING_COMMONS_FRAMEWORK_VERSION
	);

}

/**
 * Enqueue styles for blocks.
 *
 * @return void
 */
function block_styles() {

	wp_enqueue_style(
		'learning_commons_framework_blocks',
		style_url( 'block-style', 'admin' ),
		[],
		LEARNING_COMMONS_FRAMEWORK_VERSION
	);

}

/**
 * Enqueue editor styles. Filters the comma-delimited list of stylesheets to load in TinyMCE.
 *
 * @param string $stylesheets Comma-delimited list of stylesheets.
 * @return string
 */
function mce_css( $stylesheets ) {
	if ( ! empty( $stylesheets ) ) {
		$stylesheets .= ',';
	}

	return $stylesheets . LEARNING_COMMONS_FRAMEWORK_URL . ( ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ?
			'assets/css/frontend/editor-style.css' :
			'dist/css/editor-style.min.css' );
}

/**
 * Add async/defer attributes to enqueued scripts that have the specified script_execution flag.
 *
 * @link https://core.trac.wordpress.org/ticket/12009
 * @param string $tag    The script tag.
 * @param string $handle The script handle.
 * @return string
 */
function script_loader_tag( $tag, $handle ) {
	$script_execution = wp_scripts()->get_data( $handle, 'script_execution' );

	if ( ! $script_execution ) {
		return $tag;
	}

	if ( 'async' !== $script_execution && 'defer' !== $script_execution ) {
		return $tag;
	}

	// Abort adding async/defer for scripts that have this script as a dependency. _doing_it_wrong()?
	foreach ( wp_scripts()->registered as $script ) {
		if ( in_array( $handle, $script->deps, true ) ) {
			return $tag;
		}
	}

	// Add the attribute if it hasn't already been added.
	if ( ! preg_match( ":\s$script_execution(=|>|\s):", $tag ) ) {
		$tag = preg_replace( ':(?=></script>):', " $script_execution", $tag, 1 );
	}

	return $tag;
}
