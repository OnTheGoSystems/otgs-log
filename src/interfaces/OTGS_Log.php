<?php
/**
 * @author OnTheGo Systems
 */

/**
 * @author OnTheGo Systems
 */
interface OTGS_Log {
	/**
	 * @param string     $entry
	 * @param int        $level
	 * @param array|null $extra_data
	 *
	 * @return
	 */
	public function add( $entry, $level, array $extra_data = null );

	/**
	 * Get all entries
	 *
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function get();
}