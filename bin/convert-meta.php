<?php
/**
 * Convert metadata to new (1.0.0-alpha.3) format.
 *
 * @package CoopLibraryFramework
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Convert serialized metadata to ACF Repeater style metadata.
 *
 * @param int        $post_id The ID of the post being updated.
 * @param string     $meta_key The parent meta key.
 * @param array|bool $meta_value An array of unserialized meta values if present, or false.
 * @param string     $sub_key The sub-key where the new value should be added.
 * @param string     $meta_key_id The ACF field ID of the meta key.
 * @param string     $sub_key_id The ACF field ID of the sub key.
 *
 * @return void
 */
function convert_repeater( $post_id, $meta_key, $meta_value, $sub_key, $meta_key_id, $sub_key_id ) {
	if ( is_array( $meta_value ) ) {
		foreach ( $meta_value as $index => $value ) {
			update_post_meta( $post_id, "{$meta_key}_{$index}_{$subkey}", $value );
			update_post_meta( $post_id, "_{$meta_key}_{$index}_{$sub_key}", $sub_key_id );
		}

		update_post_meta( $post_id, $meta_key, count( $meta_value ) );
		update_post_meta( $post_id, "_$meta_key", $meta_key_id );

		echo "Updated '$meta_key' for resource ID $post_id.\n"; // @codingStandardsIgnoreLine
	} elseif ( get_post_meta( $post_id, "_$meta_key" ) === $meta_key_id ) {
		echo "No update needed to '$meta_key' for resource ID $post_id.\n"; // @codingStandardsIgnoreLine
	} else {
		echo "No update needed to '$meta_key' for resource ID $post_id.\n"; // @codingStandardsIgnoreLine
	}
}

$defaults = [
	'fields'         => 'ids',
	'posts_per_page' => -1,
	'post_type'      => 'lc_resource',
	'meta_compare'   => 'EXISTS',
];

$author_resources = get_posts(
	wp_parse_args( [ 'meta_key' => 'lc_resource_author' ], $defaults )
);

if ( array_filter( $author_resources ) ) {
	foreach ( $author_resources as $id ) {
		$authors = get_post_meta( $id, 'lc_resource_author', true );
		convert_repeater( $id, 'lc_resource_author', $authors, 'author', 'field_5e56ed9c93657', 'field_5e56edce93658' );
	}
} else {
	echo "No conversions needed for 'lc_resource_author'.\n";
}

$editor_resources = get_posts(
	wp_parse_args( [ 'meta_key' => 'lc_resource_editor' ], $defaults )
);

if ( array_filter( $editor_resources ) ) {
	foreach ( $editor_resources as $id ) {
		$editors = get_post_meta( $id, 'lc_resource_editor', true );
		convert_repeater( $id, 'lc_resource_editor', $editors, 'editor', 'field_5e56ee4309400', 'field_5e56ee4309401' );
	}
} else {
	echo "No conversions needed for 'lc_resource_editor'.\n";
}

$translator_resources = get_posts(
	wp_parse_args( [ 'meta_key' => 'lc_resource_translator' ], $defaults )
);

if ( array_filter( $translator_resources ) ) {
	foreach ( $translator_resources as $id ) {
		$translators = get_post_meta( $id, 'lc_resource_translator', true );
		convert_repeater( $id, 'lc_resource_translator', $translators, 'translator', 'field_5e56ee6209402', 'field_5e56ee6209403' );
	}
} else {
	echo "No conversions needed for 'lc_resource_translator'.\n";
}

$perma_cc_resources = get_posts(
	wp_parse_args( [ 'meta_key' => 'lc_resource_perma_cc_links' ], $defaults )
);

if ( array_filter( $perma_cc_resources ) ) {
	foreach ( $perma_cc_resources as $id ) {
		$perma_cc_links = get_post_meta( $id, 'lc_resource_perma_cc_links', true );
		convert_repeater( $id, 'lc_resource_perma_cc_links', $perma_cc_links, 'perma_cc_link', 'field_5e5705faee329', 'field_5e57062fee32a' );
	}
} else {
	echo "No conversions needed for 'lc_resource_perma_cc_links'.\n";
}

$wayback_machine_resources = get_posts(
	wp_parse_args( [ 'meta_key' => 'lc_resource_wayback_machine_links' ], $defaults )
);

if ( array_filter( $wayback_machine_resources ) ) {
	foreach ( $wayback_machine_resources as $id ) {
		$wayback_machine_links = get_post_meta( $id, 'lc_resource_wayback_machine_links', true );
		convert_repeater( $id, 'lc_resource_wayback_machine_links', $wayback_machine_links, 'wayback_machine_link', 'field_5e57065fee32b', 'field_5e57065fee32c' );
	}
} else {
	echo "No conversions needed for 'lc_resource_wayback_machine_links'.\n";
}
