<?php
/**
 * Metadata functionality.
 *
 * @package CoopLibraryFramework
 */

namespace CoopLibraryFramework\Metadata;

use Altmetric\Identifiers\Doi;
use Altmetric\Identifiers\Isbn;
use CoopLibraryFramework\HiddenField;
use Symfony\Component\Validator\Constraints\Issn;
use Symfony\Component\Validator\Validation;
use \WP_Error as WP_Error;

use function CoopLibraryFramework\Internationalization\get_language_choices;

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
	add_action( 'init', $n( 'register_hidden_field_type' ) );
	add_action( 'acf/init', $n( 'register_fields' ) );
	add_filter( 'acf/load_field/key=field_5e56f04ee5a20', $n( 'load_publication_day' ) );
	add_filter( 'acf/validate_value/key=field_5e5706d2de4dc', $n( 'validate_doi' ), 10, 4 );
	add_filter( 'acf/validate_value/key=field_5e5706ebde4dd', $n( 'validate_isbn' ), 10, 4 );
	add_filter( 'acf/validate_value/key=field_5e57070ade4de', $n( 'validate_issn' ), 10, 4 );
	add_filter( 'acf/validate_value/key=field_5e57062fee32a', $n( 'validate_perma_cc' ), 10, 4 );
	add_filter( 'acf/validate_value/key=field_5e57065fee32c', $n( 'validate_wayback_machine' ), 10, 4 );
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
 * Register ACF Hidden Field type.
 */
function register_hidden_field_type() {
	new \CoopLibraryFramework\HiddenField();
}

/**
 * Register the Resource Data metabox.
 *
 * @return void
 */
function register_fields() {
	if ( function_exists( 'acf_add_local_field_group' ) ) {
		acf_add_local_field_group(
			array(
				'key'                   => 'group_5e56ec989ce12',
				'title'                 => __( 'General Information', 'coop-library-framework' ),
				'fields'                => array(
					array(
						'key'               => 'field_5e56ecb087e61',
						'label'             => __( 'Link to resource', 'coop-library-framework' ),
						'name'              => 'lc_resource_permanent_link',
						'type'              => 'url',
						'instructions'      => __( 'Web address to access the resource. This information is required.', 'coop-library-framework' ),
						'required'          => 1,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'default_value'     => '',
						'placeholder'       => '',
					),
					array(
						'key'               => 'field_5e62c939ddcb6',
						'label'             => __( 'Resource language', 'coop-library-framework' ),
						'name'              => 'language',
						'type'              => 'select',
						'instructions'      => __( 'The language of the resource.', 'coop-library-framework' ),
						'required'          => 1,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'choices'           => get_language_choices(),
						'default_value'     => array( 'en' ),
						'allow_null'        => 0,
						'multiple'          => 0,
						'ui'                => 0,
						'return_format'     => 'value',
						'ajax'              => 0,
						'placeholder'       => __( 'Choose a language', 'coop-library-framework' ),
					),
					array(
						'key'               => 'field_5e56ed1ee2c88',
						'label'             => __( 'Short title', 'coop-library-framework' ),
						'name'              => 'lc_resource_short_title',
						'type'              => 'text',
						'instructions'      => __( 'Shorter title used in resource listings. Maximum 72 characters.', 'coop-library-framework' ),
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'default_value'     => '',
						'placeholder'       => '',
						'prepend'           => '',
						'append'            => '',
						'maxlength'         => 72,
					),
					array(
						'key'               => 'field_5e56ed5d2edc1',
						'label'             => __( 'Paywall', 'coop-library-framework' ),
						'name'              => 'lc_resource_has_paywall',
						'type'              => 'true_false',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'message'           => __( 'Is this resource behind a paywall?', 'coop-library-framework' ),
						'default_value'     => 0,
						'ui'                => 0,
						'ui_on_text'        => '',
						'ui_off_text'       => '',
					),
					array(
						'key'               => 'field_5e56ed9c93657',
						'label'             => __( 'Authors', 'coop-library-framework' ),
						'name'              => 'lc_resource_author',
						'type'              => 'repeater',
						'instructions'      => __( 'Authors of the resource.', 'coop-library-framework' ),
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'collapsed'         => 'field_5e56edce93658',
						'min'               => 0,
						'max'               => 0,
						'layout'            => 'row',
						'button_label'      => __( 'Add author', 'coop-library-framework' ),
						'sub_fields'        => array(
							array(
								'key'               => 'field_5e56edce93658',
								'label'             => __( 'Author', 'coop-library-framework' ),
								'name'              => 'author',
								'type'              => 'text',
								'instructions'      => '',
								'required'          => 0,
								'conditional_logic' => 0,
								'wrapper'           => array(
									'width' => '',
									'class' => '',
									'id'    => '',
								),
								'default_value'     => '',
								'placeholder'       => '',
								'prepend'           => '',
								'append'            => '',
								'maxlength'         => '',
							),
						),
					),
					array(
						'key'               => 'field_5e56ee4309400',
						'label'             => __( 'Editors', 'coop-library-framework' ),
						'name'              => 'lc_resource_editor',
						'type'              => 'repeater',
						'instructions'      => __( 'Editors of the resource.', 'coop-library-framework' ),
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'collapsed'         => 'field_5e56ee4309401',
						'min'               => 0,
						'max'               => 0,
						'layout'            => 'row',
						'button_label'      => __( 'Add editor', 'coop-library-framework' ),
						'sub_fields'        => array(
							array(
								'key'               => 'field_5e56ee4309401',
								'label'             => __( 'Editor', 'coop-library-framework' ),
								'name'              => 'editor',
								'type'              => 'text',
								'instructions'      => '',
								'required'          => 0,
								'conditional_logic' => 0,
								'wrapper'           => array(
									'width' => '',
									'class' => '',
									'id'    => '',
								),
								'default_value'     => '',
								'placeholder'       => '',
								'prepend'           => '',
								'append'            => '',
								'maxlength'         => '',
							),
						),
					),
					array(
						'key'               => 'field_5e56ee6209402',
						'label'             => __( 'Translators', 'coop-library-framework' ),
						'name'              => 'lc_resource_translator',
						'type'              => 'repeater',
						'instructions'      => __( 'Translators of the resource.', 'coop-library-framework' ),
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'collapsed'         => 'field_5e56ee6209403',
						'min'               => 0,
						'max'               => 0,
						'layout'            => 'row',
						'button_label'      => __( 'Add translator', 'coop-library-framework' ),
						'sub_fields'        => array(
							array(
								'key'               => 'field_5e56ee6209403',
								'label'             => __( 'Translator', 'coop-library-framework' ),
								'name'              => 'translator',
								'type'              => 'text',
								'instructions'      => '',
								'required'          => 0,
								'conditional_logic' => 0,
								'wrapper'           => array(
									'width' => '',
									'class' => '',
									'id'    => '',
								),
								'default_value'     => '',
								'placeholder'       => '',
								'prepend'           => '',
								'append'            => '',
								'maxlength'         => '',
							),
						),
					),
					array(
						'key'               => 'field_5e56ee8953584',
						'label'             => __( 'Publication year', 'coop-library-framework' ),
						'name'              => 'lc_resource_publication_year',
						'type'              => 'select',
						'instructions'      => __( 'The year the resource was published.', 'coop-library-framework' ),
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'choices'           => enumerate_years(),
						'default_value'     => array(),
						'allow_null'        => 1,
						'multiple'          => 0,
						'ui'                => 0,
						'return_format'     => 'value',
						'ajax'              => 0,
						'placeholder'       => __( 'Choose a year', 'coop-library-framework' ),
					),
					array(
						'key'               => 'field_5e56eef559a76',
						'label'             => __( 'Publication month', 'coop-library-framework' ),
						'name'              => 'lc_resource_publication_month',
						'type'              => 'select',
						'instructions'      => __( 'The month the resource was published.', 'coop-library-framework' ),
						'required'          => 0,
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_5e56ee8953584',
									'operator' => '!=empty',
								),
							),
						),
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'choices'           => array(
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
						),
						'default_value'     => array(),
						'allow_null'        => 1,
						'multiple'          => 0,
						'ui'                => 0,
						'return_format'     => 'value',
						'ajax'              => 0,
						'placeholder'       => __( 'Choose a month', 'coop-library-framework' ),
					),
					array(
						'key'               => 'field_5e56f04ee5a20',
						'label'             => __( 'Publication day', 'coop-library-framework' ),
						'name'              => 'lc_resource_publication_day',
						'type'              => 'select',
						'instructions'      => __( 'The numeric day the resource was published.', 'coop-library-framework' ),
						'required'          => 0,
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_5e56ee8953584',
									'operator' => '!=empty',
								),
								array(
									'field'    => 'field_5e56eef559a76',
									'operator' => '!=empty',
								),
							),
						),
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'choices'           => enumerate_days(),
						'default_value'     => array(),
						'allow_null'        => 1,
						'multiple'          => 0,
						'ui'                => 0,
						'return_format'     => 'value',
						'ajax'              => 0,
						'placeholder'       => __( 'Choose a day', 'coop-library-framework' ),
					),
					array(
						'key'               => 'field_5e5ead4ac768d',
						'label'             => __( 'Publication Date', 'coop-library-framework' ),
						'name'              => 'lc_resource_publication_date',
						'type'              => 'hidden',
						'instructions'      => __( 'You can set the publication date for this resource using the year, month and day fields above.', 'coop-library-framework' ),
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'default_value'     => 'ongoing',
						'placeholder'       => '',
						'prepend'           => '',
						'append'            => '',
						'maxlength'         => '',
						'readonly'          => 1,
					),
					array(
						'key'               => 'field_5e56f129489f0',
						'label'             => __( 'Revisions', 'coop-library-framework' ),
						'name'              => 'lc_resource_revisions',
						'type'              => 'repeater',
						'instructions'      => __( 'Revisions of the resource.', 'coop-library-framework' ),
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'collapsed'         => 'field_5e56f13f489f1',
						'min'               => 0,
						'max'               => 0,
						'layout'            => 'block',
						'button_label'      => __( 'Add revision', 'coop-library-framework' ),
						'sub_fields'        => array(
							array(
								'key'               => 'field_5e56f13f489f1',
								'label'             => __( 'Revision date', 'coop-library-framework' ),
								'name'              => 'revision_date',
								'type'              => 'date_picker',
								'instructions'      => __( 'The date of this revision.', 'coop-library-framework' ),
								'required'          => 0,
								'conditional_logic' => 0,
								'wrapper'           => array(
									'width' => '',
									'class' => '',
									'id'    => '',
								),
								'display_format'    => 'F j, Y',
								'return_format'     => 'Y-m-d',
								'first_day'         => 1,
							),
							array(
								'key'               => 'field_5e56f162489f2',
								'label'             => __( 'Revision description', 'coop-library-framework' ),
								'name'              => 'revision_description',
								'type'              => 'textarea',
								'instructions'      => __( 'A brief description of this revision.', 'coop-library-framework' ),
								'required'          => 0,
								'conditional_logic' => 0,
								'wrapper'           => array(
									'width' => '',
									'class' => '',
									'id'    => '',
								),
								'default_value'     => '',
								'placeholder'       => '',
								'maxlength'         => '',
								'rows'              => '',
								'new_lines'         => 'wpautop',
							),
						),
					),
				),
				'location'              => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'lc_resource',
						),
					),
				),
				'menu_order'            => 1,
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen'        => array(
					0 => 'excerpt',
					1 => 'slug',
					2 => 'author',
					3 => 'format',
					4 => 'page_attributes',
					5 => 'featured_image',
					6 => 'categories',
					7 => 'tags',
					8 => 'send-trackbacks',
				),
				'active'                => true,
				'description'           => '',
			)
		);

		acf_add_local_field_group(
			array(
				'key'                   => 'group_5e56ffb5225f9',
				'title'                 => __( 'About the Publication', 'coop-library-framework' ),
				'fields'                => array(
					array(
						'key'               => 'field_5e56ffcbb89c6',
						'label'             => __( 'Publication name', 'coop-library-framework' ),
						'name'              => 'lc_resource_publication_name',
						'type'              => 'text',
						'instructions'      => __( 'Name of the publication in which the resource appears.', 'coop-library-framework' ),
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'default_value'     => '',
						'placeholder'       => '',
						'prepend'           => '',
						'append'            => '',
						'maxlength'         => '',
					),
					array(
						'key'               => 'field_5e56ffffb89c7',
						'label'             => __( 'Publication link', 'coop-library-framework' ),
						'name'              => 'lc_resource_publication_link',
						'type'              => 'url',
						'instructions'      => __( 'Web address for the publication.', 'coop-library-framework' ),
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'default_value'     => '',
						'placeholder'       => '',
					),
				),
				'location'              => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'lc_resource',
						),
					),
				),
				'menu_order'            => 2,
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen'        => '',
				'active'                => true,
				'description'           => '',
			)
		);

		acf_add_local_field_group(
			array(
				'key'                   => 'group_5e5700d50d027',
				'title'                 => __( 'About the Publisher', 'coop-library-framework' ),
				'fields'                => array(
					array(
						'key'               => 'field_5e57011d10192',
						'label'             => __( 'Publisher name', 'coop-library-framework' ),
						'name'              => 'lc_resource_publisher_name',
						'type'              => 'text',
						'instructions'      => __( 'Name of the resource publisher.', 'coop-library-framework' ),
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'default_value'     => '',
						'placeholder'       => '',
						'prepend'           => '',
						'append'            => '',
						'maxlength'         => '',
					),
					array(
						'key'               => 'field_5e57012210193',
						'label'             => __( 'Publisher link', 'coop-library-framework' ),
						'name'              => 'lc_resource_publisher_link',
						'type'              => 'url',
						'instructions'      => __( 'Web address for the publisher.', 'coop-library-framework' ),
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'default_value'     => '',
						'placeholder'       => '',
					),
					array(
						'key'               => 'field_5e57015310194',
						'label'             => __( 'Publisher city', 'coop-library-framework' ),
						'name'              => 'lc_resource_publisher_locality',
						'type'              => 'text',
						'instructions'      => __( 'Town or city where the publisher is located.', 'coop-library-framework' ),
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'default_value'     => '',
						'placeholder'       => '',
						'prepend'           => '',
						'append'            => '',
						'maxlength'         => '',
					),
					array(
						'key'               => 'field_5e57016810195',
						'label'             => __( 'Publisher country', 'coop-library-framework' ),
						'name'              => 'lc_resource_publisher_country',
						'type'              => 'select',
						'instructions'      => __( 'Country where the publisher is located.', 'coop-library-framework' ),
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'choices'           => \CoopLibraryFramework\Internationalization\get_country_list( get_user_locale() ),
						'default_value'     => array(),
						'allow_null'        => 1,
						'multiple'          => 0,
						'ui'                => 0,
						'return_format'     => 'value',
						'ajax'              => 0,
						'placeholder'       => __( 'Select a country', 'coop-library-framework' ),
						'prepend'           => '',
						'append'            => '',
						'maxlength'         => '',
					),
				),
				'location'              => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'lc_resource',
						),
					),
				),
				'menu_order'            => 3,
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen'        => '',
				'active'                => true,
				'description'           => '',
			)
		);

		acf_add_local_field_group(
			array(
				'key'                   => 'group_5e5703458b8a6',
				'title'                 => __( 'Distribution License or Rights', 'coop-library-framework' ),
				'fields'                => array(
					array(
						'key'               => 'field_5e570387d5d39',
						'label'             => __( 'License or rights', 'coop-library-framework' ),
						'name'              => 'lc_resource_rights',
						'type'              => 'select',
						'instructions'      => __( 'License or rights under which the resource is distributed.', 'coop-library-framework' ),
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'choices'           => array(
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
						),
						'default_value'     => array(),
						'allow_null'        => 1,
						'multiple'          => 0,
						'ui'                => 0,
						'return_format'     => 'value',
						'ajax'              => 0,
						'placeholder'       => __( 'Choose a license or rights', 'coop-library-framework' ),
					),
					array(
						'key'               => 'field_5e5703c8d5d3a',
						'label'             => __( 'Custom license or rights', 'coop-library-framework' ),
						'name'              => 'lc_resource_custom_rights',
						'type'              => 'textarea',
						'instructions'      => implode(
							'<br />',
							[
								__( 'Custom license or rights statement.', 'coop-library-framework' ),
								__( 'This is enabled when ‘Custom…’ is selected under ‘License or rights’.', 'coop-library-framework' ),
							]
						),
						'required'          => 1,
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_5e570387d5d39',
									'operator' => '==',
									'value'    => 'custom',
								),
							),
						),
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'default_value'     => '',
						'placeholder'       => '',
						'maxlength'         => '',
						'rows'              => '',
						'new_lines'         => 'wpautop',
					),
				),
				'location'              => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'lc_resource',
						),
					),
				),
				'menu_order'            => 4,
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen'        => '',
				'active'                => true,
				'description'           => '',
			)
		);

		acf_add_local_field_group(
			array(
				'key'                   => 'group_5e57036761b35',
				'title'                 => __( 'Archival Links', 'coop-library-framework' ),
				'fields'                => array(
					array(
						'key'               => 'field_5e5705faee329',
						'label'             => 'Perma.cc',
						'name'              => 'lc_resource_perma_cc_links',
						'type'              => 'repeater',
						'instructions'      => implode(
							'<br />',
							[
								__( 'Links to archive copy on perma.cc. Specify multiple links if the resource spans multiple pages.', 'coop-library-framework' ),
								/* translators: link to https://perma.cc */
								sprintf( __( '%s provides web archiving for scholars, journals, courts, and others.', 'coop-library-framework' ), '<a href="https://perma.cc">Perma.cc</a>' ),
							]
						),
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'collapsed'         => 'field_5e57062fee32a',
						'min'               => 0,
						'max'               => 0,
						'layout'            => 'row',
						'button_label'      => __( 'Add perma.cc link', 'coop-library-framework' ),
						'sub_fields'        => array(
							array(
								'key'               => 'field_5e57062fee32a',
								'label'             => __( 'Perma.cc link', 'coop-library-framework' ),
								'name'              => 'perma_cc_link',
								'type'              => 'url',
								'instructions'      => '',
								'required'          => 1,
								'conditional_logic' => 0,
								'wrapper'           => array(
									'width' => '',
									'class' => '',
									'id'    => '',
								),
								'default_value'     => '',
								'placeholder'       => '',
							),
						),
					),
					array(
						'key'               => 'field_5e57065fee32b',
						'label'             => __( 'Internet Archive', 'coop-library-framework' ),
						'name'              => 'lc_resource_wayback_machine_links',
						'type'              => 'repeater',
						'instructions'      => implode(
							'<br />',
							[
								__( 'Links to archive copy on the Internet Archive. Specify multiple links if the resource spans multiple pages.', 'coop-library-framework' ),
								/* translators: link to https://web.archive.org */
								sprintf( __( '%s provides free and open web archiving.', 'coop-library-framework' ), '<a href="https://web.archive.org">Internet Archive</a>' ),
							]
						),
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'collapsed'         => 'field_5e57065fee32c',
						'min'               => 0,
						'max'               => 0,
						'layout'            => 'row',
						'button_label'      => __( 'Add Internet Archive link', 'coop-library-framework' ),
						'sub_fields'        => array(
							array(
								'key'               => 'field_5e57065fee32c',
								'label'             => __( 'Internet Archive link', 'coop-library-framework' ),
								'name'              => 'wayback_machine_link',
								'type'              => 'url',
								'instructions'      => '',
								'required'          => 1,
								'conditional_logic' => 0,
								'wrapper'           => array(
									'width' => '',
									'class' => '',
									'id'    => '',
								),
								'default_value'     => '',
								'placeholder'       => '',
							),
						),
					),
				),
				'location'              => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'lc_resource',
						),
					),
				),
				'menu_order'            => 5,
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen'        => '',
				'active'                => true,
				'description'           => '',
			)
		);

		acf_add_local_field_group(
			array(
				'key'                   => 'group_5e570377058dd',
				'title'                 => __( 'Catalog Codes', 'coop-library-framework' ),
				'fields'                => array(
					array(
						'key'               => 'field_5e5706d2de4dc',
						'label'             => 'DOI',
						'name'              => 'lc_resource_doi',
						'type'              => 'text',
						'instructions'      => __( 'Digital Object Identifier (or DOI) for this resource.', 'coop-library-framework' ),
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'default_value'     => '',
						'placeholder'       => '',
						'prepend'           => '',
						'append'            => '',
						'maxlength'         => '',
					),
					array(
						'key'               => 'field_5e5706ebde4dd',
						'label'             => 'ISBN',
						'name'              => 'lc_resource_isbn',
						'type'              => 'text',
						'instructions'      => __( 'International Standard Book Number (or ISBN) for this resource.', 'coop-library-framework' ),
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'default_value'     => '',
						'placeholder'       => '',
						'prepend'           => '',
						'append'            => '',
						'maxlength'         => '',
					),
					array(
						'key'               => 'field_5e57070ade4de',
						'label'             => 'ISSN',
						'name'              => 'lc_resource_issn',
						'type'              => 'text',
						'instructions'      => __( 'International Standard Serial Number (or ISSN) for this resource.', 'coop-library-framework' ),
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'default_value'     => '',
						'placeholder'       => '',
						'prepend'           => '',
						'append'            => '',
						'maxlength'         => '',
					),
				),
				'location'              => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'lc_resource',
						),
					),
				),
				'menu_order'            => 6,
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen'        => '',
				'active'                => true,
				'description'           => '',
			)
		);
	}
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
function load_publication_day( $field ) {
	$year  = get_field( 'lc_resource_publication_year', get_the_ID() );
	$month = get_field( 'lc_resource_publication_month', get_the_ID() );

	$field['choices'] = enumerate_days( $year, $month );

	return $field;
}

/**
 * Validate DOI input.
 *
 * @param mixed  $valid Whether or not the value is valid (true / false). Can also be returned as a custom error message (string).
 * @param mixed  $value The value to be saved.
 * @param array  $field An array containing all the field settings.
 * @param string $input The DOM element’s name attribute.
 */
function validate_doi( $valid, $value, $field, $input ) {
	if ( ! $valid ) {
		return $valid;
	}

	if ( '' === $value ) {
		return $valid;
	}

	if ( ! array_filter( Doi::extract( $value ) ) ) {
		$valid = __( 'This is not a valid DOI.', 'coop-library-framework' );
	}

	return $valid;
}

/**
 * Validate ISBN input.
 *
 * @param mixed  $valid Whether or not the value is valid (true / false). Can also be returned as a custom error message (string).
 * @param mixed  $value The value to be saved.
 * @param array  $field An array containing all the field settings.
 * @param string $input The DOM element’s name attribute.
 */
function validate_isbn( $valid, $value, $field, $input ) {
	if ( ! $valid ) {
		return $valid;
	}

	if ( '' === $value ) {
		return $valid;
	}

	if ( ! array_filter( Isbn::extract( $value ) ) ) {
		$valid = __( 'This is not a valid ISBN.', 'coop-library-framework' );
	}

	return $valid;
}

/**
 * Validate ISSN input.
 *
 * @param mixed  $valid Whether or not the value is valid (true / false). Can also be returned as a custom error message (string).
 * @param mixed  $value The value to be saved.
 * @param array  $field An array containing all the field settings.
 * @param string $input The DOM element’s name attribute.
 */
function validate_issn( $valid, $value, $field, $input ) {
	if ( ! $valid ) {
		return $valid;
	}

	if ( '' === $value ) {
		return $valid;
	}

	$validator  = Validation::createValidator();
	$violations = $validator->validate( $value, [ new Issn() ] );
	if ( 0 !== count( $violations ) ) {
		$valid = __( 'This is not a valid ISSN.', 'coop-library-framework' );
	}

	return $valid;
}

/**
 * Validate Perma.cc input.
 *
 * @param mixed  $valid Whether or not the value is valid (true / false). Can also be returned as a custom error message (string).
 * @param mixed  $value The value to be saved.
 * @param array  $field An array containing all the field settings.
 * @param string $input The DOM element’s name attribute.
 */
function validate_perma_cc( $valid, $value, $field, $input ) {
	if ( ! $valid ) {
		return $valid;
	}

	if ( '' === $value ) {
		return $valid;
	}

	$host = wp_parse_url( $value, PHP_URL_HOST );

	if ( 'perma.cc' !== $host ) {
		$valid = __( 'This is not a valid Perma.cc link.', 'coop-library-framework' );
	}

	return $valid;
}

/**
 * Validate Internet Archive link input.
 *
 * @param mixed  $valid Whether or not the value is valid (true / false). Can also be returned as a custom error message (string).
 * @param mixed  $value The value to be saved.
 * @param array  $field An array containing all the field settings.
 * @param string $input The DOM element’s name attribute.
 */
function validate_wayback_machine( $valid, $value, $field, $input ) {
	if ( ! $valid ) {
		return $valid;
	}

	if ( '' === $value ) {
		return $valid;
	}

	$host = wp_parse_url( $value, PHP_URL_HOST );

	if ( ! in_array( $host, [ 'web.archive.org', 'archive.org' ], true ) ) {
		$valid = __( 'This is not a valid Internet Archive link.', 'coop-library-framework' );
	}

	return $valid;
}
