<?php
/**
 * @author OnTheGo Systems
 */

namespace OTGS\Tests;

	class Test_Add_Log_Entries_In_File_System extends TestCase {
	const LOG_FILE = 'examples-otgs-log.txt';

	protected function getLogFileName() {
		return self::LOG_FILE;
	}

	/**
	 * @test
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function it_adds_log_entries_SIMPLE() {
		$max_entries = 8;
		$adapter     = new \OTGS_File_System_Log( self::getTestFile() );

		$log = new \OTGS_Multi_Log( array( $adapter ) );

		$log->add( 'First message', \OTGS_Log_Entry_Levels::LEVEL_INFORMATIONAL );
		$log->add( 'Second message', \OTGS_Log_Entry_Levels::LEVEL_INFORMATIONAL );
		$log->addError( 'First error', array( 'Extra data' => array( 'A' => 1, 'B' => 2, 'C' => 3 ) ) );
		$log->addError( 'Second error', array( 'Extra data' => array( 'A' => 1, 'B' => 2, 'C' => 3 ) ) );
		$log->addWarning( 'First warning' );
		$log->addWarning( 'Second warning' );
		$log->addInfo( 'First info' );
		$log->addInfo( 'Second info' );

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
		$adapter   = new \OTGS_File_System_Log( self::getTestFile() );

		$log = new \OTGS_Multi_Log();

		$log->addAdapter( $adapter );
		$log->setTimestamp( $timestamp );
		$log->setEntryTemplate( '%timestamp% %entry% %extra%' );

		$log->add( 'First message', \OTGS_Log_Entry_Levels::LEVEL_INFORMATIONAL );
		$log->add( 'Second message', \OTGS_Log_Entry_Levels::LEVEL_INFORMATIONAL );
		$log->addError( 'First error' );
		$log->addError( 'Second error' );
		$log->addWarning( 'First warning' );
		$log->addWarning( 'Second warning' );
		$log->addInfo( 'First info' );
		$log->addInfo( 'Second info' );

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
		$adapter   = new \OTGS_File_System_Log( self::getTestFile(), $max_entries );

		$log = new \OTGS_Multi_Log( array( $adapter ), $timestamp, '%timestamp% %entry%' );

		for ( $i = 0; $i < $max_entries*2; $i++ ) {
			$log->addError( 'Message ' . $i );
		}

		$entries = $log->get();

		$this->assertCount( $max_entries, $entries );

		$log->addWarning( 'Another message' );
		$this->assertCount( $max_entries, $entries );
	}
}