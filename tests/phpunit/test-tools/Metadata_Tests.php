<?php
namespace LearningCommonsFramework\Metadata;

use LearningCommonsFramework as Base;

class Metadata_Tests extends Base\TestCase {

	protected $testFiles = [
		'functions/metadata.php'
	];

	public function test_preload_day_options() {
		$year      = preload_day_options( 2015, 02 );
		$leap_year = preload_day_options( 2016, 02 );

		$this->assertNotContains( '29', $year );
		$this->assertContains( '29', $leap_year );
	}
}
