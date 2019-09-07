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
	// Show all languages by default
	add_filter( 'pre_get_posts', $n( 'show_all_langs' ) );
	// Ensure resources and topics are translatable.
	add_filter( 'pll_get_post_types', $n( 'add_resource_to_pll' ), 10, 2 );
	add_filter( 'pll_get_taxonomies', $n( 'add_coop_type_to_pll' ), 10, 2 );
	add_filter( 'pll_get_taxonomies', $n( 'add_sector_to_pll' ), 10, 2 );
	add_filter( 'pll_get_taxonomies', $n( 'add_region_to_pll' ), 10, 2 );
	add_filter( 'pll_get_taxonomies', $n( 'add_topic_to_pll' ), 10, 2 );
	add_filter( 'pll_get_taxonomies', $n( 'add_goal_to_pll' ), 10, 2 );
	add_filter( 'pll_get_taxonomies', $n( 'add_format_to_pll' ), 10, 2 );

	do_action( 'learning_commons_framework_loaded' );
}

/**
 * Registers the default textdomain.
 *
 * @return void
 */
function i18n() {
	$locale = apply_filters( 'plugin_locale', get_locale() );
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
			'labels'              => [
				'name'                  => pll__( 'Resources' ),
				'singular_name'         => pll__( 'Resource' ),
				'all_items'             => pll__( 'All Resources' ),
				'archives'              => pll__( 'Resource Archives' ),
				'attributes'            => pll__( 'Resource Attributes' ),
				'insert_into_item'      => pll__( 'Insert into resource' ),
				'uploaded_to_this_item' => pll__( 'Uploaded to this resource' ),
				'filter_items_list'     => pll__( 'Filter resources list' ),
				'items_list_navigation' => pll__( 'Resources list navigation' ),
				'items_list'            => pll__( 'Resources list' ),
				'new_item'              => pll__( 'New Resource' ),
				'add_new'               => pll__( 'Add New' ),
				'add_new_item'          => pll__( 'Add New Resource' ),
				'edit_item'             => pll__( 'Edit Resource' ),
				'view_item'             => pll__( 'View Resource' ),
				'view_items'            => pll__( 'View Resources' ),
				'search_items'          => pll__( 'Search resources' ),
				'not_found'             => pll__( 'No resources found' ),
				'not_found_in_trash'    => pll__( 'No resources found in trash' ),
				'parent_item_colon'     => pll__( 'Parent Resource:' ),
				'menu_name'             => pll__( 'Resources' ),
			],
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-archive',
			'rest_base'           => 'resources',
			'show_in_graphql'     => true,
			'show_in_rest'        => true,
			'supports'            => [ 'title', 'editor', 'custom-fields' ],
			'graphql_single_name' => pll__( 'Resource' ),
			'graphql_plural_name' => pll__( 'Resources' ),
		),
		array(
			'singular' => pll__( 'Resource' ),
			'plural'   => pll__( 'Resources' ),
			'slug'     => 'resources',
		)
	);
}

/**
 * Add the `lc_resource` post type to Polylang, ensuring it is translatable.
 *
 * @param array $post_types An array of post types.
 * @param bool  $is_settings Whether or not we are on the settings page.
 *
 * @return array
 */
function add_resource_to_pll( $post_types, $is_settings ) {
	if ( $is_settings ) {
		unset( $post_types['lc_resource'] );
	} else {
		$post_types['lc_resource'] = 'lc_resource';
	}
	return $post_types;
}

/**
 * Show resources in all languages by default.
 *
 * @param \WP_Query $query The current query.
 *
 * @return void
 */
function show_all_langs( $query ) {
	if ( is_post_type_archive( 'lc_resource' ) ) {
		$query->set( 'lang', '' );
	}
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
				'name'                       => pll__( 'Topics' ),
				'singular_name'              => pll__( 'Topic' ),
				'search_items'               => pll__( 'Search Topics' ),
				'popular_items'              => pll__( 'Popular Topics' ),
				'all_items'                  => pll__( 'All Topics' ),
				'parent_item'                => pll__( 'Parent Topic' ),
				'parent_item_colon'          => pll__( 'Parent Topic:' ),
				'edit_item'                  => pll__( 'Edit Topic' ),
				'update_item'                => pll__( 'Update Topic' ),
				'view_item'                  => pll__( 'View Topic' ),
				'add_new_item'               => pll__( 'Add New Topic' ),
				'new_item_name'              => pll__( 'New Topic' ),
				'separate_items_with_commas' => pll__( 'Separate topics with commas' ),
				'add_or_remove_items'        => pll__( 'Add or remove topics' ),
				'choose_from_most_used'      => pll__( 'Choose from the most used topics' ),
				'not_found'                  => pll__( 'No topics found.' ),
				'no_terms'                   => pll__( 'No topics' ),
				'menu_name'                  => pll__( 'Topics' ),
				'items_list_navigation'      => pll__( 'Topics list navigation' ),
				'items_list'                 => pll__( 'Topics list' ),
				'back_to_items'              => pll__( '&larr; Back to Topics' ),
			),
			'show_in_graphql'       => true,
			'show_in_rest'          => true,
			'rest_base'             => 'topics',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
			'graphql_single_name'   => pll__( 'Topic' ),
			'graphql_plural_name'   => pll__( 'Topics' ),
		),
		array(
			'singular' => pll__( 'Topic' ),
			'plural'   => pll__( 'Topics' ),
			'slug'     => 'topics',
		)
	);
}

/**
 * Add the `lc_topic` taxonomy to Polylang, ensuring it is translatable.
 *
 * @param array $taxonomies An array of taxonomies.
 * @param bool  $is_settings Whether or not we are on the settings page.
 *
 * @return array
 */
function add_topic_to_pll( $taxonomies, $is_settings ) {
	if ( $is_settings ) {
		unset( $taxonomies['lc_topic'] );
	} else {
		$taxonomies['lc_topic'] = 'lc_topic';
	}
	return $taxonomies;
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
			'hierarchical'          => false,
			'labels'                => array(
				'name'                       => pll__( 'Goals' ),
				'singular_name'              => pll__( 'Goal' ),
				'search_items'               => pll__( 'Search Goals' ),
				'popular_items'              => pll__( 'Popular Goals' ),
				'all_items'                  => pll__( 'All Goals' ),
				'parent_item'                => pll__( 'Parent Goal' ),
				'parent_item_colon'          => pll__( 'Parent Goal:' ),
				'edit_item'                  => pll__( 'Edit Goal' ),
				'update_item'                => pll__( 'Update Goal' ),
				'view_item'                  => pll__( 'View Goal' ),
				'add_new_item'               => pll__( 'Add New Goal' ),
				'new_item_name'              => pll__( 'New Goal' ),
				'separate_items_with_commas' => pll__( 'Separate goals with commas' ),
				'add_or_remove_items'        => pll__( 'Add or remove goals' ),
				'choose_from_most_used'      => pll__( 'Choose from the most used goals' ),
				'not_found'                  => pll__( 'No goals found.' ),
				'no_terms'                   => pll__( 'No goals' ),
				'menu_name'                  => pll__( 'Goals' ),
				'items_list_navigation'      => pll__( 'Goals list navigation' ),
				'items_list'                 => pll__( 'Goals list' ),
				'back_to_items'              => pll__( '&larr; Back to Goals' ),
			),
			'show_in_graphql'       => true,
			'show_in_rest'          => true,
			'rest_base'             => 'goals',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
			'graphql_single_name'   => pll__( 'Goal' ),
			'graphql_plural_name'   => pll__( 'Goals' ),
		),
		array(
			'singular' => pll__( 'Goal' ),
			'plural'   => pll__( 'Goals' ),
			'slug'     => 'goals',
		)
	);
}

/**
 * Add the `lc_goal` taxonomy to Polylang, ensuring it is translatable.
 *
 * @param array $taxonomies An array of taxonomies.
 * @param bool  $is_settings Whether or not we are on the settings page.
 *
 * @return array
 */
function add_goal_to_pll( $taxonomies, $is_settings ) {
	if ( $is_settings ) {
		unset( $taxonomies['lc_goal'] );
	} else {
		$taxonomies['lc_goal'] = 'lc_goal';
	}
	return $taxonomies;
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
				'name'                       => pll__( 'Regions' ),
				'singular_name'              => pll__( 'Region' ),
				'search_items'               => pll__( 'Search Regions' ),
				'popular_items'              => pll__( 'Popular Regions' ),
				'all_items'                  => pll__( 'All Regions' ),
				'parent_item'                => pll__( 'Parent Region' ),
				'parent_item_colon'          => pll__( 'Parent Region:' ),
				'edit_item'                  => pll__( 'Edit Region' ),
				'update_item'                => pll__( 'Update Region' ),
				'view_item'                  => pll__( 'View Region' ),
				'add_new_item'               => pll__( 'Add New Region' ),
				'new_item_name'              => pll__( 'New Region' ),
				'separate_items_with_commas' => pll__( 'Separate regions with commas' ),
				'add_or_remove_items'        => pll__( 'Add or remove regions' ),
				'choose_from_most_used'      => pll__( 'Choose from the most used regions' ),
				'not_found'                  => pll__( 'No regions found.' ),
				'no_terms'                   => pll__( 'No regions' ),
				'menu_name'                  => pll__( 'Regions' ),
				'items_list_navigation'      => pll__( 'Regions list navigation' ),
				'items_list'                 => pll__( 'Regions list' ),
				'back_to_items'              => pll__( '&larr; Back to Regions' ),
			),
			'show_in_graphql'       => true,
			'show_in_rest'          => true,
			'rest_base'             => 'regions',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
			'graphql_single_name'   => pll__( 'Region' ),
			'graphql_plural_name'   => pll__( 'Regions' ),
		),
		array(
			'singular' => pll__( 'Region' ),
			'plural'   => pll__( 'Regions' ),
			'slug'     => 'regions',
		)
	);
}

/**
 * Add the `lc_region` taxonomy to Polylang, ensuring it is translatable.
 *
 * @param array $taxonomies An array of taxonomies.
 * @param bool  $is_settings Whether or not we are on the settings page.
 *
 * @return array
 */
function add_region_to_pll( $taxonomies, $is_settings ) {
	if ( $is_settings ) {
		unset( $taxonomies['lc_region'] );
	} else {
		$taxonomies['lc_region'] = 'lc_region';
	}
	return $taxonomies;
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
				'name'                       => pll__( 'Sectors' ),
				'singular_name'              => pll__( 'Sector' ),
				'search_items'               => pll__( 'Search Sectors' ),
				'popular_items'              => pll__( 'Popular Sectors' ),
				'all_items'                  => pll__( 'All Sectors' ),
				'parent_item'                => pll__( 'Parent Sector' ),
				'parent_item_colon'          => pll__( 'Parent Sector:' ),
				'edit_item'                  => pll__( 'Edit Sector' ),
				'update_item'                => pll__( 'Update Sector' ),
				'view_item'                  => pll__( 'View Sector' ),
				'add_new_item'               => pll__( 'Add New Sector' ),
				'new_item_name'              => pll__( 'New Sector' ),
				'separate_items_with_commas' => pll__( 'Separate sectors with commas' ),
				'add_or_remove_items'        => pll__( 'Add or remove sectors' ),
				'choose_from_most_used'      => pll__( 'Choose from the most used sectors' ),
				'not_found'                  => pll__( 'No sectors found.' ),
				'no_terms'                   => pll__( 'No sectors' ),
				'menu_name'                  => pll__( 'Sectors' ),
				'items_list_navigation'      => pll__( 'Sectors list navigation' ),
				'items_list'                 => pll__( 'Sectors list' ),
				'back_to_items'              => pll__( '&larr; Back to Sectors' ),
			),
			'show_in_graphql'       => true,
			'show_in_rest'          => true,
			'rest_base'             => 'sectors',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
			'graphql_single_name'   => pll__( 'Sector' ),
			'graphql_plural_name'   => pll__( 'Sectors' ),
		),
		array(
			'singular' => pll__( 'Sector' ),
			'plural'   => pll__( 'Sectors' ),
			'slug'     => 'sectors',
		)
	);
}

/**
 * Add the `lc_sector` taxonomy to Polylang, ensuring it is translatable.
 *
 * @param array $taxonomies An array of taxonomies.
 * @param bool  $is_settings Whether or not we are on the settings page.
 *
 * @return array
 */
function add_sector_to_pll( $taxonomies, $is_settings ) {
	if ( $is_settings ) {
		unset( $taxonomies['lc_sector'] );
	} else {
		$taxonomies['lc_sector'] = 'lc_sector';
	}
	return $taxonomies;
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
				'name'                       => pll__( 'Co-op Types' ),
				'singular_name'              => pll__( 'Co-op Type' ),
				'search_items'               => pll__( 'Search Co-op Types' ),
				'popular_items'              => pll__( 'Popular Co-op Types' ),
				'all_items'                  => pll__( 'All Co-op Types' ),
				'parent_item'                => pll__( 'Parent Co-op Type' ),
				'parent_item_colon'          => pll__( 'Parent Co-op Type:' ),
				'edit_item'                  => pll__( 'Edit Co-op Type' ),
				'update_item'                => pll__( 'Update Co-op Type' ),
				'view_item'                  => pll__( 'View Co-op Type' ),
				'add_new_item'               => pll__( 'Add New Co-op Type' ),
				'new_item_name'              => pll__( 'New Co-op Type' ),
				'separate_items_with_commas' => pll__( 'Separate co-op types with commas' ),
				'add_or_remove_items'        => pll__( 'Add or remove co-op types' ),
				'choose_from_most_used'      => pll__( 'Choose from the most used co-op types' ),
				'not_found'                  => pll__( 'No co-op types found.' ),
				'no_terms'                   => pll__( 'No co-op types' ),
				'menu_name'                  => pll__( 'Co-op Types' ),
				'items_list_navigation'      => pll__( 'Co-op Types list navigation' ),
				'items_list'                 => pll__( 'Co-op Types list' ),
				'back_to_items'              => pll__( '&larr; Back to Co-op Types' ),
			),
			'show_in_graphql'       => true,
			'show_in_rest'          => true,
			'rest_base'             => 'coop-types',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
			'graphql_single_name'   => pll__( 'Co-op Type' ),
			'graphql_plural_name'   => pll__( 'Co-op Types' ),
		),
		array(
			'singular' => pll__( 'Co-op Type' ),
			'plural'   => pll__( 'Co-op Types' ),
			'slug'     => 'coop-types',
		)
	);
}

/**
 * Add the `lc_coop_type` taxonomy to Polylang, ensuring it is translatable.
 *
 * @param array $taxonomies An array of taxonomies.
 * @param bool  $is_settings Whether or not we are on the settings page.
 *
 * @return array
 */
function add_coop_type_to_pll( $taxonomies, $is_settings ) {
	if ( $is_settings ) {
		unset( $taxonomies['lc_coop_type'] );
	} else {
		$taxonomies['lc_coop_type'] = 'lc_coop_type';
	}
	return $taxonomies;
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
				'name'                       => pll__( 'Formats' ),
				'singular_name'              => pll__( 'Format' ),
				'search_items'               => pll__( 'Search Formats' ),
				'popular_items'              => pll__( 'Popular Formats' ),
				'all_items'                  => pll__( 'All Formats' ),
				'parent_item'                => pll__( 'Parent Format' ),
				'parent_item_colon'          => pll__( 'Parent Format:' ),
				'edit_item'                  => pll__( 'Edit Format' ),
				'update_item'                => pll__( 'Update Format' ),
				'view_item'                  => pll__( 'View Format' ),
				'add_new_item'               => pll__( 'Add New Format' ),
				'new_item_name'              => pll__( 'New Format' ),
				'separate_items_with_commas' => pll__( 'Separate formats with commas' ),
				'add_or_remove_items'        => pll__( 'Add or remove formats' ),
				'choose_from_most_used'      => pll__( 'Choose from the most used formats' ),
				'not_found'                  => pll__( 'No formats found.' ),
				'no_terms'                   => pll__( 'No formats' ),
				'menu_name'                  => pll__( 'Formats' ),
				'items_list_navigation'      => pll__( 'Formats list navigation' ),
				'items_list'                 => pll__( 'Formats list' ),
				'back_to_items'              => pll__( '&larr; Back to Formats' ),
			),
			'show_in_graphql'       => true,
			'show_in_rest'          => true,
			'rest_base'             => 'formats',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
			'graphql_single_name'   => pll__( 'Format' ),
			'graphql_plural_name'   => pll__( 'Formats' ),
		),
		array(
			'singular' => pll__( 'Format' ),
			'plural'   => pll__( 'Formats' ),
			'slug'     => 'formats',
		)
	);
}

/**
 * Add the `lc_format` taxonomy to Polylang, ensuring it is translatable.
 *
 * @param array $taxonomies An array of taxonomies.
 * @param bool  $is_settings Whether or not we are on the settings page.
 *
 * @return array
 */
function add_format_to_pll( $taxonomies, $is_settings ) {
	if ( $is_settings ) {
		unset( $taxonomies['lc_format'] );
	} else {
		$taxonomies['lc_format'] = 'lc_format';
	}
	return $taxonomies;
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
 * @param string $context Context for the script ('admin', 'frontend', or 'shared' )
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
 * @param string $context Context for the script ('admin', 'frontend', or 'shared' )
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
