<?php
/**
 * @author OnTheGo Systems
 */

/**
 * @author OnTheGo Systems
 */
interface OTGS_Log {
	/**
	 * @param string $entry
	 * @param string $type
	 *
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function add( $entry, $type = 'info' );

	/**
	 * @param $entry
	 *
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function addMessage( $entry );

	/**
	 * @param $entry
	 *
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function addError( $entry );

	/**
	 * @param $entry
	 *
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function addWarning( $entry );

	/**
	 * Get all entries
	 *
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function get();
}