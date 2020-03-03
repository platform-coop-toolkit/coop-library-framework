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
	add_action( 'init', $n( 'resource_data_init' ) );
	add_filter( 'acf/load_field/key=field_5e56f04ee5a20', $n( 'acf_load_publication_day' ) );

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
		'lc_resource_has_paywall',
		[
			'type'         => 'string',
			'description'  => 'Indicates whether or not the resource is behind a paywall.',
			'single'       => true,
			'show_in_rest' => true,
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

	require COOP_LIBRARY_FRAMEWORK_INC . 'acf.php';
}

/**
 * Generate a key/value pair of days for a given month in a given year.
 *
 * @param int $year  The year in question.
 * @param int $month The month in question
 *
 * @return int
 */
function preload_days_in_month( $year = false, $month = false ) {
	if ( $year && $month ) {
		return \cal_days_in_month( CAL_GREGORIAN, (int) $month, (int) $year );
	}

	return 31;
}

/**
 * Build options for years select element.
 *
 * @return array
 */
function enumerate_years() {
	$years = [];
	$y     = gmdate( 'Y' );
	for ( $i = $y; $i >= ( $y - 100 ); $i-- ) {
		$years[ $i ] = $i;
	}
	return $years;
}

/**
 * Build options for days select element.
 *
 * @param int $year  The year in question.
 * @param int $month The month in question
 *
 * @return array
 */
function enumerate_days( $year = false, $month = false ) {
	$days     = [];
	$max_days = preload_days_in_month( $year, $month );
	for ( $i = 1; $i <= $max_days; $i++ ) {
		$days[ str_pad( $i, 2, '0', STR_PAD_LEFT ) ] = $i;
	}
	return $days;
}

/**
 * Modify the day field for an ACF instance on load.
 *
 * @param array $field The array of field options to be modified.
 *
 * @return array
 */
function acf_load_publication_day( $field ) {
	$year  = get_field( 'lc_resource_publication_year', get_the_ID() );
	$month = get_field( 'lc_resource_publication_month', get_the_ID() );

	$field['choices'] = enumerate_days( $year, $month );

	return $field;
}

