<?php

/**
 * @author OnTheGo Systems
 */
class OTGS_JSON_File_Log extends OTGS_File_System_Log {
	public function __construct( $filename, $max_entries = 0 ) {
		parent::__construct( $filename, $max_entries );
		$this->fix_missing_constants();
	}

	public function hasTemplate() {
		return false;
	}

	/**
	 * @param string $entry
	 *
	 * @throws \OTGS_ExpectedArrayEntryException
	 */
	public function addFormatted( $entry ) {
		$this->throwExpectedArrayEntryException();
	}

	/**
	 * @return array
	 */
	public function getEntries() {
		return json_decode( $this->getContents(), true );
	}

	/**
	 * @param array $entry
	 *
	 * @return bool|int
	 */
	public function add( array $entry ) {
		$entries = $this->getEntries();

		$entries[] = $entry;

		if ( $this->max_entries ) {
			$entries = array_slice( $entries, -$this->max_entries, $this->max_entries );
		}

		if ( function_exists( 'wp_json_encode' ) ) {
			$json = wp_json_encode( $entries, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_ERROR_RECURSION | JSON_PARTIAL_OUTPUT_ON_ERROR );
		} else {
			$json = json_encode( $entries, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_ERROR_RECURSION | JSON_PARTIAL_OUTPUT_ON_ERROR );
		}

		return $this->saveContents( $json );
	}

	private function fix_missing_constants() {
		if ( ! defined( 'JSON_UNESCAPED_UNICODE' ) ) {
			define( 'JSON_UNESCAPED_UNICODE', 256 );
		}
		if ( ! defined( 'JSON_UNESCAPED_SLASHES' ) ) {
			define( 'JSON_UNESCAPED_SLASHES', 64 );
		}
		if ( ! defined( 'JSON_NUMERIC_CHECK' ) ) {
			define( 'JSON_NUMERIC_CHECK', 32 );
		}
		if ( ! defined( 'JSON_ERROR_RECURSION' ) ) {
			define( 'JSON_ERROR_RECURSION', 6 );
		}
		if ( ! defined( 'JSON_PARTIAL_OUTPUT_ON_ERROR' ) ) {
			define( 'JSON_PARTIAL_OUTPUT_ON_ERROR', 512 );
		}
	}
}