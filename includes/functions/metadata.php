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
		'lc_resource_source_language',
		[
			'type'         => 'string',
			'description'  => 'The original language of the resource.',
			'single'       => true,
			'show_in_rest' => true,
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

	$cmb = new_cmb2_box(
		array(
			'id'           => 'resource_data',
			'title'        => __( 'Resource Data', 'learning-commons-framework' ),
			'object_types' => array( 'lc_resource' ),
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true,
		)
	);

	// TODO: Required.
	$cmb->add_field(
		array(
			'name'        => __( 'Permanent Link', 'learning-commons-framework' ),
			'description' => __( 'A permanent link to the resource.', 'learning-commons-framework' ),
			'id'          => $prefix . 'permanent_link',
			'type'        => 'text_url',
			'protocols'   => array( 'http', 'https' ),
		)
	);

	$tmp   = get_terms( 'term_language', [ 'hide_empty' => false ] );
	$langs = [];
	foreach ( $tmp as $lang ) {
		$langs[ str_replace( 'pll_', '', $lang->slug ) ] = $lang->name;
	}

	// TODO: Required.
	$cmb->add_field(
		array(
			'name'             => __( 'Source Language', 'learning-commons-framework' ),
			'description'      => __( 'The original language of the resource.', 'learning-commons-framework' ),
			'id'               => $prefix . 'source_language',
			'type'             => 'select',
			'show_option_none' => true,
			'default'          => 'en',
			'options'          => $langs,
		)
	);

	// TODO: Required.
	$cmb->add_field(
		array(
			'name'        => __( 'Publication Date', 'learning-commons-framework' ),
			'description' => __( 'The publication date of the resource in YYYY-MM-DD format.', 'learning-commons-framework' ),
			'id'          => $prefix . 'publication_date',
			'type'        => 'text_date',
			'date_format' => 'Y-m-d',
		)
	);

	// TODO: Don't save any revisions if they are empty.
	$group_field_id = $cmb->add_field(
		array(
			'id'          => $prefix . 'revisions',
			'type'        => 'group',
			'description' => __( 'Revisions of the resource.', 'learning-commons-framework' ),
			'options'     => array(
				'group_title'    => __( 'Revision {#}', 'learning-commons-framework' ),
				'add_button'     => __( 'Add Another Revision', 'learning-commons-framework' ),
				'remove_button'  => __( 'Remove Revision', 'learning-commons-framework' ),
				'sortable'       => true,
				'closed'         => true,
				'remove_confirm' => esc_html__( 'Are you sure you want to remove this revision?', 'learning-commons-framework' ),
			),
		)
	);

	// TODO: Required.
	$cmb->add_group_field(
		$group_field_id,
		array(
			'name'        => __( 'Revision Date', 'learning-commons-framework' ),
			'description' => __( 'The date of this revision in YYYY-MM-DD format.', 'learning-commons-framework' ),
			'id'          => $prefix . 'revision_date',
			'type'        => 'text_date',
			'date_format' => 'Y-m-d',
		)
	);

	$cmb->add_group_field(
		$group_field_id,
		array(
			'name'        => __( 'Revision Description', 'learning-commons-framework' ),
			'description' => __( 'A brief description of this revision.', 'learning-commons-framework' ),
			'id'          => $prefix . 'revision_description',
			'type'        => 'textarea_small',
		)
	);

	$cmb->add_field(
		array(
			'name'        => __( 'Publisher Name', 'learning-commons-framework' ),
			'description' => __( 'The publisher of the resource.', 'learning-commons-framework' ),
			'id'          => $prefix . 'publisher_name',
			'type'        => 'text',
		)
	);

	$cmb->add_field(
		array(
			'name'        => __( 'Publisher City', 'learning-commons-framework' ),
			'description' => __( 'The town or city where the publisher of the resource is located.', 'learning-commons-framework' ),
			'id'          => $prefix . 'publisher_locality',
			'type'        => 'text',
		)
	);

	$cmb->add_field(
		array(
			'name'        => __( 'Publisher Country', 'learning-commons-framework' ),
			'description' => __( 'The country where the publisher of the resource is located.', 'learning-commons-framework' ),
			'id'          => $prefix . 'publisher_country',
			'type'        => 'text',
		)
	);

	$cmb->add_field(
		array(
			'name'        => __( 'Publisher Link', 'learning-commons-framework' ),
			'description' => __( 'A link to the publisher of the resource.', 'learning-commons-framework' ),
			'id'          => $prefix . 'publisher_link',
			'type'        => 'text_url',
			'protocols'   => array( 'http', 'https' ),
		)
	);
}
