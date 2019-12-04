<?php
namespace CoopLibraryFramework\Internationalization;

use CoopLibraryFramework as Base;

class Internationalization_Tests extends Base\TestCase {

	protected $testFiles = [
		'functions/internationalization.php'
	];

	public function test_copy_post_metas() {
		$meta_keys_to_copy = copy_post_metas( [
			0 => 'lc_resource_permanent_link',
			1 => 'lc_resource_publisher_name',
		] );

		$this->assertNotContains( 'lc_resource_permanent_link', $meta_keys_to_copy );
	}

	public function test_get_country_list() {
		$countries_de = get_country_list( 'de_DE' );
		$this->assertEquals( $countries_de['CA'], 'Kanada' );
	}
}
