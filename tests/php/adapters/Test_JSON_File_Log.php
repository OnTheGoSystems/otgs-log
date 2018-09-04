<?php
/**
 * @author OnTheGo Systems
 */

namespace OTGS\Tests;

class Test_JSON_File_Log extends TestCase {
	const LOG_FILE = 'test.otgs.log.json';

	protected function getLogFileName() {
		return self::LOG_FILE;
	}

	/**
	 * @test
	 * @throws \OTGS_ExpectedArrayEntryException
	 */
	public function it_throws_an_exception_when_trying_to_add_a_formatted_entry() {
		$subject = new \OTGS_JSON_File_Log( self::getTestFile() );

		$this->expectException( '\OTGS_ExpectedArrayEntryException' );

		$subject->addFormatted( 'Some string' );
	}

	/**
	 * @test
	 */
	public function it_stores_the_entry_in_a_file() {
		$entries         = array(
			array( 'First-entry' ),
			array( 'Second-entry' ),
		);
		$new_entry       = array( 'New-entry' );
		$updated_entries = $entries;
		array_push( $updated_entries, $new_entry );

		$subject = new \OTGS_JSON_File_Log( self::getTestFile() );

		$subject->add( $new_entry );

		$contents = file_get_contents( self::getTestFile() );

		$file_entries = json_decode( $contents, true );

		$this->assertSame( $file_entries, $subject->getEntries() );
	}

	/**
	 * @test
	 */
	public function it_gets_the_entries_from_a_file() {
		$entries  = array(
			array( 'First-entry' ),
			array( 'Second-entry' ),
		);

		$file_content = json_encode( $entries );

		file_put_contents( self::getTestFile(), $file_content );

		$subject = new \OTGS_JSON_File_Log( self::getTestFile() );

		$file_entries = $subject->getEntries();

		$this->assertSame( $entries, $file_entries );
	}

	/**
	 * @test
	 */
	public function it_limits_the_entries() {
		$limit    = 100;

		$subject = new \OTGS_JSON_File_Log( self::getTestFile(), $limit );

		$entry = null;
		for ( $i = 0; $i < $limit*2; $i++ ) {
			$entry = array(
				'timestamp' => $this->get_microtime(),
				'message'   => 'Entry ' . $i,
			);
			$subject->add( $entry );
		}
		$last_entry = $entry;

		$file_entries = $subject->getEntries();

		$this->assertCount( $limit, $file_entries );
		$this->assertEquals( $last_entry, end( $file_entries ), '', .1 );
	}

	/**
	 * @test
	 */
	public function it_stores_the_entry_in_a_file_using_wp_json_encode() {
		$entries         = array(
			array( 'First-entry' ),
			array( 'Second-entry' ),
		);
		$new_entry       = array( 'New-entry' );
		$updated_entries = $entries;
		array_push( $updated_entries, $new_entry );

		\WP_Mock::userFunction( 'wp_json_encode', array(
			'times'  => 1,
			'return' => function ( $data, $options ) {
				return json_encode( $data, $options );
			}
		) );

		$subject = new \OTGS_JSON_File_Log( self::getTestFile() );

		$subject->add( $new_entry );

		$contents = file_get_contents( self::getTestFile() );

		$file_entries = json_decode( $contents, true );

		$this->assertSame( $file_entries, $subject->getEntries() );
	}

	private function get_microtime() {
		list( $usec, $sec ) = explode( ' ', microtime() );

		return ( (float) $usec + (float) $sec );
	}
}
