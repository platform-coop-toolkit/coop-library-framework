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
			'description'  => 'A link or links to an archival copy of the resource on Perma.cc.',
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
			'description'  => 'A link or links to an archival copy of the resource on the Wayback Machine.',
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
			'description'  => 'The rights under which the resource is distributed.',
			'single'       => true,
			'show_in_rest' => true,
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_custom_rights',
		[
			'type'         => 'string',
			'description'  => 'The custom rights statement under which the resource is distributed.',
			'single'       => true,
			'show_in_rest' => true,
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_author',
		[
			'type'         => 'array',
			'description'  => 'The author of the resource.',
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
			'description'  => 'The editor of the resource.',
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
			'description'  => 'The translator of the resource.',
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
			'description'  => 'The year in which the resource was published.',
			'single'       => true,
			'show_in_rest' => true,
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_publication_month',
		[
			'type'         => 'integer',
			'description'  => 'The month in which the resource was published.',
			'single'       => true,
			'show_in_rest' => true,
		]
	);

	register_post_meta(
		'lc_resource',
		'lc_resource_publication_day',
		[
			'type'         => 'integer',
			'description'  => 'The day of the month on which the resource was published.',
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
 * Register the Resource Data metabox.
 *
 * @return void
 */
function resource_data_init() {
	$prefix = 'lc_resource_';

	$general_info = new_cmb2_box(
		[
			'id'           => '01_resource_data',
			'title'        => __( 'General Info', 'learning-commons-framework' ),
			'object_types' => [ 'lc_resource' ],
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true,
		]
	);

	$about_the_publication = new_cmb2_box(
		[
			'id'           => '02_about_the_publication',
			'title'        => __( 'About the Publication', 'learning-commons-framework' ),
			'object_types' => [ 'lc_resource' ],
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true,
		]
	);

	$about_the_publisher = new_cmb2_box(
		[
			'id'           => '03_about_the_publisher',
			'title'        => __( 'About the Publisher', 'learning-commons-framework' ),
			'object_types' => [ 'lc_resource' ],
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true,
		]
	);

	$rights = new_cmb2_box(
		[
			'id'           => '04_rights',
			'title'        => __( 'Rights', 'learning-commons-framework' ),
			'object_types' => [ 'lc_resource' ],
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true,
		]
	);

	$archival_links = new_cmb2_box(
		[
			'id'           => '05_archival_links',
			'title'        => __( 'Archival Links', 'learning-commons-framework' ),
			'object_types' => [ 'lc_resource' ],
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true,
		]
	);

	$catalog_codes = new_cmb2_box(
		[
			'id'           => '06_catalog_codes',
			'title'        => __( 'Catalog Codes', 'learning-commons-framework' ),
			'object_types' => [ 'lc_resource' ],
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true,
		]
	);

	$general_info->add_field(
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

	$general_info->add_field(
		[
			'name'        => __( 'Short Title', 'learning-commons-framework' ),
			'description' => __( 'A short title for the resource.', 'learning-commons-framework' ),
			'id'          => $prefix . 'short_title',
			'type'        => 'text',
		]
	);

	// TODO: Don't save any authors if they are empty.
	$general_info->add_field(
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
	$general_info->add_field(
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
	$general_info->add_field(
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

	$general_info->add_field(
		[
			'name' => 'Publication Date',
			'type' => 'title',
			'id'   => $prefix . 'publication_date',
		]
	);

	$general_info->add_field(
		[
			'name'        => __( 'Publication Year (Required)', 'learning-commons-framework' ),
			'description' => __( 'The year in which the resource was published.', 'learning-commons-framework' ),
			'id'          => $prefix . 'publication_year',
			'type'        => 'text',
			'classes'     => 'cmb-required',
			'attributes'  => [
				'data-validation' => 'true',
				'data-datetime'   => 'year',
				'data-required'   => 'true',
			],
		]
	);

	$general_info->add_field(
		[
			'name'             => __( 'Publication Month', 'learning-commons-framework' ),
			'description'      => __( 'The month in which the resource was published.', 'learning-commons-framework' ),
			'id'               => $prefix . 'publication_month',
			'type'             => 'select',
			'show_option_none' => true,
			'default'          => '',
			'options'          => [
				'01' => __( 'January', 'learning-commons-framework' ),
				'02' => __( 'February', 'learning-commons-framework' ),
				'03' => __( 'March', 'learning-commons-framework' ),
				'04' => __( 'April', 'learning-commons-framework' ),
				'05' => __( 'May', 'learning-commons-framework' ),
				'06' => __( 'June', 'learning-commons-framework' ),
				'07' => __( 'July', 'learning-commons-framework' ),
				'08' => __( 'August', 'learning-commons-framework' ),
				'09' => __( 'September', 'learning-commons-framework' ),
				'10' => __( 'October', 'learning-commons-framework' ),
				'11' => __( 'November', 'learning-commons-framework' ),
				'12' => __( 'December', 'learning-commons-framework' ),
			],
		]
	);

	$general_info->add_field(
		[
			'name'             => __( 'Publication Day', 'learning-commons-framework' ),
			'description'      => __( 'The day of the month on which the resource was published.', 'learning-commons-framework' ),
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

	$general_info->add_group_field(
		$group_field_id,
		[
			'name'        => __( 'Revision Date', 'learning-commons-framework' ),
			'description' => __( 'The date of this revision in YYYY-MM-DD format.', 'learning-commons-framework' ),
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
			'name'        => __( 'Revision Description', 'learning-commons-framework' ),
			'description' => __( 'A brief description of this revision.', 'learning-commons-framework' ),
			'id'          => 'revision_description',
			'type'        => 'textarea_small',
		]
	);

	$about_the_publication->add_field(
		[
			'name'        => __( 'Publication Name', 'learning-commons-framework' ),
			'description' => __( 'The publication in which the resource appears.', 'learning-commons-framework' ),
			'id'          => $prefix . 'publication_name',
			'type'        => 'text',
		]
	);

	$about_the_publication->add_field(
		[
			'name'        => __( 'Publication Link', 'learning-commons-framework' ),
			'description' => __( 'A link to the publication in which the resource appears.', 'learning-commons-framework' ),
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
			'name'        => __( 'Publisher Name', 'learning-commons-framework' ),
			'description' => __( 'The publisher of the resource.', 'learning-commons-framework' ),
			'id'          => $prefix . 'publisher_name',
			'type'        => 'text',
		]
	);

	$about_the_publisher->add_field(
		[
			'name'        => __( 'Publisher Link', 'learning-commons-framework' ),
			'description' => __( 'A link to the publisher of the resource.', 'learning-commons-framework' ),
			'id'          => $prefix . 'publisher_link',
			'type'        => 'text_url',
			'protocols'   => [ 'http', 'https' ],
		]
	);

	$about_the_publisher->add_field(
		[
			'name'        => __( 'Publisher City', 'learning-commons-framework' ),
			'description' => __( 'The town or city where the publisher of the resource is located.', 'learning-commons-framework' ),
			'id'          => $prefix . 'publisher_locality',
			'type'        => 'text',
		]
	);

	$about_the_publisher->add_field(
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

	$rights->add_field(
		[
			'name'             => __( 'Rights', 'learning-commons-framework' ),
			'description'      => __( 'The rights under which the resource is distributed.', 'learning-commons-framework' ),
			'id'               => $prefix . 'rights',
			'type'             => 'select',
			'show_option_none' => false,
			'default'          => 'all-rights-reserved',
			'options'          => [
				'all-rights-reserved' => __( 'All Rights Reserved', 'learning-commons-framework' ),
				'cc-by'               => __( 'Creative Commons Attribution', 'learning-commons-framework' ),
				'cc-by-nc'            => __( 'Creative Commons Attribution-NonCommercial', 'learning-commons-framework' ),
				'cc-by-nd'            => __( 'Creative Commons Attribution-NoDerivatives', 'learning-commons-framework' ),
				'cc-by-sa'            => __( 'Creative Commons Attribution-ShareAlike', 'learning-commons-framework' ),
				'cc-by-nc-nd'         => __( 'Creative Commons Attribution-NonCommercial-NoDerivatives', 'learning-commons-framework' ),
				'cc-by-nc-sa'         => __( 'Creative Commons Attribution-NonCommercial-ShareAlike', 'learning-commons-framework' ),
				'ecl'                 => __( 'Educational Community License', 'learning-commons-framework' ),
				'cc0'                 => __( 'No Rights Reserved', 'learning-commons-framework' ),
				'public-domain'       => __( 'No Known Copyright', 'learning-commons-framework' ),
				'custom'              => __( 'Custom…', 'learning-commons-framework' ),
			],
		]
	);

	$rights->add_field(
		[
			'name'        => __( 'Custom Rights', 'learning-commons-framework' ),
			'description' => __( 'A custom rights statement under which the resource is distributed.<br />Select &lsquo;Custom…&rsquo; above to enter a custom rights statement.', 'learning-commons-framework' ),
			'id'          => $prefix . 'custom_rights',
			'type'        => 'text',
			'attributes'  => [
				'disabled' => ( get_post_meta( $rights->object_id, 'lc_resource_rights', true ) === 'custom' ) ? false : true,
			],
		]
	);

	$archival_links->add_field(
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

	$archival_links->add_field(
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

	$catalog_codes->add_field(
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

	$catalog_codes->add_field(
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

	$catalog_codes->add_field(
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

/**
 * Generate a key/value pair of days for a given month in a given year.
 *
 * @param int $year  The year in question.
 * @param int $month The month in question
 *
 * @return array
 */
function preload_day_options( $year, $month ) {
	$options = [ '' => __( 'None', 'learning-commons-framework' ) ];
	if ( $year && $month ) {
		$days_in_month = \cal_days_in_month( CAL_GREGORIAN, (int) $month, (int) $year );
		for ( $i = 1; $i < $days_in_month + 1; $i++ ) {
			$val             = ( $i > 9 ) ? $i : "0${i}";
			$options[ $val ] = $i;
		}
	}

	return $options;
}

