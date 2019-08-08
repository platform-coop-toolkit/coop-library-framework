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
	add_action( 'cmb2_admin_init', $n( 'resource_data_init' ) );
	add_action( 'wp_enqueue_scripts', $n( 'scripts' ) );
	add_action( 'wp_enqueue_scripts', $n( 'styles' ) );
	add_action( 'admin_enqueue_scripts', $n( 'admin_scripts' ) );
	add_action( 'admin_enqueue_scripts', $n( 'admin_styles' ) );

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
			'supports'      => [ 'title', 'editor' ],
		),
		array(
			'singular' => __( 'Resource', 'learning-commons-framework' ),
			'plural'   => __( 'Resources', 'learning-commons-framework' ),
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
 * Register the Resource Data metabox.
 *
 * @return void
 */
function resource_data_init() {
	$prefix = 'lc_resource_';

	$cmb = new_cmb2_box( array(
		'id'            => 'resource_data',
		'title'         => __( 'Resource Data', 'learning-commons-framework' ),
		'object_types'  => array( 'lc_resource', ),
		'context'       => 'normal',
		'priority'      => 'high',
		'show_names'    => true,
	) );

	// TODO: Required.
	$cmb->add_field( array(
		'name'        => __( 'Permanent Link', 'learning-commons-framework' ),
		'description' => __( 'A permanent link to the resource.', 'learning-commons-framework' ),
		'id'          => $prefix . 'permanent_link',
		'type'        => 'text_url',
		'protocols'   => array( 'http', 'https' ),
	) );

	$tmp = get_terms( 'term_language', [ 'hide_empty' => false ] );
	$langs = [];
	foreach($tmp as $lang) {
		$langs[ str_replace('pll_', '', $lang->slug) ] = $lang->name;
	}

	// TODO: Required.
	$cmb->add_field( array(
		'name'             => __( 'Language', 'learning-commons-framework' ),
		'description'      => __( 'The language of the resource.', 'learning-commons-framework' ),
		'id'               => $prefix . 'language',
		'type'             => 'select',
		'show_option_none' => true,
		'default'          => 'en',
		'options'          => $langs,
	) );

	// TODO: Required.
	$cmb->add_field( array(
		'name'        => __( 'Publication Date', 'learning-commons-framework' ),
		'description' => __( 'The publication date of the resource in YYYY-MM-DD format.', 'learning-commons-framework' ),
		'id'          => $prefix . 'publication_date',
		'type'        => 'text_date',
		'date_format' => 'Y-m-d',
	) );

	// TODO: Don't save any revisions if they are empty.
	$group_field_id = $cmb->add_field( array(
		'id'          => $prefix . 'revisions',
		'type'        => 'group',
		'description' => __( 'Revisions of the resource.', 'learning-commons-framework' ),
		'options'     => array(
			'group_title'       => __( 'Revision {#}', 'learning-commons-framework' ),
			'add_button'        => __( 'Add Another Revision', 'learning-commons-framework' ),
			'remove_button'     => __( 'Remove Revision', 'learning-commons-framework' ),
			'sortable'          => true,
			'closed'         => true,
			'remove_confirm' => esc_html__( 'Are you sure you want to remove this revision?', 'learning-commons-framework' ),
		),
	) );

	// TODO: Required.
	$cmb->add_group_field( $group_field_id, array(
		'name'        => __( 'Revision Date', 'learning-commons-framework' ),
		'description' => __( 'The date of this revision in YYYY-MM-DD format.', 'learning-commons-framework' ),
		'id'          => $prefix . 'revision_date',
		'type'        => 'text_date',
		'date_format' => 'Y-m-d',
	) );

	$cmb->add_group_field( $group_field_id, array(
		'name'        => __( 'Revision Description', 'learning-commons-framework' ),
		'description' => __( 'A brief description of this revision.', 'learning-commons-framework' ),
		'id'          => $prefix . 'revision_description',
		'type'        => 'textarea_small',
	) );

	$cmb->add_field( array(
		'name'        => __( 'Publisher Name', 'learning-commons-framework' ),
		'description' => __( 'The publisher of the resource.', 'learning-commons-framework' ),
		'id'          => $prefix . 'publisher_name',
		'type'        => 'text',
	) );

	$cmb->add_field( array(
		'name'        => __( 'Publisher City', 'learning-commons-framework' ),
		'description' => __( 'The town or city where the publisher of the resource is located.', 'learning-commons-framework' ),
		'id'          => $prefix . 'publisher_locality',
		'type'        => 'text',
	) );

	$cmb->add_field( array(
		'name'        => __( 'Publisher Country', 'learning-commons-framework' ),
		'description' => __( 'The country where the publisher of the resource is located.', 'learning-commons-framework' ),
		'id'          => $prefix . 'publisher_country',
		'type'        => 'text',
	) );

	$cmb->add_field( array(
		'name'        => __( 'Publisher Link', 'learning-commons-framework' ),
		'description' => __( 'A link to the publisher of the resource.', 'learning-commons-framework' ),
		'id'          => $prefix . 'publisher_link',
		'type'        => 'text_url',
		'protocols'   => array( 'http', 'https' ),
	) );
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
