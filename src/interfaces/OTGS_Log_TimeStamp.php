<?php

/**
 * @author OnTheGo Systems
 */
interface OTGS_Log_TimeStamp {
	/**
	 * @param string $format
	 */
	public function setFormat( $format );

	/**
	 * @return float
	 */
	public function get();

	/**
	 * @return string
	 */
	public function getFormatted();

	/**
	 * @return string
	 */
	public function getTimeZoneValue();
}