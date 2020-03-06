<?php
/**
 * Convert languages from Polylang taxonomy to new (1.0.0-alpha.3) format.
 *
 * @package CoopLibraryFramework
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$resources = get_posts(
	[
		'fields'         => 'ids',
		'posts_per_page' => -1,
		'post_type'      => 'lc_resource',
		'post_status'    => 'all',
		'lang'           => '',
	]
);

foreach ( $resources as $post_id ) {
	update_post_meta( $post_id, 'language', pll_get_post_language( $post_id ) );
	update_post_meta( $post_id, '_language', 'field_5e62c939ddcb6' );
}
