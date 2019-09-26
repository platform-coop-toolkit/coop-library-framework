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
		'lc_resource_short_title',
		[
			'type'         => 'string',
			'description'  => 'A short title for the resource.',
			'single'       => true,
			'show_in_rest' => true,
		]
	);

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
		'lc_resource_publication_year',
		[
			'type'         => 'integer',
			'description'  => 'The publication year of the resource.',
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
		'lc_resource_publication_name',
		[
			'type'         => 'string',
			'description'  => 'The publication in which the resource appears.',
			'single'       => true,
			'show_in_rest' => true,
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_publication_link',
		[
			'type'         => 'string',
			'description'  => 'A link to the publication in which the resource appears.',
			'single'       => true,
			'show_in_rest' => true,
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_publisher_name',
		[
			'type'         => 'string',
			'description'  => 'The publisher of the resource.',
			'single'       => true,
			'show_in_rest' => true,
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_publisher_locality',
		[
			'type'         => 'string',
			'description'  => 'The town or city where the publisher of the resource is located.',
			'single'       => true,
			'show_in_rest' => true,
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_publisher_country',
		[
			'type'         => 'string',
			'description'  => 'The country where the publisher of the resource is located.',
			'single'       => true,
			'show_in_rest' => true,
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_publisher_link',
		[
			'type'         => 'string',
			'description'  => 'A link to the publisher of the resource.',
			'single'       => true,
			'show_in_rest' => true,
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_doi',
		[
			'type'         => 'string',
			'description'  => 'The DOI for this resource.',
			'single'       => true,
			'show_in_rest' => true,
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_isbn',
		[
			'type'         => 'string',
			'description'  => 'The ISBN for this resource.',
			'single'       => true,
			'show_in_rest' => true,
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_issn',
		[
			'type'         => 'string',
			'description'  => 'The ISSN for this resource.',
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
			'title'        => __( 'Resource Data', 'learning-commons-framework' ),
			'object_types' => [ 'lc_resource' ],
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true,
		]
	);

	$cmb->add_field(
		[
			'name'        => __( 'Short Title', 'learning-commons-framework' ),
			'description' => __( 'A short title for the resource.', 'learning-commons-framework' ),
			'id'          => $prefix . 'short_title',
			'type'        => 'text',
		]
	);

	$cmb->add_field(
		[
			'name'        => __( 'Permanent Link (Required)', 'learning-commons-framework' ),
			'description' => __( 'A permanent link to the resource.', 'learning-commons-framework' ),
			'id'          => $prefix . 'permanent_link',
			'type'        => 'text_url',
			'protocols'   => [ 'http', 'https' ],
			'classes'     => 'cmb-required',
			'attributes'  => [
				'data-validation' => 'true',
				'data-required'   => 'true',
			],
		]
	);

	$cmb->add_field(
		[
			'name'        => __( 'Perma.cc Link', 'learning-commons-framework' ),
			'description' => __( 'A link or links to an archival copy of the resource on <a href="https://perma.cc">Perma.cc</a>. If the resource spans multiple pages on Perma.cc, you may add multiple links.', 'learning-commons-framework' ),
			'id'          => $prefix . 'perma_cc_links',
			'type'        => 'text_url',
			'repeatable'  => true,
			'protocols'   => [ 'http', 'https' ],
			'text'        => [
				'add_row_text' => __( 'Add Link', 'learning-commons-framework' ),
			],
			'attributes'  => [
				'aria-label'      => __( 'Perma.cc Link', 'learning-commons-framework' ),
				'data-validation' => 'true',
				'data-domain'     => 'perma.cc',
			],
		]
	);

	$cmb->add_field(
		[
			'name'        => __( 'Wayback Machine Link', 'learning-commons-framework' ),
			'description' => __( 'A link or links to an archival copy of the resource on the <a href="https://web.archive.org">Wayback Machine</a>. If the resource spans multiple pages on the Wayback Machine, you may add multiple links.', 'learning-commons-framework' ),
			'id'          => $prefix . 'wayback_machine_links',
			'type'        => 'text_url',
			'repeatable'  => true,
			'protocols'   => [ 'http', 'https' ],
			'text'        => [
				'add_row_text' => __( 'Add Link', 'learning-commons-framework' ),
			],
			'attributes'  => [
				'aria-label'      => __( 'Wayback Machine Link', 'learning-commons-framework' ),
				'data-validation' => 'true',
				'data-domain'     => 'web.archive.org',
			],
		]
	);

	// TODO: Don't save any authors if they are empty.
	$cmb->add_field(
		[
			'name'        => __( 'Author', 'learning-commons-framework' ),
			'description' => __( 'The author of the resource.', 'learning-commons-framework' ),
			'id'          => $prefix . 'author',
			'type'        => 'text',
			'repeatable'  => true,
			'text'        => [
				'add_row_text' => __( 'Add Author', 'learning-commons-framework' ),
			],
			'attributes'  => [
				'aria-label' => __( 'Author', 'learning-commons-framework' ),
			],
		]
	);

	// TODO: Don't save any authors if they are empty.
	$cmb->add_field(
		[
			'name'        => __( 'Editor', 'learning-commons-framework' ),
			'description' => __( 'The editor of the resource.', 'learning-commons-framework' ),
			'id'          => $prefix . 'editor',
			'type'        => 'text',
			'repeatable'  => true,
			'text'        => [
				'add_row_text' => __( 'Add Editor', 'learning-commons-framework' ),
			],
			'attributes'  => [
				'aria-label' => __( 'Editor', 'learning-commons-framework' ),
			],
		]
	);

	// TODO: Don't save any translators if they are empty.
	$cmb->add_field(
		[
			'name'        => __( 'Translator', 'learning-commons-framework' ),
			'description' => __( 'The translator of the resource.', 'learning-commons-framework' ),
			'id'          => $prefix . 'translator',
			'type'        => 'text',
			'repeatable'  => true,
			'text'        => [
				'add_row_text' => __( 'Add Translator', 'learning-commons-framework' ),
			],
			'attributes'  => [
				'aria-label' => __( 'Translator', 'learning-commons-framework' ),
			],
		]
	);

	$cmb->add_field(
		[
			'name'        => __( 'Publication Date', 'learning-commons-framework' ),
			'description' => __( 'The publication date of the resource in YYYY-MM-DD format.', 'learning-commons-framework' ),
			'id'          => $prefix . 'publication_date',
			'type'        => 'text_date',
			'date_format' => 'Y-m-d',
			'attributes'  => [
				'data-validation' => 'true',
				'data-datetime'   => 'date',
			],
		]
	);

	// TODO: Validate year.
	$cmb->add_field(
		[
			'name'        => __( 'Publication Year (Required)', 'learning-commons-framework' ),
			'description' => __( 'The publication year of the resource.', 'learning-commons-framework' ),
			'id'          => $prefix . 'publication_year',
			'type'        => 'text',
			'classes'     => 'cmb-required',
			'attributes'  => [
				'data-validation' => 'true',
				'data-required'   => 'true',
			],
		]
	);

	// TODO: Don't save any revisions if they are empty.
	$group_field_id = $cmb->add_field(
		[
			'id'          => $prefix . 'revisions',
			'type'        => 'group',
			'description' => __( 'Revisions of the resource.', 'learning-commons-framework' ),
			'options'     => [
				'group_title'    => __( 'Revision {#}', 'learning-commons-framework' ),
				'add_button'     => __( 'Add Revision', 'learning-commons-framework' ),
				'remove_button'  => __( 'Remove Revision', 'learning-commons-framework' ),
				'sortable'       => true,
				'closed'         => true,
				'remove_confirm' => __( 'Are you sure you want to remove this revision?', 'learning-commons-framework' ),
			],
		]
	);

	$cmb->add_group_field(
		$group_field_id,
		[
			'name'        => __( 'Revision Date', 'learning-commons-framework' ),
			'description' => __( 'The date of this revision in YYYY-MM-DD format.', 'learning-commons-framework' ),
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
			'name'        => __( 'Revision Description', 'learning-commons-framework' ),
			'description' => __( 'A brief description of this revision.', 'learning-commons-framework' ),
			'id'          => $prefix . 'revision_description',
			'type'        => 'textarea_small',
		]
	);

	$cmb->add_field(
		[
			'name'        => __( 'Publication Name', 'learning-commons-framework' ),
			'description' => __( 'The publication in which the resource appears.', 'learning-commons-framework' ),
			'id'          => $prefix . 'publication_name',
			'type'        => 'text',
		]
	);

	$cmb->add_field(
		[
			'name'        => __( 'Publication Link', 'learning-commons-framework' ),
			'description' => __( 'A link to the publication in which the resource appears.', 'learning-commons-framework' ),
			'id'          => $prefix . 'publication_link',
			'type'        => 'text_url',
			'protocols'   => [ 'http', 'https' ],
		]
	);

	$cmb->add_field(
		[
			'name'        => __( 'Publisher Name', 'learning-commons-framework' ),
			'description' => __( 'The publisher of the resource.', 'learning-commons-framework' ),
			'id'          => $prefix . 'publisher_name',
			'type'        => 'text',
		]
	);

	$cmb->add_field(
		[
			'name'        => __( 'Publisher City', 'learning-commons-framework' ),
			'description' => __( 'The town or city where the publisher of the resource is located.', 'learning-commons-framework' ),
			'id'          => $prefix . 'publisher_locality',
			'type'        => 'text',
		]
	);

	$cmb->add_field(
		[
			'name'             => __( 'Publisher Country', 'learning-commons-framework' ),
			'description'      => __( 'The country where the publisher of the resource is located.', 'learning-commons-framework' ),
			'id'               => $prefix . 'publisher_country',
			'type'             => 'select',
			'show_option_none' => true,
			'default'          => '',
			'options'          => \LearningCommonsFramework\Internationalization\get_country_list( get_user_locale() ),
		]
	);

	$cmb->add_field(
		[
			'name'        => __( 'Publisher Link', 'learning-commons-framework' ),
			'description' => __( 'A link to the publisher of the resource.', 'learning-commons-framework' ),
			'id'          => $prefix . 'publisher_link',
			'type'        => 'text_url',
			'protocols'   => [ 'http', 'https' ],
		]
	);

	$cmb->add_field(
		[
			'name'        => __( 'DOI (Digital Object Identifier)', 'learning-commons-framework' ),
			'description' => __( 'The DOI for this resource.', 'learning-commons-framework' ),
			'id'          => $prefix . 'doi',
			'type'        => 'text',
			'attributes'  => [
				'data-validation' => 'true',
				'data-identifier' => 'doi',
			],
		]
	);

	$cmb->add_field(
		[
			'name'        => __( 'ISBN (International Standard Book Number)', 'learning-commons-framework' ),
			'description' => __( 'The ISBN for this resource.', 'learning-commons-framework' ),
			'id'          => $prefix . 'isbn',
			'type'        => 'text',
			'attributes'  => [
				'data-validation' => 'true',
				'data-identifier' => 'isbn',
			],
		]
	);

	$cmb->add_field(
		[
			'name'        => __( 'ISSN (International Standard Serial Number)', 'learning-commons-framework' ),
			'description' => __( 'The ISSN for this resource.', 'learning-commons-framework' ),
			'id'          => $prefix . 'issn',
			'type'        => 'text',
			'attributes'  => [
				'data-validation' => 'true',
				'data-identifier' => 'issn',
			],
		]
	);
}
