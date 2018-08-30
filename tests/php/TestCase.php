<?php
/**
 * @author OnTheGo Systems
 */
namespace OTGS\Tests;

abstract class TestCase extends \PHPUnit\Framework\TestCase {
	public function setUp() {
		parent::setUp();
		\WP_Mock::setUp();
	}

	public function tearDown() {
		\WP_Mock::tearDown();
		parent::tearDown();
	}
}