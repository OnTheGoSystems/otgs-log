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

		$subject->withAdapter( $adapter_name )->add( $entry );
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
		$subject->withAdapter( $another_adapter_name )->add( $entry );
	}

	/**
	 * @test
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function it_stores_the_entry_in_the_log_adapter() {
		$timestamp    = 'yyy-mm-dd hh:mm:ss.mmmmm';
		$adapter_name = 'SomeLogAdapter';

		$entry_type = 'Info';
		$new_entry = 'Last entry';

		$some_log_adapter = $this->get_adapter_stub( $adapter_name );
		$some_log_adapter->expects( $this->once() )
						 ->method( 'add' )
						 ->with( $timestamp . ' ' . $entry_type . ' ' . $new_entry );

		$timestamp_helper = $this->get_timestamp_helper( 'SomeTimeStampHelper' );
		$timestamp_helper->expects( $this->once() )
						 ->method( 'get' )
						 ->willReturn( $timestamp );

		$subject = new \OTGS_Multi_Log();
		$subject->addAdapter( $some_log_adapter );
		$subject->setTimestamp( $timestamp_helper );

		$subject->withAdapter( $adapter_name )->add( $new_entry, $entry_type );
	}

	/**
	 * @test
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function it_sets_the_format_of_the_entry_log() {
		$entry_format = '%timestamp%: %entry%';
		$timestamp    = 'yyy-mm-dd hh:mm:ss.mmmmm';
		$adapter_name = 'SomeLogAdapter';

		$new_entry = 'Last entry';

		$expected_entry = str_replace( array( '%timestamp%', '%entry%' ), array( $timestamp, $new_entry ), $entry_format );

		$some_log_adapter = $this->get_adapter_stub( $adapter_name );
		$some_log_adapter->expects( $this->once() )
						 ->method( 'add' )
						 ->with( $expected_entry );

		$timestamp_helper = $this->get_timestamp_helper( 'SomeTimeStampHelper' );
		$timestamp_helper->expects( $this->once() )
						 ->method( 'get' )
						 ->willReturn( $timestamp );

		$subject = new \OTGS_Multi_Log();
		$subject->setEntryFormat( $entry_format );
		$subject->addAdapter( $some_log_adapter );
		$subject->setTimestamp( $timestamp_helper );

		$subject->withAdapter( $adapter_name )->add( $new_entry );
	}

	/**
	 * @test
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function it_stores_the_entry_in_the_first_registered_adapter() {
		$adapter_name         = 'SomeLogAdapter';
		$another_adapter_name = 'AnotherLogAdapter';

		$new_entry = 'An entry';

		$some_log_adapter = $this->get_adapter_stub( $adapter_name );
		$some_log_adapter->expects( $this->once() )
						 ->method( 'add' );

		$another_log_adapter = $this->get_adapter_stub( $another_adapter_name );
		$another_log_adapter->expects( $this->never() )
							->method( 'add' );

		$subject = new \OTGS_Multi_Log();
		$subject->addAdapter( $some_log_adapter );
		$subject->addAdapter( $another_log_adapter );

		$subject->add( $new_entry );
	}

	/**
	 * @param string $class_name
	 *
	 * @return \OTGS_Log_Adapter|\PHPUnit_Framework_MockObject_MockObject
	 */
	public function get_adapter_stub( $class_name ) {
		$another_log_adapter = $this->getMockBuilder( \OTGS_Log_Adapter::class )
									->setMockClassName( $class_name )
									->disableOriginalConstructor()
									->setMethods( array(
										'add',
										'getEntries',
									) )
									->getMock();

		return $another_log_adapter;
	}

	/**
	 * @param string $class_name
	 *
	 * @return \OTGS_Log_TimeStamp|\PHPUnit_Framework_MockObject_MockObject
	 */
	private function get_timestamp_helper( $class_name ) {
		$another_log_adapter = $this->getMockBuilder( \OTGS_Log_TimeStamp::class )
									->setMockClassName( $class_name )
									->disableOriginalConstructor()
									->setMethods( array(
										'setFormat',
										'get',
									) )
									->getMock();

		return $another_log_adapter;
	}
}