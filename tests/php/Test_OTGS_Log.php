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

		$subject = new \OTGS_Log();

		$this->expectException( \OTGS_MissingAdaptersException::class );

		$subject->log( $adapter_name )->add( $entry );
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

		$subject = new \OTGS_Log();
		$subject->register_adapter( $some_log_adapter );
		$subject->log( $another_adapter_name )->add( $entry );
	}

	/**
	 * @test
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function it_stores_the_entry_in_the_log_adapter() {
		$adapter_name = 'SomeLogAdapter';
		$entry        = 'An entry';

		$some_log_adapter = $this->get_adapter_stub( $adapter_name );
		$some_log_adapter->expects( $this->once() )
		                 ->method( 'save' )
		                 ->with( function ( $arg ) use ($entry) {
			                 return strpos( $arg, $entry ) !== 0;
		                 } );

		$subject = new \OTGS_Log();
		$subject->register_adapter( $some_log_adapter );

		$subject->log( $adapter_name )->add( $entry );
	}

	/**
	 * @test
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function it_stores_the_entry_in_the_first_registered_adapter() {
		$adapter_name         = 'SomeLogAdapter';
		$another_adapter_name = 'AnotherLogAdapter';
		$entry                = 'An entry';

		$some_log_adapter = $this->get_adapter_stub( $adapter_name );
		$some_log_adapter->expects( $this->once() )
		                 ->method( 'add' )
		                 ->with( $entry );

		$another_log_adapter = $this->get_adapter_stub( $another_adapter_name );
		$another_log_adapter->expects( $this->never() )
		                    ->method( 'add' )
		                    ->with( $entry );

		$subject = new \OTGS_Log();
		$subject->register_adapter( $some_log_adapter );
		$subject->register_adapter( $another_log_adapter );

		$subject->log()->add( $entry );
	}

	/**
	 * @param string $class_name
	 *
	 * @return \OTGS_Log_Adapter|\PHPUnit_Framework_MockObject_MockObject
	 */
	public function get_adapter_stub( $class_name ) {
		/** @var \OTGS_Log_Adapter|\PHPUnit_Framework_MockObject_MockObject $another_log_adapter */
		$another_log_adapter = $this->getMockBuilder( \OTGS_Log_Adapter::class )
		                            ->setMockClassName( $class_name )
		                            ->disableOriginalConstructor()
		                            ->setMethods( array(
			                            'save',
			                            'add',
			                            'get_entries',
		                            ) )
		                            ->getMock();

		return $another_log_adapter;
	}
}