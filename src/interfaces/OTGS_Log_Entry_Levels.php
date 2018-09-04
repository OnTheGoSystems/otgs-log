<?php
/**
 * @author OnTheGo Systems
 */

/**
 * @author OnTheGo Systems
 *
 * @see    https://en.wikipedia.org/wiki/Syslog#Severity_levels
 */
interface OTGS_Log_Entry_Levels {
	const TYPE_EMERGENCY     = "Emergency";
	const TYPE_ALERT         = "Alert";
	const TYPE_CRITICAL      = "Critical";
	const TYPE_ERROR         = "Error";
	const TYPE_WARNING       = "Warning";
	const TYPE_NOTICE        = "Notice";
	const TYPE_INFORMATIONAL = "Informational";
	const TYPE_DEBUG         = "Debug";

	const LEVEL_EMERGENCY     = 0;
	const LEVEL_ALERT         = 1;
	const LEVEL_CRITICAL      = 2;
	const LEVEL_ERROR         = 3;
	const LEVEL_WARNING       = 4;
	const LEVEL_NOTICE        = 5;
	const LEVEL_INFORMATIONAL = 6;
	const LEVEL_DEBUG         = 7;

	const DESCRIPTION_EMERGENCY     = "System is unusable.";
	const DESCRIPTION_ALERT         = "Action must be taken immediately";
	const DESCRIPTION_CRITICAL      = "Critical conditions";
	const DESCRIPTION_ERROR         = "Error conditions";
	const DESCRIPTION_WARNING       = "Warning conditions";
	const DESCRIPTION_NOTICE        = "Normal but significant conditions";
	const DESCRIPTION_INFORMATIONAL = "Informational messages";
	const DESCRIPTION_DEBUG         = "Debug-level messages";

	/**
	 * @param int $level
	 *
	 * @return null|string
	 */
	public function getName( $level );

	/**
	 * @param int $level
	 *
	 * @return false|string
	 */
	public function getDescription( $level );
}