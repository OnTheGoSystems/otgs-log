<?php
/**
 * @author OnTheGo Systems
 */

/**
 * @author OnTheGo Systems
 */
abstract class OTGS_Log_Adapter {
	/**
	 * @return mixed
	 */
	abstract public function hasTemplate();

	/**
	 * @param string $entry
	 *
	 * @return bool
	 */
	abstract public function addFormatted( $entry );

	/**
	 * @param array $entry
	 *
	 * @return bool
	 */
	abstract public function add( array $entry );

	/**
	 * @return array|string|object
	 */
	abstract public function getEntries();


	/**
	 * @throws \OTGS_ExpectedFormattedEntryException
	 */
	protected function throwExpectedFormattedEntryException() {
		throw new OTGS_ExpectedFormattedEntryException( '\OTGS_ExpectedFormattedEntryException can only handle formatted entries' );
	}

	/**
	 * @throws \OTGS_ExpectedArrayEntryException
	 */
	protected function throwExpectedArrayEntryException() {
		throw new OTGS_ExpectedArrayEntryException( '\ExpectedArrayEntryException can only handle array entries' );
	}
}