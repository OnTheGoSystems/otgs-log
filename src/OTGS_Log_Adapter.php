<?php
/**
 * @author OnTheGo Systems
 */

/**
 * @author OnTheGo Systems
 */
abstract class OTGS_Log_Adapter {
	abstract public function save( array $entries );

	/**
	 * @return array
	 */
	abstract public function get_entries();
}