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

	// Editor styles. add_editor_style() doesn't work outside of a theme.
	add_filter( 'mce_css', $n( 'mce_css' ) );
	// Hook to allow async or defer on asset loading.
	add_filter( 'script_loader_tag', $n( 'script_loader_tag' ), 10, 2 );

	// Filter messages for `lc-resource` post_type.
	add_filter( 'post_updated_messages', $n( 'resource_updated_messages' ) );

	do_action( 'LEARNING_COMMONS_FRAMEWORK_loaded' );
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
	register_post_type( 'lc-resource', array(
		'labels'                => array(
			'name'                  => __( 'Resources', 'learning-commons-framework' ),
			'singular_name'         => __( 'Resource', 'learning-commons-framework' ),
			'all_items'             => __( 'All Resources', 'learning-commons-framework' ),
			'archives'              => __( 'Resource Archives', 'learning-commons-framework' ),
			'attributes'            => __( 'Resource Attributes', 'learning-commons-framework' ),
			'insert_into_item'      => __( 'Insert into Resource', 'learning-commons-framework' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Resource', 'learning-commons-framework' ),
			'featured_image'        => _x( 'Featured Image', 'lc-resource', 'learning-commons-framework' ),
			'set_featured_image'    => _x( 'Set featured image', 'lc-resource', 'learning-commons-framework' ),
			'remove_featured_image' => _x( 'Remove featured image', 'lc-resource', 'learning-commons-framework' ),
			'use_featured_image'    => _x( 'Use as featured image', 'lc-resource', 'learning-commons-framework' ),
			'filter_items_list'     => __( 'Filter Resources list', 'learning-commons-framework' ),
			'items_list_navigation' => __( 'Resources list navigation', 'learning-commons-framework' ),
			'items_list'            => __( 'Resources list', 'learning-commons-framework' ),
			'new_item'              => __( 'New Resource', 'learning-commons-framework' ),
			'add_new'               => __( 'Add New', 'learning-commons-framework' ),
			'add_new_item'          => __( 'Add New Resource', 'learning-commons-framework' ),
			'edit_item'             => __( 'Edit Resource', 'learning-commons-framework' ),
			'view_item'             => __( 'View Resource', 'learning-commons-framework' ),
			'view_items'            => __( 'View Resources', 'learning-commons-framework' ),
			'search_items'          => __( 'Search Resources', 'learning-commons-framework' ),
			'not_found'             => __( 'No Resources found', 'learning-commons-framework' ),
			'not_found_in_trash'    => __( 'No Resources found in trash', 'learning-commons-framework' ),
			'parent_item_colon'     => __( 'Parent Resource:', 'learning-commons-framework' ),
			'menu_name'             => __( 'Resources', 'learning-commons-framework' ),
		),
		'public'                => true,
		'hierarchical'          => false,
		'show_ui'               => true,
		'show_in_nav_menus'     => true,
		'supports'              => array( 'title', 'editor' ),
		'has_archive'           => true,
		'rewrite'               => true,
		'query_var'             => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-archive',
		'show_in_rest'          => true,
		'rest_base'             => 'lc-resource',
		'rest_controller_class' => 'WP_REST_Posts_Controller',
	) );
}

/**
 * Sets the post updated messages for the `lc-resource` post type.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `lc-resource` post type.
 */
function resource_updated_messages( $messages ) {
	global $post;

	$permalink = get_permalink( $post );

	$messages['lc-resource'] = array(
		0  => '', // Unused. Messages start at index 1.
		/* translators: %s: post permalink */
		1  => sprintf( __( 'Resource updated. <a target="_blank" href="%s">View Resource</a>', 'learning-commons-framework' ), esc_url( $permalink ) ),
		2  => __( 'Custom field updated.', 'learning-commons-framework' ),
		3  => __( 'Custom field deleted.', 'learning-commons-framework' ),
		4  => __( 'Resource updated.', 'learning-commons-framework' ),
		/* translators: %s: date and time of the revision */
		5  => isset( $_GET['revision'] ) ? sprintf( __( 'Resource restored to revision from %s', 'learning-commons-framework' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		/* translators: %s: post permalink */
		6  => sprintf( __( 'Resource published. <a href="%s">View Resource</a>', 'learning-commons-framework' ), esc_url( $permalink ) ),
		7  => __( 'Resource saved.', 'learning-commons-framework' ),
		/* translators: %s: post permalink */
		8  => sprintf( __( 'Resource submitted. <a target="_blank" href="%s">Preview Resource</a>', 'learning-commons-framework' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		/* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
		9  => sprintf( __( 'Resource scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Resource</a>', 'learning-commons-framework' ),
		date_i18n( __( 'M j, Y @ G:i', 'learning-commons-framework' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
		/* translators: %s: post permalink */
		10 => sprintf( __( 'Resource draft updated. <a target="_blank" href="%s">Preview Resource</a>', 'learning-commons-framework' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
	);

	return $messages;
}

/**
 * Initializes the plugin and fires an action other plugins can hook into.
 *
 * @return void
 */
function init() {
	do_action( 'LEARNING_COMMONS_FRAMEWORK_init' );
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

	return "dist/js/${script}.js";

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
		'LEARNING_COMMONS_FRAMEWORK_shared',
		script_url( 'shared', 'shared' ),
		[],
		LEARNING_COMMONS_FRAMEWORK_VERSION,
		true
	);

	wp_enqueue_script(
		'LEARNING_COMMONS_FRAMEWORK_frontend',
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
		'LEARNING_COMMONS_FRAMEWORK_shared',
		script_url( 'shared', 'shared' ),
		[],
		LEARNING_COMMONS_FRAMEWORK_VERSION,
		true
	);

	wp_enqueue_script(
		'LEARNING_COMMONS_FRAMEWORK_admin',
		script_url( 'admin', 'admin' ),
		[],
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
		'LEARNING_COMMONS_FRAMEWORK_shared',
		style_url( 'shared-style', 'shared' ),
		[],
		LEARNING_COMMONS_FRAMEWORK_VERSION
	);

	if ( is_admin() ) {
		wp_enqueue_style(
			'LEARNING_COMMONS_FRAMEWORK_admin',
			style_url( 'admin-style', 'admin' ),
			[],
			LEARNING_COMMONS_FRAMEWORK_VERSION
		);
	} else {
		wp_enqueue_style(
			'LEARNING_COMMONS_FRAMEWORK_frontend',
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
		'LEARNING_COMMONS_FRAMEWORK_shared',
		style_url( 'shared-style', 'shared' ),
		[],
		LEARNING_COMMONS_FRAMEWORK_VERSION
	);

	wp_enqueue_style(
		'LEARNING_COMMONS_FRAMEWORK_admin',
		style_url( 'admin-style', 'admin' ),
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
		return $tag; // _doing_it_wrong()?
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
