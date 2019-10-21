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
	add_filter( 'use_block_editor_for_post_type', $n( 'supports_block_editor' ), 10, 2 );

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
			'admin_cols'          => [
				'title',
				'language'  => [
					'title'    => __( 'Language', 'learning-commons-framework' ),
					'taxonomy' => 'language',
				],
				'format'    => [
					'title'    => __( 'Format', 'learning-commons-framework' ),
					'taxonomy' => 'lc_format',
				],
				'topic'     => [
					'title'    => __( 'Topics', 'learning-commons-framework' ),
					'taxonomy' => 'lc_topic',
				],
				'published' => [
					'title'    => 'Published',
					'meta_key' => 'lc_resource_publication_year',
				],
			],
			'labels'              => [
				'name'                  => __( 'Resources', 'learning-commons-framework' ),
				'singular_name'         => __( 'Resource', 'learning-commons-framework' ),
				'all_items'             => __( 'All Resources', 'learning-commons-framework' ),
				'archives'              => __( 'Resource Archives', 'learning-commons-framework' ),
				'attributes'            => __( 'Resource Attributes', 'learning-commons-framework' ),
				'insert_into_item'      => __( 'Insert into resource', 'learning-commons-framework' ),
				'uploaded_to_this_item' => __( 'Uploaded to this resource', 'learning-commons-framework' ),
				'filter_items_list'     => __( 'Filter resources list', 'learning-commons-framework' ),
				'items_list_navigation' => __( 'Resources list navigation', 'learning-commons-framework' ),
				'items_list'            => __( 'Resources list', 'learning-commons-framework' ),
				'new_item'              => __( 'New Resource', 'learning-commons-framework' ),
				'add_new'               => __( 'Add New', 'learning-commons-framework' ),
				'add_new_item'          => __( 'Add New Resource', 'learning-commons-framework' ),
				'edit_item'             => __( 'Edit Resource', 'learning-commons-framework' ),
				'view_item'             => __( 'View Resource', 'learning-commons-framework' ),
				'view_items'            => __( 'View Resources', 'learning-commons-framework' ),
				'search_items'          => __( 'Search resources', 'learning-commons-framework' ),
				'not_found'             => __( 'No resources found', 'learning-commons-framework' ),
				'not_found_in_trash'    => __( 'No resources found in trash', 'learning-commons-framework' ),
				'parent_item_colon'     => __( 'Parent Resource:', 'learning-commons-framework' ),
				'menu_name'             => __( 'Resources', 'learning-commons-framework' ),
			],
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-archive',
			'rest_base'           => 'resources',
			'show_in_graphql'     => true,
			'show_in_rest'        => true,
			'supports'            => [ 'title', 'editor', 'custom-fields' ],
			'graphql_single_name' => __( 'Resource', 'learning-commons-framework' ),
			'graphql_plural_name' => __( 'Resources', 'learning-commons-framework' ),
		),
		array(
			'singular' => __( 'Resource', 'learning-commons-framework' ),
			'plural'   => __( 'Resources', 'learning-commons-framework' ),
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
				'name'                       => __( 'Topics', 'learning-commons-framework' ),
				'singular_name'              => __( 'Topic', 'learning-commons-framework' ),
				'search_items'               => __( 'Search Topics', 'learning-commons-framework' ),
				'popular_items'              => __( 'Popular Topics', 'learning-commons-framework' ),
				'all_items'                  => __( 'All Topics', 'learning-commons-framework' ),
				'parent_item'                => __( 'Parent Topic', 'learning-commons-framework' ),
				'parent_item_colon'          => __( 'Parent Topic:', 'learning-commons-framework' ),
				'edit_item'                  => __( 'Edit Topic', 'learning-commons-framework' ),
				'update_item'                => __( 'Update Topic', 'learning-commons-framework' ),
				'view_item'                  => __( 'View Topic', 'learning-commons-framework' ),
				'add_new_item'               => __( 'Add New Topic', 'learning-commons-framework' ),
				'new_item_name'              => __( 'New Topic', 'learning-commons-framework' ),
				'separate_items_with_commas' => __( 'Separate topics with commas', 'learning-commons-framework' ),
				'add_or_remove_items'        => __( 'Add or remove topics', 'learning-commons-framework' ),
				'choose_from_most_used'      => __( 'Choose from the most used topics', 'learning-commons-framework' ),
				'not_found'                  => __( 'No topics found.', 'learning-commons-framework' ),
				'no_terms'                   => __( 'No topics', 'learning-commons-framework' ),
				'menu_name'                  => __( 'Topics', 'learning-commons-framework' ),
				'items_list_navigation'      => __( 'Topics list navigation', 'learning-commons-framework' ),
				'items_list'                 => __( 'Topics list', 'learning-commons-framework' ),
				'back_to_items'              => __( '&larr; Back to Topics', 'learning-commons-framework' ),
			),
			'show_in_graphql'       => true,
			'show_in_rest'          => true,
			'rest_base'             => 'topics',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
			'graphql_single_name'   => __( 'Topic', 'learning-commons-framework' ),
			'graphql_plural_name'   => __( 'Topics', 'learning-commons-framework' ),
		),
		array(
			'singular' => __( 'Topic', 'learning-commons-framework' ),
			'plural'   => __( 'Topics', 'learning-commons-framework' ),
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
				'name'                       => __( 'Goals', 'learning-commons-framework' ),
				'singular_name'              => __( 'Goal', 'learning-commons-framework' ),
				'search_items'               => __( 'Search Goals', 'learning-commons-framework' ),
				'popular_items'              => __( 'Popular Goals', 'learning-commons-framework' ),
				'all_items'                  => __( 'All Goals', 'learning-commons-framework' ),
				'parent_item'                => __( 'Parent Goal', 'learning-commons-framework' ),
				'parent_item_colon'          => __( 'Parent Goal:', 'learning-commons-framework' ),
				'edit_item'                  => __( 'Edit Goal', 'learning-commons-framework' ),
				'update_item'                => __( 'Update Goal', 'learning-commons-framework' ),
				'view_item'                  => __( 'View Goal', 'learning-commons-framework' ),
				'add_new_item'               => __( 'Add New Goal', 'learning-commons-framework' ),
				'new_item_name'              => __( 'New Goal', 'learning-commons-framework' ),
				'separate_items_with_commas' => __( 'Separate goals with commas', 'learning-commons-framework' ),
				'add_or_remove_items'        => __( 'Add or remove goals', 'learning-commons-framework' ),
				'choose_from_most_used'      => __( 'Choose from the most used goals', 'learning-commons-framework' ),
				'not_found'                  => __( 'No goals found.', 'learning-commons-framework' ),
				'no_terms'                   => __( 'No goals', 'learning-commons-framework' ),
				'menu_name'                  => __( 'Goals', 'learning-commons-framework' ),
				'items_list_navigation'      => __( 'Goals list navigation', 'learning-commons-framework' ),
				'items_list'                 => __( 'Goals list', 'learning-commons-framework' ),
				'back_to_items'              => __( '&larr; Back to Goals', 'learning-commons-framework' ),
			),
			'show_in_graphql'       => true,
			'show_in_rest'          => true,
			'rest_base'             => 'goals',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
			'graphql_single_name'   => __( 'Goal', 'learning-commons-framework' ),
			'graphql_plural_name'   => __( 'Goals', 'learning-commons-framework' ),
		),
		array(
			'singular' => __( 'Goal', 'learning-commons-framework' ),
			'plural'   => __( 'Goals', 'learning-commons-framework' ),
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
				'name'                       => __( 'Regions', 'learning-commons-framework' ),
				'singular_name'              => __( 'Region', 'learning-commons-framework' ),
				'search_items'               => __( 'Search Regions', 'learning-commons-framework' ),
				'popular_items'              => __( 'Popular Regions', 'learning-commons-framework' ),
				'all_items'                  => __( 'All Regions', 'learning-commons-framework' ),
				'parent_item'                => __( 'Parent Region', 'learning-commons-framework' ),
				'parent_item_colon'          => __( 'Parent Region:', 'learning-commons-framework' ),
				'edit_item'                  => __( 'Edit Region', 'learning-commons-framework' ),
				'update_item'                => __( 'Update Region', 'learning-commons-framework' ),
				'view_item'                  => __( 'View Region', 'learning-commons-framework' ),
				'add_new_item'               => __( 'Add New Region', 'learning-commons-framework' ),
				'new_item_name'              => __( 'New Region', 'learning-commons-framework' ),
				'separate_items_with_commas' => __( 'Separate regions with commas', 'learning-commons-framework' ),
				'add_or_remove_items'        => __( 'Add or remove regions', 'learning-commons-framework' ),
				'choose_from_most_used'      => __( 'Choose from the most used regions', 'learning-commons-framework' ),
				'not_found'                  => __( 'No regions found.', 'learning-commons-framework' ),
				'no_terms'                   => __( 'No regions', 'learning-commons-framework' ),
				'menu_name'                  => __( 'Regions', 'learning-commons-framework' ),
				'items_list_navigation'      => __( 'Regions list navigation', 'learning-commons-framework' ),
				'items_list'                 => __( 'Regions list', 'learning-commons-framework' ),
				'back_to_items'              => __( '&larr; Back to Regions', 'learning-commons-framework' ),
			),
			'show_in_graphql'       => true,
			'show_in_rest'          => true,
			'rest_base'             => 'regions',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
			'graphql_single_name'   => __( 'Region', 'learning-commons-framework' ),
			'graphql_plural_name'   => __( 'Regions', 'learning-commons-framework' ),
		),
		array(
			'singular' => __( 'Region', 'learning-commons-framework' ),
			'plural'   => __( 'Regions', 'learning-commons-framework' ),
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
				'name'                       => __( 'Sectors', 'learning-commons-framework' ),
				'singular_name'              => __( 'Sector', 'learning-commons-framework' ),
				'search_items'               => __( 'Search Sectors', 'learning-commons-framework' ),
				'popular_items'              => __( 'Popular Sectors', 'learning-commons-framework' ),
				'all_items'                  => __( 'All Sectors', 'learning-commons-framework' ),
				'parent_item'                => __( 'Parent Sector', 'learning-commons-framework' ),
				'parent_item_colon'          => __( 'Parent Sector:', 'learning-commons-framework' ),
				'edit_item'                  => __( 'Edit Sector', 'learning-commons-framework' ),
				'update_item'                => __( 'Update Sector', 'learning-commons-framework' ),
				'view_item'                  => __( 'View Sector', 'learning-commons-framework' ),
				'add_new_item'               => __( 'Add New Sector', 'learning-commons-framework' ),
				'new_item_name'              => __( 'New Sector', 'learning-commons-framework' ),
				'separate_items_with_commas' => __( 'Separate sectors with commas', 'learning-commons-framework' ),
				'add_or_remove_items'        => __( 'Add or remove sectors', 'learning-commons-framework' ),
				'choose_from_most_used'      => __( 'Choose from the most used sectors', 'learning-commons-framework' ),
				'not_found'                  => __( 'No sectors found.', 'learning-commons-framework' ),
				'no_terms'                   => __( 'No sectors', 'learning-commons-framework' ),
				'menu_name'                  => __( 'Sectors', 'learning-commons-framework' ),
				'items_list_navigation'      => __( 'Sectors list navigation', 'learning-commons-framework' ),
				'items_list'                 => __( 'Sectors list', 'learning-commons-framework' ),
				'back_to_items'              => __( '&larr; Back to Sectors', 'learning-commons-framework' ),
			),
			'show_in_graphql'       => true,
			'show_in_rest'          => true,
			'rest_base'             => 'sectors',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
			'graphql_single_name'   => __( 'Sector', 'learning-commons-framework' ),
			'graphql_plural_name'   => __( 'Sectors', 'learning-commons-framework' ),
		),
		array(
			'singular' => __( 'Sector', 'learning-commons-framework' ),
			'plural'   => __( 'Sectors', 'learning-commons-framework' ),
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
				'name'                       => __( 'Co-op Types', 'learning-commons-framework' ),
				'singular_name'              => __( 'Co-op Type', 'learning-commons-framework' ),
				'search_items'               => __( 'Search Co-op Types', 'learning-commons-framework' ),
				'popular_items'              => __( 'Popular Co-op Types', 'learning-commons-framework' ),
				'all_items'                  => __( 'All Co-op Types', 'learning-commons-framework' ),
				'parent_item'                => __( 'Parent Co-op Type', 'learning-commons-framework' ),
				'parent_item_colon'          => __( 'Parent Co-op Type:', 'learning-commons-framework' ),
				'edit_item'                  => __( 'Edit Co-op Type', 'learning-commons-framework' ),
				'update_item'                => __( 'Update Co-op Type', 'learning-commons-framework' ),
				'view_item'                  => __( 'View Co-op Type', 'learning-commons-framework' ),
				'add_new_item'               => __( 'Add New Co-op Type', 'learning-commons-framework' ),
				'new_item_name'              => __( 'New Co-op Type', 'learning-commons-framework' ),
				'separate_items_with_commas' => __( 'Separate co-op types with commas', 'learning-commons-framework' ),
				'add_or_remove_items'        => __( 'Add or remove co-op types', 'learning-commons-framework' ),
				'choose_from_most_used'      => __( 'Choose from the most used co-op types', 'learning-commons-framework' ),
				'not_found'                  => __( 'No co-op types found.', 'learning-commons-framework' ),
				'no_terms'                   => __( 'No co-op types', 'learning-commons-framework' ),
				'menu_name'                  => __( 'Co-op Types', 'learning-commons-framework' ),
				'items_list_navigation'      => __( 'Co-op Types list navigation', 'learning-commons-framework' ),
				'items_list'                 => __( 'Co-op Types list', 'learning-commons-framework' ),
				'back_to_items'              => __( '&larr; Back to Co-op Types', 'learning-commons-framework' ),
			),
			'show_in_graphql'       => true,
			'show_in_rest'          => true,
			'rest_base'             => 'coop-types',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
			'graphql_single_name'   => __( 'Co-op Type', 'learning-commons-framework' ),
			'graphql_plural_name'   => __( 'Co-op Types', 'learning-commons-framework' ),
		),
		array(
			'singular' => __( 'Co-op Type', 'learning-commons-framework' ),
			'plural'   => __( 'Co-op Types', 'learning-commons-framework' ),
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
				'name'                       => __( 'Formats', 'learning-commons-framework' ),
				'singular_name'              => __( 'Format', 'learning-commons-framework' ),
				'search_items'               => __( 'Search Formats', 'learning-commons-framework' ),
				'popular_items'              => __( 'Popular Formats', 'learning-commons-framework' ),
				'all_items'                  => __( 'All Formats', 'learning-commons-framework' ),
				'parent_item'                => __( 'Parent Format', 'learning-commons-framework' ),
				'parent_item_colon'          => __( 'Parent Format:', 'learning-commons-framework' ),
				'edit_item'                  => __( 'Edit Format', 'learning-commons-framework' ),
				'update_item'                => __( 'Update Format', 'learning-commons-framework' ),
				'view_item'                  => __( 'View Format', 'learning-commons-framework' ),
				'add_new_item'               => __( 'Add New Format', 'learning-commons-framework' ),
				'new_item_name'              => __( 'New Format', 'learning-commons-framework' ),
				'separate_items_with_commas' => __( 'Separate formats with commas', 'learning-commons-framework' ),
				'add_or_remove_items'        => __( 'Add or remove formats', 'learning-commons-framework' ),
				'choose_from_most_used'      => __( 'Choose from the most used formats', 'learning-commons-framework' ),
				'not_found'                  => __( 'No formats found.', 'learning-commons-framework' ),
				'no_terms'                   => __( 'No formats', 'learning-commons-framework' ),
				'menu_name'                  => __( 'Formats', 'learning-commons-framework' ),
				'items_list_navigation'      => __( 'Formats list navigation', 'learning-commons-framework' ),
				'items_list'                 => __( 'Formats list', 'learning-commons-framework' ),
				'back_to_items'              => __( '&larr; Back to Formats', 'learning-commons-framework' ),
			),
			'show_in_graphql'       => true,
			'show_in_rest'          => true,
			'rest_base'             => 'formats',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
			'graphql_single_name'   => __( 'Format', 'learning-commons-framework' ),
			'graphql_plural_name'   => __( 'Formats', 'learning-commons-framework' ),
		),
		array(
			'singular' => __( 'Format', 'learning-commons-framework' ),
			'plural'   => __( 'Formats', 'learning-commons-framework' ),
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
		[ 'wp-i18n' ],
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

/**
 * Determine which post types support the Block Editor.
 *
 * @param bool   $use_block_editor Whether or not hte block editor should be used.
 * @param string $post_type The current post type.
 *
 * @return bool
 */
function supports_block_editor( $use_block_editor, $post_type ) {
	if ( 'lc_resource' === $post_type ) {
		return false;
	}

	return $use_block_editor;
}
