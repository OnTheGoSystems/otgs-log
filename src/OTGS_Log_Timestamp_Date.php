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
	 * @return float
	 */
	public function get() {
		list($usec, $sec) = explode(' ',microtime());
		return ((float)$usec + (float)$sec);
	}

	/**
	 * @return string
	 */
	public function getFormatted() {
		return $this->getDateInstance()->format( $this->format );
	}

	/**
	 * @return \DateTimeZone
	 */
	public function getTimeZone() {
		return $this->getDateInstance()->getTimezone();
	}

	/**
	 * @return string
	 */
	public function getTimeZoneValue() {
		return $this->getTimezone()->getName();
	}

	/**
	 * @return \DateTime
	 */
	private function getDateInstance() {
		return DateTime::createFromFormat( '0.u00 U', microtime() );
	}
}