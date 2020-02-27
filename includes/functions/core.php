<?php
/**
 * Core plugin functionality.
 *
 * @package CoopLibraryFramework
 */

namespace CoopLibraryFramework\Core;

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
	add_action( 'init', $n( 'coop_type_init' ) );
	add_action( 'init', $n( 'sector_init' ) );
	add_action( 'init', $n( 'region_init' ) );
	add_action( 'init', $n( 'topic_init' ) );
	add_action( 'init', $n( 'goal_init' ) );
	add_action( 'init', $n( 'format_init' ) );

	add_action( 'wp_enqueue_scripts', $n( 'scripts' ) );
	add_action( 'wp_enqueue_scripts', $n( 'styles' ) );
	add_action( 'admin_enqueue_scripts', $n( 'admin_scripts' ) );
	add_action( 'admin_enqueue_scripts', $n( 'admin_styles' ) );

	// Editor styles. add_editor_style() doesn't work outside of a theme.
	add_filter( 'mce_css', $n( 'mce_css' ) );
	// Hook to allow async or defer on asset loading.
	add_filter( 'script_loader_tag', $n( 'script_loader_tag' ), 10, 2 );

	// Disable inaccessible sortable JavaScript for term order.
	add_filter( 'wp_fancy_term_order', '__return_false' );

	do_action( 'coop_library_framework_loaded' );
}

/**
 * Registers the default textdomain.
 *
 * @return void
 */
function i18n() {
	$locale = apply_filters( 'plugin_locale', get_locale() );
	load_textdomain( 'coop-library-framework', WP_LANG_DIR . '/coop-library-framework/coop-library-framework-' . $locale . '.mo' );
	load_plugin_textdomain( 'coop-library-framework', false, plugin_basename( COOP_LIBRARY_FRAMEWORK_PATH ) . '/languages/' );
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
			'admin_cols'          => [
				'title',
				'language'  => [
					'title'    => __( 'Language', 'coop-library-framework' ),
					'taxonomy' => 'language',
				],
				'format'    => [
					'title'    => __( 'Format', 'coop-library-framework' ),
					'taxonomy' => 'lc_format',
				],
				'topic'     => [
					'title'    => __( 'Topics', 'coop-library-framework' ),
					'taxonomy' => 'lc_topic',
				],
				'published' => [
					'title'    => 'Published',
					'meta_key' => 'lc_resource_publication_year',
				],
				'published' => [
					'title'      => 'Date Added',
					'post_field' => 'post_date',
				],
			],
			'labels'              => [
				'name'                  => __( 'Resources', 'coop-library-framework' ),
				'singular_name'         => __( 'Resource', 'coop-library-framework' ),
				'all_items'             => __( 'All Resources', 'coop-library-framework' ),
				'archives'              => __( 'Resource Archives', 'coop-library-framework' ),
				'attributes'            => __( 'Resource Attributes', 'coop-library-framework' ),
				'insert_into_item'      => __( 'Insert into resource', 'coop-library-framework' ),
				'uploaded_to_this_item' => __( 'Uploaded to this resource', 'coop-library-framework' ),
				'filter_items_list'     => __( 'Filter resources list', 'coop-library-framework' ),
				'items_list_navigation' => __( 'Resources list navigation', 'coop-library-framework' ),
				'items_list'            => __( 'Resources list', 'coop-library-framework' ),
				'new_item'              => __( 'New Resource', 'coop-library-framework' ),
				'add_new'               => __( 'Add New', 'coop-library-framework' ),
				'add_new_item'          => __( 'Add New Resource', 'coop-library-framework' ),
				'edit_item'             => __( 'Edit Resource', 'coop-library-framework' ),
				'view_item'             => __( 'View Resource', 'coop-library-framework' ),
				'view_items'            => __( 'View Resources', 'coop-library-framework' ),
				'search_items'          => __( 'Search resources', 'coop-library-framework' ),
				'not_found'             => __( 'No resources found', 'coop-library-framework' ),
				'not_found_in_trash'    => __( 'No resources found in trash', 'coop-library-framework' ),
				'parent_item_colon'     => __( 'Parent Resource:', 'coop-library-framework' ),
				'menu_name'             => __( 'Resources', 'coop-library-framework' ),
			],
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-archive',
			'rest_base'           => 'resources',
			'show_in_graphql'     => true,
			'show_in_rest'        => true,
			'supports'            => [ 'title', 'editor', 'custom-fields' ],
			'graphql_single_name' => __( 'Resource', 'coop-library-framework' ),
			'graphql_plural_name' => __( 'Resources', 'coop-library-framework' ),
		),
		array(
			'singular' => __( 'Resource', 'coop-library-framework' ),
			'plural'   => __( 'Resources', 'coop-library-framework' ),
			'slug'     => 'resources',
		)
	);
}

/**
 * Registers the `lc_topic` taxonomy,
 * for use with 'lc_resource'.
 */
function topic_init() {
	register_extended_taxonomy(
		'lc_topic',
		array( 'lc_resource' ),
		array(
			'hierarchical'          => true,
			'labels'                => array(
				'name'                       => __( 'Topics', 'coop-library-framework' ),
				'singular_name'              => __( 'Topic', 'coop-library-framework' ),
				'search_items'               => __( 'Search Topics', 'coop-library-framework' ),
				'popular_items'              => __( 'Popular Topics', 'coop-library-framework' ),
				'all_items'                  => __( 'All Topics', 'coop-library-framework' ),
				'parent_item'                => __( 'Parent Topic', 'coop-library-framework' ),
				'parent_item_colon'          => __( 'Parent Topic:', 'coop-library-framework' ),
				'edit_item'                  => __( 'Edit Topic', 'coop-library-framework' ),
				'update_item'                => __( 'Update Topic', 'coop-library-framework' ),
				'view_item'                  => __( 'View Topic', 'coop-library-framework' ),
				'add_new_item'               => __( 'Add New Topic', 'coop-library-framework' ),
				'new_item_name'              => __( 'New Topic', 'coop-library-framework' ),
				'separate_items_with_commas' => __( 'Separate topics with commas', 'coop-library-framework' ),
				'add_or_remove_items'        => __( 'Add or remove topics', 'coop-library-framework' ),
				'choose_from_most_used'      => __( 'Choose from the most used topics', 'coop-library-framework' ),
				'not_found'                  => __( 'No topics found.', 'coop-library-framework' ),
				'no_terms'                   => __( 'No topics', 'coop-library-framework' ),
				'menu_name'                  => __( 'Topics', 'coop-library-framework' ),
				'items_list_navigation'      => __( 'Topics list navigation', 'coop-library-framework' ),
				'items_list'                 => __( 'Topics list', 'coop-library-framework' ),
				'back_to_items'              => __( '&larr; Back to Topics', 'coop-library-framework' ),
			),
			'show_in_graphql'       => true,
			'show_in_rest'          => true,
			'rest_base'             => 'topics',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
			'graphql_single_name'   => __( 'Topic', 'coop-library-framework' ),
			'graphql_plural_name'   => __( 'Topics', 'coop-library-framework' ),
		),
		array(
			'singular' => __( 'Topic', 'coop-library-framework' ),
			'plural'   => __( 'Topics', 'coop-library-framework' ),
			'slug'     => 'topics',
		)
	);
}

/**
 * Registers the `lc_goal` taxonomy,
 * for use with 'lc_resource'.
 */
function goal_init() {
	register_extended_taxonomy(
		'lc_goal',
		array( 'lc_resource' ),
		array(
			'hierarchical'          => true,
			'labels'                => array(
				'name'                       => __( 'Goals', 'coop-library-framework' ),
				'singular_name'              => __( 'Goal', 'coop-library-framework' ),
				'search_items'               => __( 'Search Goals', 'coop-library-framework' ),
				'popular_items'              => __( 'Popular Goals', 'coop-library-framework' ),
				'all_items'                  => __( 'All Goals', 'coop-library-framework' ),
				'parent_item'                => __( 'Parent Goal', 'coop-library-framework' ),
				'parent_item_colon'          => __( 'Parent Goal:', 'coop-library-framework' ),
				'edit_item'                  => __( 'Edit Goal', 'coop-library-framework' ),
				'update_item'                => __( 'Update Goal', 'coop-library-framework' ),
				'view_item'                  => __( 'View Goal', 'coop-library-framework' ),
				'add_new_item'               => __( 'Add New Goal', 'coop-library-framework' ),
				'new_item_name'              => __( 'New Goal', 'coop-library-framework' ),
				'separate_items_with_commas' => __( 'Separate goals with commas', 'coop-library-framework' ),
				'add_or_remove_items'        => __( 'Add or remove goals', 'coop-library-framework' ),
				'choose_from_most_used'      => __( 'Choose from the most used goals', 'coop-library-framework' ),
				'not_found'                  => __( 'No goals found.', 'coop-library-framework' ),
				'no_terms'                   => __( 'No goals', 'coop-library-framework' ),
				'menu_name'                  => __( 'Goals', 'coop-library-framework' ),
				'items_list_navigation'      => __( 'Goals list navigation', 'coop-library-framework' ),
				'items_list'                 => __( 'Goals list', 'coop-library-framework' ),
				'back_to_items'              => __( '&larr; Back to Goals', 'coop-library-framework' ),
			),
			'show_in_graphql'       => true,
			'show_in_rest'          => true,
			'rest_base'             => 'goals',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
			'graphql_single_name'   => __( 'Goal', 'coop-library-framework' ),
			'graphql_plural_name'   => __( 'Goals', 'coop-library-framework' ),
		),
		array(
			'singular' => __( 'Goal', 'coop-library-framework' ),
			'plural'   => __( 'Goals', 'coop-library-framework' ),
			'slug'     => 'goals',
		)
	);
}

/**
 * Registers the `lc_region` taxonomy,
 * for use with 'lc_resource'.
 */
function region_init() {
	register_extended_taxonomy(
		'lc_region',
		array( 'lc_resource' ),
		array(
			'hierarchical'          => true,
			'labels'                => array(
				'name'                       => __( 'Regions', 'coop-library-framework' ),
				'singular_name'              => __( 'Region', 'coop-library-framework' ),
				'search_items'               => __( 'Search Regions', 'coop-library-framework' ),
				'popular_items'              => __( 'Popular Regions', 'coop-library-framework' ),
				'all_items'                  => __( 'All Regions', 'coop-library-framework' ),
				'parent_item'                => __( 'Parent Region', 'coop-library-framework' ),
				'parent_item_colon'          => __( 'Parent Region:', 'coop-library-framework' ),
				'edit_item'                  => __( 'Edit Region', 'coop-library-framework' ),
				'update_item'                => __( 'Update Region', 'coop-library-framework' ),
				'view_item'                  => __( 'View Region', 'coop-library-framework' ),
				'add_new_item'               => __( 'Add New Region', 'coop-library-framework' ),
				'new_item_name'              => __( 'New Region', 'coop-library-framework' ),
				'separate_items_with_commas' => __( 'Separate regions with commas', 'coop-library-framework' ),
				'add_or_remove_items'        => __( 'Add or remove regions', 'coop-library-framework' ),
				'choose_from_most_used'      => __( 'Choose from the most used regions', 'coop-library-framework' ),
				'not_found'                  => __( 'No regions found.', 'coop-library-framework' ),
				'no_terms'                   => __( 'No regions', 'coop-library-framework' ),
				'menu_name'                  => __( 'Regions', 'coop-library-framework' ),
				'items_list_navigation'      => __( 'Regions list navigation', 'coop-library-framework' ),
				'items_list'                 => __( 'Regions list', 'coop-library-framework' ),
				'back_to_items'              => __( '&larr; Back to Regions', 'coop-library-framework' ),
			),
			'show_in_graphql'       => true,
			'show_in_rest'          => true,
			'rest_base'             => 'regions',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
			'graphql_single_name'   => __( 'Region', 'coop-library-framework' ),
			'graphql_plural_name'   => __( 'Regions', 'coop-library-framework' ),
		),
		array(
			'singular' => __( 'Region', 'coop-library-framework' ),
			'plural'   => __( 'Regions', 'coop-library-framework' ),
			'slug'     => 'regions',
		)
	);
}

/**
 * Registers the `lc_sector` taxonomy,
 * for use with 'lc_resource'.
 */
function sector_init() {
	register_extended_taxonomy(
		'lc_sector',
		array( 'lc_resource' ),
		array(
			'hierarchical'          => true,
			'labels'                => array(
				'name'                       => __( 'Sectors', 'coop-library-framework' ),
				'singular_name'              => __( 'Sector', 'coop-library-framework' ),
				'search_items'               => __( 'Search Sectors', 'coop-library-framework' ),
				'popular_items'              => __( 'Popular Sectors', 'coop-library-framework' ),
				'all_items'                  => __( 'All Sectors', 'coop-library-framework' ),
				'parent_item'                => __( 'Parent Sector', 'coop-library-framework' ),
				'parent_item_colon'          => __( 'Parent Sector:', 'coop-library-framework' ),
				'edit_item'                  => __( 'Edit Sector', 'coop-library-framework' ),
				'update_item'                => __( 'Update Sector', 'coop-library-framework' ),
				'view_item'                  => __( 'View Sector', 'coop-library-framework' ),
				'add_new_item'               => __( 'Add New Sector', 'coop-library-framework' ),
				'new_item_name'              => __( 'New Sector', 'coop-library-framework' ),
				'separate_items_with_commas' => __( 'Separate sectors with commas', 'coop-library-framework' ),
				'add_or_remove_items'        => __( 'Add or remove sectors', 'coop-library-framework' ),
				'choose_from_most_used'      => __( 'Choose from the most used sectors', 'coop-library-framework' ),
				'not_found'                  => __( 'No sectors found.', 'coop-library-framework' ),
				'no_terms'                   => __( 'No sectors', 'coop-library-framework' ),
				'menu_name'                  => __( 'Sectors', 'coop-library-framework' ),
				'items_list_navigation'      => __( 'Sectors list navigation', 'coop-library-framework' ),
				'items_list'                 => __( 'Sectors list', 'coop-library-framework' ),
				'back_to_items'              => __( '&larr; Back to Sectors', 'coop-library-framework' ),
			),
			'show_in_graphql'       => true,
			'show_in_rest'          => true,
			'rest_base'             => 'sectors',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
			'graphql_single_name'   => __( 'Sector', 'coop-library-framework' ),
			'graphql_plural_name'   => __( 'Sectors', 'coop-library-framework' ),
		),
		array(
			'singular' => __( 'Sector', 'coop-library-framework' ),
			'plural'   => __( 'Sectors', 'coop-library-framework' ),
			'slug'     => 'sectors',
		)
	);
}

/**
 * Registers the `lc_sector` taxonomy,
 * for use with 'lc_resource'.
 */
function coop_type_init() {
	register_extended_taxonomy(
		'lc_coop_type',
		array( 'lc_resource' ),
		array(
			'hierarchical'          => true,
			'labels'                => array(
				'name'                       => __( 'Co-op Types', 'coop-library-framework' ),
				'singular_name'              => __( 'Co-op Type', 'coop-library-framework' ),
				'search_items'               => __( 'Search Co-op Types', 'coop-library-framework' ),
				'popular_items'              => __( 'Popular Co-op Types', 'coop-library-framework' ),
				'all_items'                  => __( 'All Co-op Types', 'coop-library-framework' ),
				'parent_item'                => __( 'Parent Co-op Type', 'coop-library-framework' ),
				'parent_item_colon'          => __( 'Parent Co-op Type:', 'coop-library-framework' ),
				'edit_item'                  => __( 'Edit Co-op Type', 'coop-library-framework' ),
				'update_item'                => __( 'Update Co-op Type', 'coop-library-framework' ),
				'view_item'                  => __( 'View Co-op Type', 'coop-library-framework' ),
				'add_new_item'               => __( 'Add New Co-op Type', 'coop-library-framework' ),
				'new_item_name'              => __( 'New Co-op Type', 'coop-library-framework' ),
				'separate_items_with_commas' => __( 'Separate co-op types with commas', 'coop-library-framework' ),
				'add_or_remove_items'        => __( 'Add or remove co-op types', 'coop-library-framework' ),
				'choose_from_most_used'      => __( 'Choose from the most used co-op types', 'coop-library-framework' ),
				'not_found'                  => __( 'No co-op types found.', 'coop-library-framework' ),
				'no_terms'                   => __( 'No co-op types', 'coop-library-framework' ),
				'menu_name'                  => __( 'Co-op Types', 'coop-library-framework' ),
				'items_list_navigation'      => __( 'Co-op Types list navigation', 'coop-library-framework' ),
				'items_list'                 => __( 'Co-op Types list', 'coop-library-framework' ),
				'back_to_items'              => __( '&larr; Back to Co-op Types', 'coop-library-framework' ),
			),
			'show_in_graphql'       => true,
			'show_in_rest'          => true,
			'rest_base'             => 'coop-types',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
			'graphql_single_name'   => __( 'Co-op Type', 'coop-library-framework' ),
			'graphql_plural_name'   => __( 'Co-op Types', 'coop-library-framework' ),
		),
		array(
			'singular' => __( 'Co-op Type', 'coop-library-framework' ),
			'plural'   => __( 'Co-op Types', 'coop-library-framework' ),
			'slug'     => 'coop-types',
		)
	);
}

/**
 * Registers the `lc_format` taxonomy,
 * for use with 'lc_resource'.
 */
function format_init() {
	register_extended_taxonomy(
		'lc_format',
		array( 'lc_resource' ),
		array(
			'hierarchical'          => true,
			'labels'                => array(
				'name'                       => __( 'Formats', 'coop-library-framework' ),
				'singular_name'              => __( 'Format', 'coop-library-framework' ),
				'search_items'               => __( 'Search Formats', 'coop-library-framework' ),
				'popular_items'              => __( 'Popular Formats', 'coop-library-framework' ),
				'all_items'                  => __( 'All Formats', 'coop-library-framework' ),
				'parent_item'                => __( 'Parent Format', 'coop-library-framework' ),
				'parent_item_colon'          => __( 'Parent Format:', 'coop-library-framework' ),
				'edit_item'                  => __( 'Edit Format', 'coop-library-framework' ),
				'update_item'                => __( 'Update Format', 'coop-library-framework' ),
				'view_item'                  => __( 'View Format', 'coop-library-framework' ),
				'add_new_item'               => __( 'Add New Format', 'coop-library-framework' ),
				'new_item_name'              => __( 'New Format', 'coop-library-framework' ),
				'separate_items_with_commas' => __( 'Separate formats with commas', 'coop-library-framework' ),
				'add_or_remove_items'        => __( 'Add or remove formats', 'coop-library-framework' ),
				'choose_from_most_used'      => __( 'Choose from the most used formats', 'coop-library-framework' ),
				'not_found'                  => __( 'No formats found.', 'coop-library-framework' ),
				'no_terms'                   => __( 'No formats', 'coop-library-framework' ),
				'menu_name'                  => __( 'Formats', 'coop-library-framework' ),
				'items_list_navigation'      => __( 'Formats list navigation', 'coop-library-framework' ),
				'items_list'                 => __( 'Formats list', 'coop-library-framework' ),
				'back_to_items'              => __( '&larr; Back to Formats', 'coop-library-framework' ),
			),
			'show_in_graphql'       => true,
			'show_in_rest'          => true,
			'rest_base'             => 'formats',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
			'graphql_single_name'   => __( 'Format', 'coop-library-framework' ),
			'graphql_plural_name'   => __( 'Formats', 'coop-library-framework' ),
		),
		array(
			'singular' => __( 'Format', 'coop-library-framework' ),
			'plural'   => __( 'Formats', 'coop-library-framework' ),
			'slug'     => 'formats',
		)
	);
}

/**
 * Initializes the plugin and fires an action other plugins can hook into.
 *
 * @return void
 */
function init() {
	do_action( 'coop_library_framework_init' );
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
 * @param string $context Context for the script ('admin', 'frontend', or 'shared' )
 *
 * @return string|WP_Error URL
 */
function script_url( $script, $context ) {
	if ( ! in_array( $context, get_enqueue_contexts(), true ) ) {
		return new WP_Error( 'invalid_enqueue_context', 'Invalid $context specified in CoopLibraryFramework script loader.' );
	}

	return COOP_LIBRARY_FRAMEWORK_URL . "dist/js/${script}.js";

}

/**
 * Generate an URL to a stylesheet, taking into account whether SCRIPT_DEBUG is enabled.
 *
 * @param string $stylesheet Stylesheet file name (no .css extension)
 * @param string $context Context for the script ('admin', 'frontend', or 'shared' )
 *
 * @return string URL
 */
function style_url( $stylesheet, $context ) {
	if ( ! in_array( $context, get_enqueue_contexts(), true ) ) {
		return new WP_Error( 'invalid_enqueue_context', 'Invalid $context specified in CoopLibraryFramework stylesheet loader.' );
	}

	return COOP_LIBRARY_FRAMEWORK_URL . "dist/css/${stylesheet}.css";
}

/**
 * Enqueue scripts for front-end.
 *
 * @return void
 */
function scripts() {
	wp_enqueue_script(
		'coop_library_framework_shared',
		script_url( 'shared', 'shared' ),
		[],
		COOP_LIBRARY_FRAMEWORK_VERSION,
		true
	);

	wp_enqueue_script(
		'coop_library_framework_frontend',
		script_url( 'frontend', 'frontend' ),
		[],
		COOP_LIBRARY_FRAMEWORK_VERSION,
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
		'coop_library_framework_shared',
		script_url( 'shared', 'shared' ),
		[],
		COOP_LIBRARY_FRAMEWORK_VERSION,
		true
	);

	wp_enqueue_script(
		'coop_library_framework_admin',
		script_url( 'admin', 'admin' ),
		[ 'wp-a11y', 'wp-i18n' ],
		COOP_LIBRARY_FRAMEWORK_VERSION,
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
		'coop_library_framework_shared',
		style_url( 'shared-style', 'shared' ),
		[],
		COOP_LIBRARY_FRAMEWORK_VERSION
	);

	if ( is_admin() ) {
		wp_enqueue_style(
			'coop_library_framework_admin',
			style_url( 'admin-style', 'admin' ),
			[],
			COOP_LIBRARY_FRAMEWORK_VERSION
		);
	} else {
		wp_enqueue_style(
			'coop_library_framework_frontend',
			style_url( 'style', 'frontend' ),
			[],
			COOP_LIBRARY_FRAMEWORK_VERSION
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
		'coop_library_framework_shared',
		style_url( 'shared-style', 'shared' ),
		[],
		COOP_LIBRARY_FRAMEWORK_VERSION
	);

	wp_enqueue_style(
		'coop_library_framework_admin',
		style_url( 'admin-style', 'admin' ),
		[],
		COOP_LIBRARY_FRAMEWORK_VERSION
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

	return $stylesheets . COOP_LIBRARY_FRAMEWORK_URL . ( ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ?
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
