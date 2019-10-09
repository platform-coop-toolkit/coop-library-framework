<?php
/**
 * Test adding resources.
 */

class ResourceTest extends \WPAcceptance\PHPUnit\TestCase {

	/**
	 * @testdox I am able to publish a resource.
	 */
	function testPostPublish() {
		$actor = $this->openBrowserPage();

		$actor->login();

		$actor->moveTo( 'wp-admin/post-new.php?post_type=lc_resource' );

		$actor->typeInField( '#title', 'Test Resource' );

		$actor->typeInField( '#lc_resource_permanent_link', 'https://resource.link' );

		$actor->typeInField( '#lc_resource_publication_year', '2019' );

		$actor->click( '#publish' );

		$actor->waitUntilElementVisible( '.notice-success' );

		$actor->seeText( 'Resource published', '.notice-success' );
	}
}
