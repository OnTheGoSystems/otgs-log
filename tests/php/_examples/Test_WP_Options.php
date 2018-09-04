<?php
/**
 * @author OnTheGo Systems
 */

namespace OTGS\Tests;

class Test_Add_Log_Entries_In_WP_Options extends TestCase {
	private $options = array();

	/**
	 * @test
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function it_adds_log_entries_in_wp_options_SIMPLE() {
		$adapter = new \OTGS_WP_Option_Log( 'otgs-log' );

		$log = new \OTGS_Multi_Log( array( $adapter ) );

		$log->add( 'First message', \OTGS_Log_Entry_Levels::LEVEL_INFORMATIONAL );
		$log->add( 'Second message', \OTGS_Log_Entry_Levels::LEVEL_INFORMATIONAL );
		$log->addError( 'First error' );
		$log->addError( 'Second error' );
		$log->addWarning( 'First warning' );
		$log->addWarning( 'Second warning' );
		$log->addInfo( 'First info' );
		$log->addInfo( 'Second info' );

		$entries = $log->get();

		$this->assertCount( 8, $entries );
	}

	/**
	 * @test
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function it_adds_log_entries_in_wp_options_ADVANCED() {
		$timestamp_format = 'Y-m-d H:i:s.u';

		$timestamp = new \OTGS_Log_Timestamp_Date( $timestamp_format );
		$adapter   = new \OTGS_WP_Option_Log( 'otgs-log' );

		$log = new \OTGS_Multi_Log();

		$log->addAdapter( $adapter );
		$log->setTimestamp( $timestamp );
		$log->setEntryTemplate( '%timestamp% %entry% %extra_data%' );

		$log->add( 'First message', \OTGS_Log_Entry_Levels::LEVEL_INFORMATIONAL );
		$log->add( 'Second message', \OTGS_Log_Entry_Levels::LEVEL_INFORMATIONAL );
		$log->addError( 'First error' );
		$log->addError( 'Second error' );
		$log->addWarning( 'First warning' );
		$log->addWarning( 'Second warning' );
		$log->addInfo( 'First info' );
		$log->addInfo( 'Second info' );

		$entries = $log->get();

		$this->assertCount( 8, $entries );
	}

	/**
	 * @test
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function it_keeps_the_log_in_the_max_entries_limit() {
		$max_entries = 100;

		$timestamp_format = 'Y-m-d H:i:s.u';

		$timestamp = new \OTGS_Log_Timestamp_Date( $timestamp_format );
		$adapter   = new \OTGS_WP_Option_Log( 'otgs-log', $max_entries );

		$log = new \OTGS_Multi_Log( array( $adapter ), $timestamp, '%timestamp% %entry%' );

		for ( $i = 0; $i < $max_entries*2; $i++ ) {
			$log->add( 'Message ' . $i, \OTGS_Log_Entry_Levels::LEVEL_INFORMATIONAL );
		}

		$entries = $log->get();

		$this->assertCount( $max_entries, $entries );

		$log->addWarning( 'Another message' );
		$this->assertCount( $max_entries, $entries );
	}


	public function setUp() {
		parent::setUp();

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

	}

	protected function getLogFileName() {
		// TODO: Implement getLogFileName() method.
	}
}