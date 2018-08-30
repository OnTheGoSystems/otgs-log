<?php
/**
 * @author OnTheGo Systems
 */

namespace OTGS\Tests;

class Test_WP_Options_Log extends TestCase {
	/**
	 * @test
	 */
	public function it_stores_the_entry_in_wp_options() {
		$option_key = 'option_key';
		$entries    = array(
			'First entry',
			'Second entry',
		);

		$subject = new \OTGS_WP_Option_Log( $option_key );
		\WP_Mock::userFunction( 'update_option', array(
			'times' => 1,
			'args'  => array( $option_key, $entries, false ),
		) );


		$subject->save( $entries );
	}

	/**
	 * @test
	 */
	public function it_get_the_entries_from_wp_options() {
		$option_key = 'option_key';
		$entries    = array(
			'First entry',
			'Second entry',
		);

		$subject = new \OTGS_WP_Option_Log( $option_key );
		\WP_Mock::userFunction( 'get_option', array(
			'times'  => 1,
			'args'   => array( $option_key, array() ),
			'return' => $entries,
		) );

		$subject->get_entries();
	}

	/**
	 * @test
	 */
	public function it_add_a_new_entry_in_wp_options() {
		$option_key      = 'option_key';
		$entries         = array(
			'First entry',
		);

		$updated_entries = $entries + array( 'Second entry' );

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


		$subject->add( 'Second entry' );
	}

}