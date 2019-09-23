<?php
/**
 * Metadata functionality.
 *
 * @package LearningCommonsFramework
 */

namespace LearningCommonsFramework\Metadata;

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

	add_action( 'init', $n( 'register_meta' ) );
	add_action( 'cmb2_admin_init', $n( 'resource_data_init' ) );
}

/**
 * Register the Resource Data custom fields for access via the REST API.
 *
 * @return void
 */
function register_meta() {
	register_post_meta(
		'lc_resource',
		'lc_resource_permanent_link',
		[
			'type'         => 'string',
			'description'  => 'A permanent link to the resource.',
			'single'       => true,
			'show_in_rest' => true,
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_perma_cc_links',
		[
			'type'         => 'string',
			'description'  => 'A link or links to an archival copy of the resource on Perma.cc.',
			'single'       => true,
			'show_in_rest' => [
				'prepare_callback' => 'LearningCommonsFramework\Metadata\prepare_archival_links',
			],
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_wayback_machine_links',
		[
			'type'         => 'string',
			'description'  => 'A link or links to an archival copy of the resource on the Wayback Machine.',
			'single'       => true,
			'show_in_rest' => [
				'prepare_callback' => 'LearningCommonsFramework\Metadata\prepare_archival_links',
			],
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_author',
		[
			'type'         => 'string',
			'description'  => 'The author of the resource.',
			'single'       => true,
			'show_in_rest' => [
				'prepare_callback' => 'LearningCommonsFramework\Metadata\prepare_contributors',
			],
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_editor',
		[
			'type'         => 'string',
			'description'  => 'The editor of the resource.',
			'single'       => true,
			'show_in_rest' => [
				'prepare_callback' => 'LearningCommonsFramework\Metadata\prepare_contributors',
			],
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_translator',
		[
			'type'         => 'string',
			'description'  => 'The translator of the resource.',
			'single'       => true,
			'show_in_rest' => [
				'prepare_callback' => 'LearningCommonsFramework\Metadata\prepare_contributors',
			],
		]
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

	register_post_meta(
		'lc_resource',
		'lc_resource_revisions',
		[
			'type'         => 'string',
			'description'  => 'Revisions of the resource.',
			'single'       => true,
			'show_in_rest' => [
				'prepare_callback' => 'LearningCommonsFramework\Metadata\prepare_revisions',
			],
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_publisher_name',
		[
			'type'         => 'string',
			'description'  => '',
			'single'       => true,
			'show_in_rest' => true,
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_publisher_locality',
		[
			'type'         => 'string',
			'description'  => '',
			'single'       => true,
			'show_in_rest' => true,
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_publisher_country',
		[
			'type'         => 'string',
			'description'  => '',
			'single'       => true,
			'show_in_rest' => true,
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_publisher_link',
		[
			'type'         => 'string',
			'description'  => '',
			'single'       => true,
			'show_in_rest' => true,
		]
	);

}

/**
 * Prepare `lc_resource_authors`, `lc_resource_editors`, and `lc_resource_translators`
 * for REST API access.
 *
 * @param mixed $value The metadata value.
 *
 * @return array
 */
function prepare_contributors( $value ) {
	$result = [];
	if ( is_array( $value ) ) {
		foreach ( $value as $v ) {
			$result[] = $v;
		}
	}
	return $result;
}

/**
 * Prepare `lc_resource_perma_cc_links` and `lc_resource_wayback_machine_links`
 * for REST API access.
 *
 * @param mixed $value The metadata value.
 *
 * @return array
 */
function prepare_archival_links( $value ) {
	$result = [];
	if ( is_array( $value ) ) {
		foreach ( $value as $v ) {
			$result[] = $v;
		}
	}
	return $result;
}

/**
 * Prepare `lc_resource_revisions` for REST API access.
 *
 * @param mixed $value The metadata value.
 *
 * @return array
 */
function prepare_revisions( $value ) {
	$result = [];
	if ( is_array( $value ) ) {
		foreach ( $value as $v ) {
			$result[] = $v['lc_resource_revision_date'] . ': ' . $v['lc_resource_revision_description'];
		}
	}
	return $result;
}

/**
 * Register the Resource Data metabox.
 *
 * @return void
 */
function resource_data_init() {
	$prefix = 'lc_resource_';

	$cmb = new_cmb2_box(
		[
			'id'           => 'resource_data',
			'title'        => pll__( 'Resource Data' ),
			'object_types' => [ 'lc_resource' ],
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true,
		]
	);

	$cmb->add_field(
		[
			'name'        => pll__( 'Permanent Link' ),
			'description' => pll__( 'A permanent link to the resource.' ),
			'id'          => $prefix . 'permanent_link',
			'type'        => 'text_url',
			'protocols'   => [ 'http', 'https' ],
			'attributes'  => [
				'data-validation' => 'true',
				'data-required'   => 'true',
			],
		]
	);

	// TODO: Validate that the URL starts with https://perma.cc/
	$cmb->add_field(
		[
			'name'        => pll__( 'Perma.cc Link', 'learning-commons-framework' ),
			'description' => pll__( 'A link or links to an archival copy of the resource on <a href="https://perma.cc">Perma.cc</a>. If the resource spans multiple pages on Perma.cc, you may add multiple links.', 'learning-commons-framework' ),
			'id'          => $prefix . 'perma_cc_links',
			'type'        => 'text_url',
			'repeatable'  => true,
			'protocols'   => [ 'http', 'https' ],
			'text'        => [
				'add_row_text' => pll__( 'Add Link' ),
			],
			'attributes'  => [
				'aria-label'      => pll__( 'Perma.cc Link' ),
				'data-validation' => 'true',
				'data-domain'     => 'perma.cc',
			],
		]
	);

	// TODO: Validate that the URL starts with https://web.archive.org/
	$cmb->add_field(
		[
			'name'        => pll__( 'Wayback Machine Link', 'learning-commons-framework' ),
			'description' => pll__( 'A link or links to an archival copy of the resource on the <a href="https://web.archive.org">Wayback Machine</a>. If the resource spans multiple pages on the Wayback Machine, you may add multiple links.', 'learning-commons-framework' ),
			'id'          => $prefix . 'wayback_machine_links',
			'type'        => 'text_url',
			'repeatable'  => true,
			'protocols'   => [ 'http', 'https' ],
			'text'        => [
				'add_row_text' => pll__( 'Add Link' ),
			],
			'attributes'  => [
				'aria-label'      => pll__( 'Wayback Machine Link' ),
				'data-validation' => 'true',
				'data-domain'     => 'web.archive.org',
			],
		]
	);

	// TODO: Don't save any authors if they are empty.
	$cmb->add_field(
		[
			'name'        => pll__( 'Author' ),
			'description' => pll__( 'The author of the resource.' ),
			'id'          => $prefix . 'author',
			'type'        => 'text',
			'repeatable'  => true,
			'text'        => [
				'add_row_text' => pll__( 'Add Author' ),
			],
			'attributes'  => [
				'aria-label' => pll__( 'Author' ),
			],
		]
	);

	// TODO: Don't save any authors if they are empty.
	$cmb->add_field(
		[
			'name'        => pll__( 'Editor' ),
			'description' => pll__( 'The editor of the resource.' ),
			'id'          => $prefix . 'editor',
			'type'        => 'text',
			'repeatable'  => true,
			'text'        => [
				'add_row_text' => pll__( 'Add Editor' ),
			],
			'attributes'  => [
				'aria-label' => pll__( 'Editor' ),
			],
		]
	);

	// TODO: Don't save any translators if they are empty.
	$cmb->add_field(
		[
			'name'        => pll__( 'Translator' ),
			'description' => pll__( 'The translator of the resource.' ),
			'id'          => $prefix . 'translator',
			'type'        => 'text',
			'repeatable'  => true,
			'text'        => [
				'add_row_text' => pll__( 'Add Translator' ),
			],
			'attributes'  => [
				'aria-label' => pll__( 'Translator' ),
			],
		]
	);

	$cmb->add_field(
		[
			'name'        => pll__( 'Publication Date' ),
			'description' => pll__( 'The publication date of the resource in YYYY-MM-DD format.' ),
			'id'          => $prefix . 'publication_date',
			'type'        => 'text_date',
			'date_format' => 'Y-m-d',
			'attributes'  => [
				'data-validation' => 'true',
				'data-required'   => 'true',
				'data-datetime'   => 'date',
			],
		]
	);

	// TODO: Don't save any revisions if they are empty.
	$group_field_id = $cmb->add_field(
		[
			'id'          => $prefix . 'revisions',
			'type'        => 'group',
			'description' => pll__( 'Revisions of the resource.' ),
			'options'     => [
				'group_title'    => pll__( 'Revision {#}' ),
				'add_button'     => pll__( 'Add Revision' ),
				'remove_button'  => pll__( 'Remove Revision' ),
				'sortable'       => true,
				'closed'         => true,
				'remove_confirm' => pll__( 'Are you sure you want to remove this revision?' ),
			],
		]
	);

	$cmb->add_group_field(
		$group_field_id,
		[
			'name'        => pll__( 'Revision Date' ),
			'description' => pll__( 'The date of this revision in YYYY-MM-DD format.' ),
			'id'          => $prefix . 'revision_date',
			'type'        => 'text_date',
			'date_format' => 'Y-m-d',
			'attributes'  => [
				'data-validation' => 'true',
				'data-datetime'   => 'date',
			],
		]
	);

	$cmb->add_group_field(
		$group_field_id,
		[
			'name'        => pll__( 'Revision Description' ),
			'description' => pll__( 'A brief description of this revision.' ),
			'id'          => $prefix . 'revision_description',
			'type'        => 'textarea_small',
		]
	);

	$cmb->add_field(
		[
			'name'        => pll__( 'Publisher Name' ),
			'description' => pll__( 'The publisher of the resource.' ),
			'id'          => $prefix . 'publisher_name',
			'type'        => 'text',
		]
	);

	$cmb->add_field(
		[
			'name'        => pll__( 'Publisher City' ),
			'description' => pll__( 'The town or city where the publisher of the resource is located.' ),
			'id'          => $prefix . 'publisher_locality',
			'type'        => 'text',
		]
	);

	$cmb->add_field(
		[
			'name'             => pll__( 'Publisher Country' ),
			'description'      => pll__( 'The country where the publisher of the resource is located.' ),
			'id'               => $prefix . 'publisher_country',
			'type'             => 'select',
			'show_option_none' => true,
			'default'          => '',
			'options'          => \LearningCommonsFramework\Internationalization\get_country_list( get_user_locale() ),
		]
	);

	$cmb->add_field(
		[
			'name'        => pll__( 'Publisher Link' ),
			'description' => pll__( 'A link to the publisher of the resource.' ),
			'id'          => $prefix . 'publisher_link',
			'type'        => 'text_url',
			'protocols'   => [ 'http', 'https' ],
		]
	);
}
