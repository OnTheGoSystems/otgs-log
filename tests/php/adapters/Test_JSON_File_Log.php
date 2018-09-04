<?php
/**
 * @author OnTheGo Systems
 */

namespace OTGS\Tests;

class Test_JSON_File_Log extends TestCase {

	/**
	 * @test
	 * @throws \OTGS_ExpectedArrayEntryException
	 */
	public function it_throws_an_exception_when_trying_to_add_a_formatted_entry() {
		$filename        = 'tests.log';

		$subject = new \OTGS_JSON_File_Log( $filename );

		$this->expectException( '\OTGS_ExpectedArrayEntryException' );

		$subject->addFormatted( 'Some string' );
	}

	/**
	 * @test
	 */
	public function it_stores_the_entry_in_a_file() {
		$filename        = 'tests.log';
		$entries         = array(
			array( 'First-entry' ),
			array( 'Second-entry' ),
		);
		$new_entry       = array( 'New-entry' );
		$updated_entries = $entries;
		array_push( $updated_entries, $new_entry );

		$subject = new \OTGS_JSON_File_Log( $filename );

		$subject->add( $new_entry );

		$contents = file_get_contents( $filename );

		$file_entries = json_decode( $contents, true );

		$this->assertSame( $file_entries, $subject->getEntries() );

		unlink( $filename );
	}

	/**
	 * @test
	 */
	public function it_gets_the_entries_from_a_file() {
		$filename = 'tests.log';
		$entries  = array(
			array( 'First-entry' ),
			array( 'Second-entry' ),
		);

		$file_content = json_encode( $entries );

		file_put_contents( $filename, $file_content );

		$subject = new \OTGS_JSON_File_Log( $filename );

		$file_entries = $subject->getEntries();

		$this->assertSame( $entries, $file_entries );

		unlink( $filename );
	}

	/**
	 * @test
	 */
	public function it_limits_the_entries() {
		$limit    = 100;
		$filename = 'tests.log';

		$subject = new \OTGS_JSON_File_Log( $filename, $limit );

		$entry = null;
		for ( $i = 0; $i < $limit*2; $i++ ) {
			$entry = array(
				'timestamp' => date( 'Y-m-d H:i:s.u' ),
				'message'   => 'Entry ' . $i,
			);
			$subject->add( $entry );
		}
		$last_entry = $entry;

		$file_entries = $subject->getEntries();

		$this->assertCount( $limit, $file_entries );
		$this->assertSame( $last_entry, end( $file_entries ) );

		unlink( $filename );
	}
}
