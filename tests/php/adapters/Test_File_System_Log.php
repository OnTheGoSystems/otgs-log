<?php
/**
 * @author OnTheGo Systems
 */

namespace OTGS\Tests;

class Test_File_System_Log extends TestCase {

	/**
	 * @test
	 * @throws \OTGS_ExpectedFormattedEntryException
	 */
	public function it_throws_an_exception_when_trying_to_add_a_formatted_entry() {
		$filename = 'tests.log';

		$subject = new \OTGS_File_System_Log( $filename );

		$this->expectException( '\OTGS_ExpectedFormattedEntryException' );

		$subject->add( array( 'Some string' ) );
	}

	/**
	 * @test
	 */
	public function it_stores_the_entry_in_a_file() {
		$filename        = 'tests.log';
		$entries         = array(
			'First-entry',
			'Second-entry',
		);
		$new_entry       = 'New-entry';
		$updated_entries = $entries;
		array_push( $updated_entries, $new_entry );

		$subject = new \OTGS_File_System_Log( $filename );

		$subject->addFormatted( $new_entry );

		$contents = file_get_contents( $filename );
		$contents = preg_replace( '/^[\r\n]+/', '', $contents );
		$contents = preg_replace( '/[\r\n]+$/', '', $contents );

		$file_entries = explode( PHP_EOL, $contents );

		$this->assertSame( $file_entries, $subject->getEntries() );

		unlink( $filename );
	}

	/**
	 * @test
	 */
	public function it_gets_the_entries_from_a_file() {
		$filename = 'tests.log';
		$entries  = array(
			'First-entry',
			'Second-entry',
		);

		$file_content = implode( PHP_EOL, $entries );

		file_put_contents( $filename, $file_content );

		$subject = new \OTGS_File_System_Log( $filename );

		$this->assertSame( $entries, $subject->getEntries() );

		unlink( $filename );
	}

	/**
	 * @test
	 */
	public function it_limits_the_entries() {
		$limit    = 100;
		$filename = 'tests.log';

		$subject = new \OTGS_File_System_Log( $filename, $limit );

		$entry = null;
		for ( $i = 0; $i < $limit*2; $i++ ) {
			$entry     = 'Entry ' . $i;
			$subject->addFormatted( $entry );
		}
		$last_entry = $entry;

		$file_entries = $subject->getEntries();

		$this->assertCount( $limit, $file_entries );
		$this->assertSame( $last_entry, end( $file_entries ) );

		unlink( $filename );
	}
}
