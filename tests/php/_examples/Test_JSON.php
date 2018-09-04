<?php
/**
 * @author OnTheGo Systems
 */

namespace OTGS\Tests;

class Test_Add_Log_Entries_In_JSON_File extends TestCase {
	const LOG_FILE = 'otgs-log.json';

	private $options = array();

	/**
	 * @test
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function it_adds_log_entries_SIMPLE() {
		$max_entries = 8;
		$adapter     = new \OTGS_JSON_File_Log( self::LOG_FILE );

		$log = new \OTGS_Multi_Log( array( $adapter ) );

		$log->add( 'First message' );
		$log->add( 'Second message' );
		$log->addError( 'First error', array( 'Extra data' => array( 'A' => 1, 'B' => 2, 'C' => 3 ) ) );
		$log->addError( 'Second error', array( 'Extra data' => array( 'A' => 1, 'B' => 2, 'C' => 3 ) ) );
		$log->addWarning( 'First warning' );
		$log->addWarning( 'Second warning' );
		$log->add( 'generic', 'Test' );
		$log->add( 'generic', 'Test' );

		$entries = $log->get();

		$this->assertCount( $max_entries, $entries );
	}

	/**
	 * @test
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function it_adds_log_entries_ADVANCED() {
		$max_entries      = 8;
		$timestamp_format = 'Y-m-d H:i:s.u';

		$timestamp = new \OTGS_Log_Timestamp_Date( $timestamp_format );
		$adapter   = new \OTGS_JSON_File_Log( self::LOG_FILE );

		$log = new \OTGS_Multi_Log();

		$log->addAdapter( $adapter );
		$log->setTimestamp( $timestamp );
		$log->setEntryTemplate( '%timestamp% %entry%' );

		$log->add( 'First message' );
		$log->add( 'Second message' );
		$log->addError( 'First error' );
		$log->addError( 'Second error' );
		$log->addWarning( 'First warning' );
		$log->addWarning( 'Second warning' );
		$log->add( 'generic', 'Test' );
		$log->add( 'generic', 'Test' );

		$entries = $log->get();

		$this->assertCount( $max_entries, $entries );
	}

	/**
	 * @test
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function it_keeps_the_log_in_the_max_entries_limit() {
		$max_entries = 100;

		$timestamp_format = 'Y-m-d H:i:s.u';

		$timestamp = new \OTGS_Log_Timestamp_Date( $timestamp_format );
		$adapter   = new \OTGS_JSON_File_Log( self::LOG_FILE, $max_entries );

		$log = new \OTGS_Multi_Log( array( $adapter ), $timestamp, '%timestamp% %entry%' );

		for ( $i = 0; $i < $max_entries*2; $i++ ) {
			$log->add( 'Message ' . $i );
		}

		$entries = $log->get();

		$this->assertCount( $max_entries, $entries );

		$log->add( 'Another message' );
		$this->assertCount( $max_entries, $entries );
	}

	public function tearDown() {
		unlink( self::LOG_FILE );
		parent::tearDown();
	}
}