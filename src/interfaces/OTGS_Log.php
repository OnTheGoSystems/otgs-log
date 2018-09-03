<?php
/**
 * @author OnTheGo Systems
 */

/**
 * @author OnTheGo Systems
 */
interface OTGS_Log {
	const ENTRY_WARNING = 'W';
	const ENTRY_ERROR   = 'E';
	const ENTRY_INFO    = 'I';

	/**
	 * @param string $entry
	 * @param string $type
	 *
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function add( $entry, $type = 'I' );

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