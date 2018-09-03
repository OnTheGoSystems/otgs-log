<?php

/**
 * @author OnTheGo Systems
 */
class OTGS_File_System_Log extends OTGS_Log_Adapter {

	private $filename;
	private $max_entries;

	/**
	 * OTGS_WP_Option_Log constructor.
	 *
	 * @param string $filename
	 * @param int    $max_entries
	 */
	public function __construct( $filename, $max_entries = 0 ) {
		$this->filename    = $filename;
		$this->max_entries = $max_entries;
	}

	/**
	 * @param string $entry
	 *
	 * @return bool
	 */
	public function add( $entry ) {
		$entries = $this->getEntries();

		$entries[] = $entry;

		if ( $this->max_entries ) {
			$entries = array_slice( $entries, -$this->max_entries, $this->max_entries );
		}

		$contents = implode( PHP_EOL, $entries );
		$contents .= PHP_EOL;

		return file_put_contents( $this->filename, $contents );
	}

	/**
	 * @return array
	 */
	public function getEntries() {
		$contents = '';
		if ( file_exists( $this->filename ) ) {
			$contents = file_get_contents( $this->filename );
		}

		$contents = preg_replace( '/^[\r\n]+/', '', $contents );
		$contents = preg_replace( '/[\r\n]+$/', '', $contents );

		return $contents ? explode( PHP_EOL, $contents ) : array();
	}

}