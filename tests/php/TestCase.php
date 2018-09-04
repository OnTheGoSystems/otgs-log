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
		$test_file = $this->getTestFile();
		if ( file_exists( $test_file ) ) {
			unlink( $test_file );
		}
		\WP_Mock::tearDown();
		parent::tearDown();
	}

	protected function getTestFile() {
		return __DIR__ . '/' . str_replace( '\\', '_', get_called_class() ) . '.' . $this->getLogFileName();
	}

	abstract protected function getLogFileName();
}