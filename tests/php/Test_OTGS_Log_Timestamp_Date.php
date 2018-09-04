<?php
/**
 * @author OnTheGo Systems
 */

namespace OTGS\Tests;

class Test_OTGS_Log_Timestamp_Date extends TestCase {
	/**
	 * @test
	 */
	public function it_gets_a_formatted_timestamp() {
		$format  = 'Y-m-d';

		$subject = new \OTGS_Log_Timestamp_Date();
		$subject->setFormat( $format );

		$this->assertSame( date( $format ), $subject->get() );
	}

	protected function getLogFileName() {
		// TODO: Implement getLogFileName() method.
	}
}