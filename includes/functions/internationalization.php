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
