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
	 * @return string
	 */
	public function get();
}