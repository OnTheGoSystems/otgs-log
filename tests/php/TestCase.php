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
		if ( file_exists( $this->getTestFile() ) ) {
			unlink( self::getTestFile() );
		}
		\WP_Mock::tearDown();
		parent::tearDown();
	}

	protected function getTestFile() {
		return __DIR__ . '/' . __CLASS__ . '.' . $this->getLogFileName();
	}

	abstract protected function getLogFileName();
}