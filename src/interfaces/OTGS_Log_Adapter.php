<?php
/**
 * @author OnTheGo Systems
 */

/**
 * @author OnTheGo Systems
 */
abstract class OTGS_Log_Adapter {
	/**
	 * @param string $entry
	 *
	 * @return bool
	 */
	abstract public function add( $entry );

	/**
	 * @return array|string|object
	 */
	abstract public function getEntries();
}