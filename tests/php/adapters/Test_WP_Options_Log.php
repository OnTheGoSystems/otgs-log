<?php
/**
 * @author OnTheGo Systems
 */

namespace OTGS\Tests;

class Test_WP_Options_Log extends TestCase {
	private $options = array();

	/**
	 * @test
	 * @throws \OTGS_ExpectedFormattedEntryException
	 */
	public function it_throws_an_exception_when_trying_to_add_a_formatted_entry() {
		$filename = 'tests.log';

		$subject = new \OTGS_WP_Option_Log( $filename );

		$this->expectException( '\OTGS_ExpectedFormattedEntryException' );

		$subject->add( array( 'Some string' ) );
	}

	/**
	 * @test
	 */
	public function it_stores_the_entry() {
		$option_key      = 'option_key';
		$entries         = array(
			'First-entry',
			'Second-entry',
		);
		$new_entry       = 'New-entry';
		$updated_entries = $entries;
		array_push( $updated_entries, $new_entry );

		$subject = new \OTGS_WP_Option_Log( $option_key );
		\WP_Mock::userFunction( 'get_option', array(
			'times'  => 1,
			'args'   => array( $option_key, array() ),
			'return' => $entries,
		) );
		\WP_Mock::userFunction( 'update_option', array(
			'times' => 1,
			'args'  => array( $option_key, $updated_entries, false ),
		) );

		$subject->addFormatted( $new_entry );
	}

	/**
	 * @test
	 */
	public function it_gets_the_entries() {
		$option_key = 'option_key';
		$entries    = array(
			'First-entry',
			'Second-entry',
		);

		$subject = new \OTGS_WP_Option_Log( $option_key );
		\WP_Mock::userFunction( 'get_option', array(
			'times'  => 1,
			'args'   => array( $option_key, array() ),
			'return' => $entries,
		) );

		$subject->getEntries();
	}

	/**
	 * @test
	 */
	public function it_limits_the_entries() {
		\WP_Mock::userFunction( 'get_option', array(
			'return' => function ( $option_name, $default ) {
				if ( array_key_exists( $option_name, $this->options ) ) {
					return $this->options[ $option_name ];
				}

				return $default;
			},
		) );
		\WP_Mock::userFunction( 'update_option', array(
			'return' => function ( $option_name, $value ) {
				$this->options[ $option_name ] = $value;
			},

		) );

		$limit      = 100;
		$option_key = 'option_key';

		$subject = new \OTGS_WP_Option_Log( $option_key, $limit );

		$entry = null;
		for ( $i = 0; $i < $limit*2; $i++ ) {
			$entry = 'Entry ' . $i;
			$subject->addFormatted( $entry );
		}
		$last_entry = $entry;

		$file_entries = $subject->getEntries();

		$this->assertCount( $limit, $file_entries );
		$this->assertSame( $last_entry, end( $file_entries ) );
	}
}
