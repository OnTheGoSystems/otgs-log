<?php

/**
 * @author OnTheGo Systems
 */
class OTGS_Log_Timestamp_Date implements OTGS_Log_TimeStamp {
	private $format;

	/**
	 * OTGS_Log_Timestamp_Date constructor.
	 *
	 * @param $format
	 */
	public function __construct( $format = 'Y-m-d H:i:s.u' ) {
		$this->format = $format;
	}

	/**
	 * @param string $format
	 */
	public function setFormat( $format ) {
		$this->format = $format;
	}

	/**
	 * @return string
	 */
	public function get() {
		return date( $this->format );
	}
}