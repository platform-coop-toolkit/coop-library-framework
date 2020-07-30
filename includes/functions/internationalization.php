<?php
/**
 * Internationalization functionality.
 *
 * @package CoopLibraryFramework
 */

namespace CoopLibraryFramework\Internationalization;

/**
 * Default setup routine
 *
 * @return void
 */
function setup() {
	$n = function( $function ) {
		return __NAMESPACE__ . "\\$function";
	};

	add_filter( 'pll_copy_post_metas', $n( 'copy_post_metas' ) );
	add_action( 'init', $n( 'add_localization_options_page' ) );
}

/**
 * Determine which post meta should be synchronized between translations.
 *
 * @param array $metas The array of metadata keys to synchronize.
 */
function copy_post_metas( $metas ) {
	$unset = [
		// Link will differ for the translated version.
		'lc_resource_permanent_link',
		// Language wil differ for the translated version.
		'language',
		// Translator will be specific to the translated version.
		'lc_resource_translator',
	];

	foreach ( $metas as $key => $value ) {
		if ( in_array( $value, $unset, true ) ) {
			unset( $metas[ $key ] );
		}
	}

	return $metas;
}


/**
 * Add localization options page.
 *
 * @return void
 */
function add_localization_options_page() {
	if ( function_exists( 'acf_add_options_page' ) ) {
		acf_add_options_page(
			[
				'page_title' => __( 'Localization Settings', 'coop-library-framework' ),
				'menu_title' => __( 'Localization', 'coop-library-framework' ),
				'menu_slug'  => 'localization-settings',
				'capability' => 'manage_options',
				'icon_url'   => 'dashicons-admin-site-alt3',
			]
		);
	}

	if ( function_exists( 'acf_add_local_field_group' ) ) {
		$languages = get_language_choices();
		unset( $languages['en'] );
		acf_add_local_field_group(
			[
				'key'                   => 'group_5e84bc76ae537',
				'title'                 => __( 'Localization Settings', 'coop-library-framework' ),
				'fields'                => [
					[
						'key'               => 'field_5e84bc8dd03ab',
						'label'             => __( 'Enabled Languages', 'coop-library-framework' ),
						'name'              => 'enabled_languages',
						'type'              => 'checkbox',
						'instructions'      => __( 'Select the languages you\'d like to be displayed to site visitors. English content will always be displayed.', 'coop-library-framework' ),
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => [
							'width' => '',
							'class' => '',
							'id'    => '',
						],
						'choices'           => $languages,
						'allow_custom'      => 0,
						'default_value'     => [ 'en' ],
						'layout'            => 'vertical',
						'toggle'            => 0,
						'return_format'     => 'value',
						'save_custom'       => 0,
					],
				],
				'location'              => [
					[
						[
							'param'    => 'options_page',
							'operator' => '==',
							'value'    => 'localization-settings',
						],
					],
				],
				'menu_order'            => 0,
				'position'              => 'normal',
				'style'                 => 'seamless',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen'        => '',
				'active'                => true,
				'description'           => '',
			]
		);
	}
}

/**
 * Load a list of countries in the current locale.
 *
 * @param string $locale The current locale, for example en_US.
 *
 * @return array $countries An associative array of countries.
 */
function get_country_list( $locale = false ) {
	if ( ! $locale ) {
		$locale = get_locale();
	}
	$countries = include COOP_LIBRARY_FRAMEWORK_PATH . 'vendor/umpirsky/country-list/data/' . $locale . '/country.php';
	return $countries;
}

/**
 * Load a list of languages in the current locale.
 *
 * @param string $locale The current locale, for example en_US.
 *
 * @return array $languages An associative array of languages.
 */
function get_language_list( $locale = false ) {
	if ( ! $locale ) {
		$locale = get_locale();
	}
	$languages = include COOP_LIBRARY_FRAMEWORK_PATH . 'vendor/umpirsky/language-list/data/' . $locale . '/language.php';
	return $languages;
}

/**
 * Get language choices for ACF based on what languages are set in Polylang.
 *
 * @param string $locale The current locale, for example en_US.
 *
 * @return array $choices
 */
function get_language_choices( $locale = false ) {
	if ( ! $locale ) {
		$locale = get_locale();
	}
	$locales   = ( function_exists( 'pll_languages_list' ) ) ? pll_languages_list( [ 'fields' => 'slug' ] ) : [ 'en' ];
	$languages = get_language_list( $locale );
	$choices   = [];
	foreach ( $locales as $key ) {
		$choices[ $key ] = $languages[ $key ];
	}
	asort( $choices );
	return $choices;
}

/**
 * Get language choices for ACF based on what languages are set in Polylang.
 *
 * @param string $slug The language slug to look up.
 * @param string $locale The current locale, for example en_US.
 *
 * @return array $choices
 */
function get_localized_language( $slug, $locale = false ) {
	if ( ! $locale ) {
		$locale = get_locale();
	}
	$languages = get_language_list( $locale );

	return $languages[ $slug ];
}
