<?php
/**
 * @author OnTheGo Systems
 */

namespace OTGS\Tests;

class Test_File_System_Log extends TestCase {

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

		$subject->add( $new_entry );

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

		$subject->getEntries();

		$this->assertSame( $entries, $subject->getEntries() );

		unlink( $filename );
	}

}