<?php
/**
 * Metadata functionality.
 *
 * @package CoopLibraryFramework
 */

namespace CoopLibraryFramework\Metadata;

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
			'description'  => 'Shorter title used in resource listings.',
			'single'       => true,
			'show_in_rest' => true,
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_permanent_link',
		[
			'type'         => 'string',
			'description'  => 'Web address to access the resource.',
			'single'       => true,
			'show_in_rest' => [
				'schema' => [
					'type'   => 'string',
					'format' => 'uri',
				],
			],
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_perma_cc_links',
		[
			'type'         => 'array',
			'description'  => 'Links to archive copy on perma.cc.',
			'single'       => true,
			'show_in_rest' => [
				'schema' => [
					'type'  => 'array',
					'items' => [
						'type'   => 'string',
						'format' => 'uri',
					],
				],
			],
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_wayback_machine_links',
		[
			'type'         => 'array',
			'description'  => 'Links to archive copy on Internet Archive.',
			'single'       => true,
			'show_in_rest' => [
				'schema' => [
					'type'  => 'array',
					'items' => [
						'type'   => 'string',
						'format' => 'uri',
					],
				],
			],
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_rights',
		[
			'type'         => 'string',
			'description'  => 'License or rights under which the resource is distributed.',
			'single'       => true,
			'show_in_rest' => true,
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_custom_rights',
		[
			'type'         => 'string',
			'description'  => 'Custom license or rights statement.',
			'single'       => true,
			'show_in_rest' => true,
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_author',
		[
			'type'         => 'array',
			'description'  => 'Authors of the resource.',
			'single'       => true,
			'show_in_rest' => [
				'schema' => [
					'type'  => 'array',
					'items' => [
						'type' => 'string',
					],
				],
			],
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_editor',
		[
			'type'         => 'array',
			'description'  => 'Editors of the resource.',
			'single'       => true,
			'show_in_rest' => [
				'schema' => [
					'type'  => 'array',
					'items' => [
						'type' => 'string',
					],
				],
			],
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_translator',
		[
			'type'         => 'array',
			'description'  => 'Translators of the resource.',
			'single'       => true,
			'show_in_rest' => [
				'schema' => [
					'type'  => 'array',
					'items' => [
						'type' => 'string',
					],
				],
			],
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_publication_year',
		[
			'type'         => 'integer',
			'description'  => 'The year the resource was published.',
			'single'       => true,
			'show_in_rest' => true,
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_publication_month',
		[
			'type'         => 'integer',
			'description'  => 'The month the resource was published.',
			'single'       => true,
			'show_in_rest' => true,
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_publication_day',
		[
			'type'         => 'integer',
			'description'  => 'The numeric day the resource was published.',
			'single'       => true,
			'show_in_rest' => true,
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_publication_date',
		[
			'type'         => 'string',
			'description'  => 'The date the resource was published.',
			'single'       => true,
			'show_in_rest' => true,
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_revisions',
		[
			'type'         => 'array',
			'description'  => 'Revisions of the resource.',
			'single'       => true,
			'show_in_rest' => [
				'schema' => [
					'type'  => 'array',
					'items' => [
						'type'       => 'object',
						'properties' => [
							'revision_date'        => [
								'type'   => 'string',
								'format' => 'datetime',
							],
							'revision_description' => [
								'type' => 'string',
							],
						],
					],
				],
			],
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_publication_name',
		[
			'type'         => 'string',
			'description'  => 'Name of the publication in which the resource appears.',
			'single'       => true,
			'show_in_rest' => true,
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_publication_link',
		[
			'type'         => 'string',
			'description'  => 'Web address for the publication.',
			'single'       => true,
			'show_in_rest' => true,
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_publisher_name',
		[
			'type'         => 'string',
			'description'  => 'Name of the resource publisher.',
			'single'       => true,
			'show_in_rest' => true,
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_publisher_locality',
		[
			'type'         => 'string',
			'description'  => 'Town or city where the publisher is located.',
			'single'       => true,
			'show_in_rest' => true,
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_publisher_country',
		[
			'type'         => 'string',
			'description'  => 'Country where the publisher is located.',
			'single'       => true,
			'show_in_rest' => true,
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_publisher_link',
		[
			'type'         => 'string',
			'description'  => 'Web address for the publisher.',
			'single'       => true,
			'show_in_rest' => true,
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_doi',
		[
			'type'         => 'string',
			'description'  => 'Digital Object Identifier (or DOI) for this resource.',
			'single'       => true,
			'show_in_rest' => true,
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_isbn',
		[
			'type'         => 'string',
			'description'  => 'International Standard Book Number (or ISBN) for this resource.',
			'single'       => true,
			'show_in_rest' => true,
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_issn',
		[
			'type'         => 'string',
			'description'  => 'International Standard Serial Number (or ISSN) for this resource.',
			'single'       => true,
			'show_in_rest' => true,
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_favorites',
		[
			'type'         => 'integer',
			'description'  => 'The number of times this resource has been favorited.',
			'single'       => true,
			'show_in_rest' => true,
		]
	);
}

/**
 * Register the Resource Data metabox.
 *
 * @return void
 */
function resource_data_init() {
	$prefix = 'lc_resource_';

	$general_info = new_cmb2_box(
		[
			'id'           => '01_resource_data',
			'title'        => __( 'General Information', 'coop-library-framework' ),
			'object_types' => [ 'lc_resource' ],
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true,
		]
	);

	$about_the_publication = new_cmb2_box(
		[
			'id'           => '02_about_the_publication',
			'title'        => __( 'About the Publication', 'coop-library-framework' ),
			'object_types' => [ 'lc_resource' ],
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true,
		]
	);

	$about_the_publisher = new_cmb2_box(
		[
			'id'           => '03_about_the_publisher',
			'title'        => __( 'About the Publisher', 'coop-library-framework' ),
			'object_types' => [ 'lc_resource' ],
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true,
		]
	);

	$rights = new_cmb2_box(
		[
			'id'           => '04_rights',
			'title'        => __( 'Distribution License or Rights', 'coop-library-framework' ),
			'object_types' => [ 'lc_resource' ],
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true,
		]
	);

	$archival_links = new_cmb2_box(
		[
			'id'           => '05_archival_links',
			'title'        => __( 'Archival Links', 'coop-library-framework' ),
			'object_types' => [ 'lc_resource' ],
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true,
		]
	);

	$catalog_codes = new_cmb2_box(
		[
			'id'           => '06_catalog_codes',
			'title'        => __( 'Catalog Codes', 'coop-library-framework' ),
			'object_types' => [ 'lc_resource' ],
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true,
		]
	);

	$general_info->add_field(
		[
			'name'        => __( 'Favorites', 'coop-library-framework' ),
			'description' => __( 'The number of times this resource has been favorited.', 'coop-library-framework' ),
			'id'          => $prefix . 'favorites',
			'type'        => 'hidden',
			'default'     => 0,
			'sanitize_cb' => 'intval',
		]
	);

	$general_info->add_field(
		[
			'name'        => __( 'Link to resource (Required)', 'coop-library-framework' ),
			'description' => __( 'Web address to access the resource. This information is required.', 'coop-library-framework' ),
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

	$general_info->add_field(
		[
			'name'        => __( 'Short title', 'coop-library-framework' ),
			'description' => __( 'Shorter title used in resource listings.', 'coop-library-framework' ),
			'id'          => $prefix . 'short_title',
			'type'        => 'text',
		]
	);

	// TODO: Don't save any authors if they are empty.
	$general_info->add_field(
		[
			'name'        => __( 'Author', 'coop-library-framework' ),
			'description' => __( 'Authors of the resource.', 'coop-library-framework' ),
			'id'          => $prefix . 'author',
			'type'        => 'text',
			'repeatable'  => true,
			'text'        => [
				'add_row_text' => __( 'Add Another Author', 'coop-library-framework' ),
			],
			'attributes'  => [
				'aria-label' => __( 'Author', 'coop-library-framework' ),
			],
		]
	);

	// TODO: Don't save any authors if they are empty.
	$general_info->add_field(
		[
			'name'        => __( 'Editor', 'coop-library-framework' ),
			'description' => __( 'Editors of the resource.', 'coop-library-framework' ),
			'id'          => $prefix . 'editor',
			'type'        => 'text',
			'repeatable'  => true,
			'text'        => [
				'add_row_text' => __( 'Add Another Editor', 'coop-library-framework' ),
			],
			'attributes'  => [
				'aria-label' => __( 'Editor', 'coop-library-framework' ),
			],
		]
	);

	// TODO: Don't save any translators if they are empty.
	$general_info->add_field(
		[
			'name'        => __( 'Translator', 'coop-library-framework' ),
			'description' => __( 'Translators of the resource.', 'coop-library-framework' ),
			'id'          => $prefix . 'translator',
			'type'        => 'text',
			'repeatable'  => true,
			'text'        => [
				'add_row_text' => __( 'Add Another Translator', 'coop-library-framework' ),
			],
			'attributes'  => [
				'aria-label' => __( 'Translator', 'coop-library-framework' ),
			],
		]
	);

	$general_info->add_field(
		[
			'name' => 'Publication date',
			'type' => 'title',
			'id'   => $prefix . 'publication_date_title',
		]
	);

	$general_info->add_field(
		[
			'name'    => 'Publication date',
			'type'    => 'hidden',
			'id'      => $prefix . 'publication_date',
			'default' => 'ongoing',
		]
	);

	$general_info->add_field(
		[
			'name'        => __( 'Publication year', 'coop-library-framework' ),
			'description' => __( 'The year the resource was published.', 'coop-library-framework' ),
			'id'          => $prefix . 'publication_year',
			'type'        => 'text',
			'attributes'  => [
				'data-validation' => 'true',
				'data-datetime'   => 'year',
			],
		]
	);

	$general_info->add_field(
		[
			'name'             => __( 'Publication month', 'coop-library-framework' ),
			'description'      => __( 'The month the resource was published.', 'coop-library-framework' ),
			'id'               => $prefix . 'publication_month',
			'type'             => 'select',
			'show_option_none' => true,
			'default'          => '',
			'options'          => [
				'01' => __( 'January', 'coop-library-framework' ),
				'02' => __( 'February', 'coop-library-framework' ),
				'03' => __( 'March', 'coop-library-framework' ),
				'04' => __( 'April', 'coop-library-framework' ),
				'05' => __( 'May', 'coop-library-framework' ),
				'06' => __( 'June', 'coop-library-framework' ),
				'07' => __( 'July', 'coop-library-framework' ),
				'08' => __( 'August', 'coop-library-framework' ),
				'09' => __( 'September', 'coop-library-framework' ),
				'10' => __( 'October', 'coop-library-framework' ),
				'11' => __( 'November', 'coop-library-framework' ),
				'12' => __( 'December', 'coop-library-framework' ),
			],
		]
	);

	$general_info->add_field(
		[
			'name'             => __( 'Publication day', 'coop-library-framework' ),
			'description'      => __( 'The numeric day the resource was published.', 'coop-library-framework' ),
			'id'               => $prefix . 'publication_day',
			'type'             => 'select',
			'show_option_none' => true,
			'default'          => '',
			'options'          => preload_day_options(
				get_post_meta( $general_info->object_id(), 'lc_resource_publication_year', true ),
				get_post_meta( $general_info->object_id(), 'lc_resource_publication_month', true )
			),
		]
	);

	// TODO: Don't save any revisions if they are empty.
	$group_field_id = $general_info->add_field(
		[
			'id'          => $prefix . 'revisions',
			'type'        => 'group',
			'description' => __( 'Revisions of the resource', 'coop-library-framework' ),
			'options'     => [
				'group_title'    => __( 'Revision {#}', 'coop-library-framework' ),
				'add_button'     => __( 'Add Another Revision', 'coop-library-framework' ),
				'remove_button'  => __( 'Remove Revision', 'coop-library-framework' ),
				'sortable'       => true,
				'closed'         => true,
				'remove_confirm' => __( 'Remove this revision?', 'coop-library-framework' ),
			],
		]
	);

	$general_info->add_group_field(
		$group_field_id,
		[
			'name'        => __( 'Revision date', 'coop-library-framework' ),
			'description' => __( 'The date of this revision in YYYY-MM-DD format.', 'coop-library-framework' ),
			'id'          => 'revision_date',
			'type'        => 'text_date',
			'date_format' => 'Y-m-d',
			'attributes'  => [
				'data-validation' => 'true',
				'data-datetime'   => 'date',
			],
		]
	);

	$general_info->add_group_field(
		$group_field_id,
		[
			'name'        => __( 'Revision description', 'coop-library-framework' ),
			'description' => __( 'A brief description of this revision.', 'coop-library-framework' ),
			'id'          => 'revision_description',
			'type'        => 'textarea_small',
		]
	);

	$about_the_publication->add_field(
		[
			'name'        => __( 'Publication name', 'coop-library-framework' ),
			'description' => __( 'Name of the publication in which the resource appears.', 'coop-library-framework' ),
			'id'          => $prefix . 'publication_name',
			'type'        => 'text',
		]
	);

	$about_the_publication->add_field(
		[
			'name'        => __( 'Publication link', 'coop-library-framework' ),
			'description' => __( 'Web address for the publication.', 'coop-library-framework' ),
			'id'          => $prefix . 'publication_link',
			'type'        => 'text_url',
			'protocols'   => [ 'http', 'https' ],
			'attributes'  => [
				'data-validation' => 'true',
			],
		]
	);

	$about_the_publisher->add_field(
		[
			'name'        => __( 'Publisher name', 'coop-library-framework' ),
			'description' => __( 'Name of the resource publisher.', 'coop-library-framework' ),
			'id'          => $prefix . 'publisher_name',
			'type'        => 'text',
		]
	);

	$about_the_publisher->add_field(
		[
			'name'        => __( 'Publisher link', 'coop-library-framework' ),
			'description' => __( 'Web address for the publisher.', 'coop-library-framework' ),
			'id'          => $prefix . 'publisher_link',
			'type'        => 'text_url',
			'protocols'   => [ 'http', 'https' ],
		]
	);

	$about_the_publisher->add_field(
		[
			'name'        => __( 'Publisher city', 'coop-library-framework' ),
			'description' => __( 'Town or city where the publisher is located.', 'coop-library-framework' ),
			'id'          => $prefix . 'publisher_locality',
			'type'        => 'text',
		]
	);

	$about_the_publisher->add_field(
		[
			'name'             => __( 'Publisher country', 'coop-library-framework' ),
			'description'      => __( 'Country where the publisher is located.', 'coop-library-framework' ),
			'id'               => $prefix . 'publisher_country',
			'type'             => 'select',
			'show_option_none' => true,
			'default'          => '',
			'options'          => \CoopLibraryFramework\Internationalization\get_country_list( get_user_locale() ),
		]
	);

	$rights->add_field(
		[
			'name'             => __( 'License or rights', 'coop-library-framework' ),
			'description'      => __( 'License or rights under which the resource is distributed.', 'coop-library-framework' ),
			'id'               => $prefix . 'rights',
			'type'             => 'select',
			'show_option_none' => __( 'Not specified', 'coop-library-framework' ),
			'default'          => '',
			'options'          => [
				'all-rights-reserved' => __( 'All Rights Reserved', 'coop-library-framework' ),
				'cc-by'               => __( 'Creative Commons Attribution', 'coop-library-framework' ),
				'cc-by-nc'            => __( 'Creative Commons Attribution-NonCommercial', 'coop-library-framework' ),
				'cc-by-nd'            => __( 'Creative Commons Attribution-NoDerivatives', 'coop-library-framework' ),
				'cc-by-sa'            => __( 'Creative Commons Attribution-ShareAlike', 'coop-library-framework' ),
				'cc-by-nc-nd'         => __( 'Creative Commons Attribution-NonCommercial-NoDerivatives', 'coop-library-framework' ),
				'cc-by-nc-sa'         => __( 'Creative Commons Attribution-NonCommercial-ShareAlike', 'coop-library-framework' ),
				'ecl'                 => __( 'Educational Community License', 'coop-library-framework' ),
				'cc0'                 => __( 'No Rights Reserved', 'coop-library-framework' ),
				'public-domain'       => __( 'No Known Copyright', 'coop-library-framework' ),
				'custom'              => __( 'Custom…', 'coop-library-framework' ),
			],
		]
	);

	$rights->add_field(
		[
			'name'        => __( 'Custom license or rights', 'coop-library-framework' ),
			'description' => __( 'Custom license or rights statement.<br />This is enabled when &lsquo;Custom…&rsquo; is selected under &lsquo;License or rights&rsquo;.', 'coop-library-framework' ),
			'id'          => $prefix . 'custom_rights',
			'type'        => 'text',
			'attributes'  => [
				'disabled' => ( get_post_meta( $rights->object_id, 'lc_resource_rights', true ) === 'custom' ) ? false : true,
			],
		]
	);

	$archival_links->add_field(
		[
			'name'        => __( 'Perma.cc', 'coop-library-framework' ),
			'description' => __( 'Links to archive copy on perma.cc. Specify multiple links if the resource spans multiple pages.<br /><a href="https://perma.cc">Perma.cc</a> provides web archiving for scholars, journals, courts, and others.', 'coop-library-framework' ),
			'id'          => $prefix . 'perma_cc_links',
			'type'        => 'text_url',
			'repeatable'  => true,
			'protocols'   => [ 'http', 'https' ],
			'text'        => [
				'add_row_text' => __( 'Add Another Perma.cc Link', 'coop-library-framework' ),
			],
			'attributes'  => [
				'aria-label'      => __( 'Perma.cc Link', 'coop-library-framework' ),
				'data-validation' => 'true',
				'data-domain'     => 'perma.cc',
			],
		]
	);

	$archival_links->add_field(
		[
			'name'        => __( 'Internet Archive', 'coop-library-framework' ),
			'description' => __( 'Links to archive copy on Internet Archive. Specify multiple links if the resource spans multiple pages.<br /><a href="https://web.archive.org">Internet Archive</a> provides free and open web archiving.', 'coop-library-framework' ),
			'id'          => $prefix . 'wayback_machine_links',
			'type'        => 'text_url',
			'repeatable'  => true,
			'protocols'   => [ 'http', 'https' ],
			'text'        => [
				'add_row_text' => __( 'Add Another Internet Archive Link', 'coop-library-framework' ),
			],
			'attributes'  => [
				'aria-label'      => __( 'Internet Archive Link', 'coop-library-framework' ),
				'data-validation' => 'true',
				'data-domain'     => 'web.archive.org',
			],
		]
	);

	$catalog_codes->add_field(
		[
			'name'        => __( 'DOI', 'coop-library-framework' ),
			'description' => __( 'Digital Object Identifier (or DOI) for this resource.', 'coop-library-framework' ),
			'id'          => $prefix . 'doi',
			'type'        => 'text',
			'attributes'  => [
				'data-validation' => 'true',
				'data-identifier' => 'doi',
			],
		]
	);

	$catalog_codes->add_field(
		[
			'name'        => __( 'ISBN', 'coop-library-framework' ),
			'description' => __( 'International Standard Book Number (or ISBN) for this resource.', 'coop-library-framework' ),
			'id'          => $prefix . 'isbn',
			'type'        => 'text',
			'attributes'  => [
				'data-validation' => 'true',
				'data-identifier' => 'isbn',
			],
		]
	);

	$catalog_codes->add_field(
		[
			'name'        => __( 'ISSN', 'coop-library-framework' ),
			'description' => __( 'International Standard Serial Number (or ISSN) for this resource.', 'coop-library-framework' ),
			'id'          => $prefix . 'issn',
			'type'        => 'text',
			'attributes'  => [
				'data-validation' => 'true',
				'data-identifier' => 'issn',
			],
		]
	);
}

/**
 * Generate a key/value pair of days for a given month in a given year.
 *
 * @param int $year  The year in question.
 * @param int $month The month in question
 *
 * @return array
 */
function preload_day_options( $year, $month ) {
	$options = [ '' => __( 'None', 'coop-library-framework' ) ];
	if ( $year && $month ) {
		$days_in_month = \cal_days_in_month( CAL_GREGORIAN, (int) $month, (int) $year );
		for ( $i = 1; $i < $days_in_month + 1; $i++ ) {
			$val             = ( $i > 9 ) ? $i : "0${i}";
			$options[ $val ] = $i;
		}
	}

	return $options;
}
