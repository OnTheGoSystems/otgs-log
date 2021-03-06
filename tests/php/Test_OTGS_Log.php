<?php
/**
 * @author OnTheGo Systems
 */

namespace OTGS\Tests;

class Test_OTGS_Log extends TestCase {
	/**
	 * @test
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function it_throws_an_exception_when_there_are_no_registered_adapters() {
		$adapter_name = 'some-adapter';
		$entry        = 'An entry';

		$subject = new \OTGS_Multi_Log();

		$this->expectException( \OTGS_MissingAdaptersException::class );

		$subject->withAdapter( $adapter_name )->add( $entry, \OTGS_Log_Entry_Levels::LEVEL_INFORMATIONAL );
	}

	/**
	 * @test
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function it_throws_an_exception_when_the_requested_adapter_is_not_registered() {
		$adapter_name         = 'SomeLogAdapter';
		$another_adapter_name = 'AnotherLogAdapter';
		$entry                = 'An entry';

		$some_log_adapter = $this->get_adapter_stub( $adapter_name );

		$this->expectException( \OTGS_MissingAdaptersException::class );

		$subject = new \OTGS_Multi_Log();
		$subject->addAdapter( $some_log_adapter );
		$subject->withAdapter( $another_adapter_name )->add( $entry, \OTGS_Log_Entry_Levels::LEVEL_INFORMATIONAL );
	}

	/**
	 * @test
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function it_stores_the_formatted_entry_in_the_log_adapter() {
		$timestamp    = 'yyy-mm-dd hh:mm:ss.mmmmm';
		$adapter_name = 'SomeLogAdapter';

		$level               = 0;
		$new_entry           = 'Last entry';
		$extra_data          = array(
			'A' => 1,
			'B' => 2,
			'C' => 3,
		);
		$expected_extra_data = array(
			'data'        => array(
				'A' => 1,
				'B' => 2,
				'C' => 3,
			),
			'description' => \OTGS_Log_Entry_Levels::DESCRIPTION_EMERGENCY,
		);
		$encoded_extra_data  = json_encode( $expected_extra_data );

		$some_log_adapter = $this->get_adapter_stub( $adapter_name, true );
		$some_log_adapter->expects( $this->once() )
						 ->method( 'addFormatted' )
						 ->with( $timestamp . ' ' . $level . ' ' . $new_entry . ' ' . $encoded_extra_data );
		$some_log_adapter->expects( $this->never() )
						 ->method( 'add' );

		$timestamp_helper = $this->get_timestamp_helper( 'SomeTimeStampHelper' );
		$timestamp_helper->expects( $this->once() )
						 ->method( 'getFormatted' )
						 ->willReturn( $timestamp );
		$timestamp_helper->expects( $this->never() )
						 ->method( 'get' );

		$subject = new \OTGS_Multi_Log();
		$subject->addAdapter( $some_log_adapter );
		$subject->setTimestamp( $timestamp_helper );

		$subject->withAdapter( $adapter_name )->add( $new_entry, $level, $extra_data );
	}

	/**
	 * @test
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function it_stores_the_formatted_entry_in_the_log_adapter_with_extra_data_in_a_separate_entry() {
		$timestamp    = 'yyy-mm-dd hh:mm:ss.mmmmm';
		$adapter_name = 'SomeLogAdapter';

		$level      = 0;
		$new_entry  = 'Last entry';
		$extra_data = array(
			'A' => 1,
			'B' => 2,
			'C' => 3,
		);

		$some_log_adapter = $this->get_adapter_stub( $adapter_name, true );
		$some_log_adapter->expects( $this->exactly( 2 ) )
						 ->method( 'addFormatted' );
		$some_log_adapter->expects( $this->never() )
						 ->method( 'add' );

		$timestamp_helper = $this->get_timestamp_helper( 'SomeTimeStampHelper' );
		$timestamp_helper->expects( $this->once() )
						 ->method( 'getFormatted' )
						 ->willReturn( $timestamp );
		$timestamp_helper->expects( $this->never() )
						 ->method( 'get' );

		$subject = new \OTGS_Multi_Log();
		$subject->addAdapter( $some_log_adapter );
		$subject->setTimestamp( $timestamp_helper );
		$subject->setEntryTemplate( '%timestamp% %type% %entry%' );

		$subject->withAdapter( $adapter_name )->add( $new_entry, $level, $extra_data );
	}

	/**
	 * @test
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function it_stores_the_formatted_entry_in_the_log_adapter_with_a_differently_encoded_extra_data() {
		$timestamp    = 'yyy-mm-dd hh:mm:ss.mmmmm';
		$adapter_name = 'SomeLogAdapter';

		$level               = 0;
		$new_entry           = 'Last entry';
		$extra_data          = array(
			'A' => 1,
			'B' => 2,
			'C' => 3,
		);
		$expected_extra_data = array(
			'data'        => array(
				'A' => 1,
				'B' => 2,
				'C' => 3,
			),
			'description' => \OTGS_Log_Entry_Levels::DESCRIPTION_EMERGENCY,
		);
		$encoded_extra_data  = serialize( $expected_extra_data );

		$some_log_adapter = $this->get_adapter_stub( $adapter_name, true );
		$some_log_adapter->expects( $this->once() )
						 ->method( 'addFormatted' )
						 ->with( $timestamp . ' ' . $level . ' ' . $new_entry . ' ' . $encoded_extra_data );
		$some_log_adapter->expects( $this->never() )
						 ->method( 'add' );

		$timestamp_helper = $this->get_timestamp_helper( 'SomeTimeStampHelper' );
		$timestamp_helper->expects( $this->once() )
						 ->method( 'getFormatted' )
						 ->willReturn( $timestamp );
		$timestamp_helper->expects( $this->never() )
						 ->method( 'get' );

		$subject = new \OTGS_Multi_Log();
		$subject->addAdapter( $some_log_adapter );
		$subject->setTimestamp( $timestamp_helper );
		$subject->setDataEncoding( 'serialize' );

		$subject->withAdapter( $adapter_name )->add( $new_entry, $level, $extra_data );
	}

	/**
	 * @test
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function it_stores_the_array_entry_in_the_log_adapter() {
		$timestamp    = 1234.5678;
		$adapter_name = 'SomeLogAdapter';

		$level               = 0;
		$new_entry           = 'Last entry';
		$extra_data          = array(
			'A' => 1,
			'B' => 2,
			'C' => 3,
		);
		$expected_extra_data = array(
			'data'        => array(
				'A' => 1,
				'B' => 2,
				'C' => 3,
			),
			'description' => \OTGS_Log_Entry_Levels::DESCRIPTION_EMERGENCY,
		);

		$some_log_adapter = $this->get_adapter_stub( $adapter_name, false );
		$some_log_adapter->expects( $this->once() )
						 ->method( 'add' )
						 ->with( array(
							 'timestamp'  => $timestamp,
							 'level'      => 0,
							 'level_name' => \OTGS_Log_Entry_Levels::TYPE_EMERGENCY,
							 'message'    => $new_entry,
							 'extra'      => $expected_extra_data,
							 'datetime'   => array(
								 'date'     => $timestamp,
								 'timezone' => 'Time/Zone',
							 )
						 ) );
		$some_log_adapter->expects( $this->never() )
						 ->method( 'addFormatted' );

		$timestamp_helper = $this->get_timestamp_helper( 'SomeTimeStampHelper' );
		$timestamp_helper->expects( $this->never() )
						 ->method( 'getFormatted' );
		$timestamp_helper->expects( $this->once() )
						 ->method( 'get' )
						 ->willReturn( $timestamp );
		$timestamp_helper->expects( $this->once() )
						 ->method( 'getTimeZoneValue' )
						 ->willReturn( 'Time/Zone' );

		$subject = new \OTGS_Multi_Log();
		$subject->addAdapter( $some_log_adapter );
		$subject->setTimestamp( $timestamp_helper );

		$subject->withAdapter( $adapter_name )->add( $new_entry, $level, $extra_data );
	}

	/**
	 * @test
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function it_sets_the_format_of_the_entry_log() {
		$entry_format = '%timestamp%: %level% %entry% %extra%';
		$timestamp    = 'yyy-mm-dd hh:mm:ss.mmmmm';
		$adapter_name = 'SomeLogAdapter';

		$level      = 0;
		$new_entry  = 'Last entry';
		$extra_data = array( 'Extra data' );

		$expected_extra_data         = array(
			'data'        => $extra_data,
			'description' => \OTGS_Log_Entry_Levels::DESCRIPTION_EMERGENCY,
		);
		$expected_extra_data_encoded = json_encode( $expected_extra_data );

		$expected_entry = str_replace(
			array( '%timestamp%', '%level%', '%entry%', '%extra%' ),
			array( $timestamp, $level, $new_entry, $expected_extra_data_encoded ),
			$entry_format
		);

		$some_log_adapter = $this->get_adapter_stub( $adapter_name, true );
		$some_log_adapter->expects( $this->once() )
						 ->method( 'addFormatted' )
						 ->with( $expected_entry );

		$timestamp_helper = $this->get_timestamp_helper( 'SomeTimeStampHelper' );
		$timestamp_helper->expects( $this->once() )
						 ->method( 'getFormatted' )
						 ->willReturn( $timestamp );
		$timestamp_helper->expects( $this->never() )
						 ->method( 'get' );

		$subject = new \OTGS_Multi_Log();
		$subject->setEntryTemplate( $entry_format );
		$subject->addAdapter( $some_log_adapter );
		$subject->setTimestamp( $timestamp_helper );

		$subject->withAdapter( $adapter_name )->add( $new_entry, $level, $extra_data );
	}

	/**
	 * @test
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function it_stores_the_entry_in_the_first_registered_adapter() {
		$adapter_name         = 'SomeLogAdapter';
		$another_adapter_name = 'AnotherLogAdapter';

		$new_entry = 'An entry';

		$some_log_adapter = $this->get_adapter_stub( $adapter_name, true );
		$some_log_adapter->expects( $this->once() )
						 ->method( 'addFormatted' );

		$another_log_adapter = $this->get_adapter_stub( $another_adapter_name, true );
		$another_log_adapter->expects( $this->never() )
							->method( 'addFormatted' );

		$subject = new \OTGS_Multi_Log();
		$subject->addAdapter( $some_log_adapter );
		$subject->addAdapter( $another_log_adapter );

		$subject->addInfo( $new_entry );
	}

	/**
	 * @param string    $class_name
	 * @param bool|null $hasTemplate
	 *
	 * @return \OTGS_Log_Adapter|\PHPUnit_Framework_MockObject_MockObject
	 */
	public function get_adapter_stub( $class_name, $hasTemplate = null ) {
		$adapter = $this->getMockBuilder( \OTGS_Log_Adapter::class )
						->setMockClassName( $class_name )
						->disableOriginalConstructor()
						->setMethods( array(
							'hasTemplate',
							'addFormatted',
							'add',
							'getEntries',
						) )
						->getMock();

		if ( null !== $hasTemplate ) {
			$adapter->method( 'hasTemplate' )->willReturn( $hasTemplate );
		}

		return $adapter;
	}

	/**
	 * @param string $class_name
	 *
	 * @return \OTGS_Log_TimeStamp|\PHPUnit_Framework_MockObject_MockObject
	 */
	private function get_timestamp_helper( $class_name ) {
		$timestamp = $this->getMockBuilder( \OTGS_Log_TimeStamp::class )
						  ->setMockClassName( $class_name )
						  ->disableOriginalConstructor()
						  ->setMethods( array(
							  'setFormat',
							  'getFormatted',
							  'get',
							  'getTimeZoneValue',
						  ) )
						  ->getMock();

		return $timestamp;
	}

	protected function getLogFileName() {
		// TODO: Implement getLogFileName() method.
	}
}