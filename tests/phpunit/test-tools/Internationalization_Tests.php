<?php
namespace CoopLibraryFramework\Internationalization;

use CoopLibraryFramework as Base;

class Internationalization_Tests extends Base\TestCase {

	protected $testFiles = [
		'functions/internationalization.php'
	];

	public function test_get_country_list() {
		$countries_de = get_country_list( 'de_DE' );
		$this->assertEquals( $countries_de['CA'], 'Kanada' );
	}
}
