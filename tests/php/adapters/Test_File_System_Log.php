<?php
/**
 * @author OnTheGo Systems
 */

namespace OTGS\Tests;

class Test_File_System_Log extends TestCase {
	const LOG_FILE = 'test.otgs.log.txt';

	protected function getLogFileName() {
		return self::LOG_FILE;
	}

	/**
	 * @test
	 * @throws \OTGS_ExpectedFormattedEntryException
	 */
	public function it_throws_an_exception_when_trying_to_add_an_array_entry() {
		$subject = new \OTGS_File_System_Log( self::getTestFile() );

		$this->expectException( '\OTGS_ExpectedFormattedEntryException' );

		$subject->add( array( 'Some string' ) );
	}

	/**
	 * @test
	 */
	public function it_stores_the_entry_in_a_file() {
		$entries         = array(
			'First-entry',
			'Second-entry',
		);
		$new_entry       = 'New-entry';
		$updated_entries = $entries;
		array_push( $updated_entries, $new_entry );

		$subject = new \OTGS_File_System_Log( self::getTestFile() );

		$subject->addFormatted( $new_entry );

		$contents = file_get_contents( self::getTestFile() );
		$contents = preg_replace( '/^[\r\n]+/', '', $contents );
		$contents = preg_replace( '/[\r\n]+$/', '', $contents );

		$file_entries = explode( PHP_EOL, $contents );

		$this->assertSame( $file_entries, $subject->getEntries() );

		unlink( self::getTestFile() );
	}

	/**
	 * @test
	 */
	public function it_gets_the_entries_from_a_file() {
		$entries  = array(
			'First-entry',
			'Second-entry',
		);

		$file_content = implode( PHP_EOL, $entries );

		file_put_contents( self::getTestFile(), $file_content );

		$subject = new \OTGS_File_System_Log( self::getTestFile() );

		$this->assertSame( $entries, $subject->getEntries() );

		unlink( self::getTestFile() );
	}

	/**
	 * @test
	 */
	public function it_limits_the_entries() {
		$limit    = 100;

		$subject = new \OTGS_File_System_Log( self::getTestFile(), $limit );

		$entry = null;
		for ( $i = 0; $i < $limit*2; $i++ ) {
			$entry     = 'Entry ' . $i;
			$subject->addFormatted( $entry );
		}
		$last_entry = $entry;

		$file_entries = $subject->getEntries();

		$this->assertCount( $limit, $file_entries );
		$this->assertSame( $last_entry, end( $file_entries ) );

		unlink( self::getTestFile() );
	}
}
