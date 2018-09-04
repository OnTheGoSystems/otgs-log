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
	 * @param string     $entry
	 * @param string     $type
	 * @param mixed|null $extra_data
	 *
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function add( $entry, $type = 'I', $extra_data = null );

	/**
	 * @param $entry
	 * @param mixed|null $extra_data
	 *
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function addError( $entry, $extra_data = null );

	/**
	 * @param $entry
	 * @param mixed|null $extra_data
	 *
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function addWarning( $entry, $extra_data = null );

	/**
	 * Get all entries
	 *
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function get();
}