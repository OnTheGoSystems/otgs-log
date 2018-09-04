<?php

/**
 * @author OnTheGo Systems
 */
class OTGS_JSON_File_Log extends OTGS_File_System_Log {

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
			$json = wp_json_encode( $entries );
		} else {
			$json = json_encode( $entries );
		}

		return $this->saveContents( $json );
	}
}