<?php

/**
 * @author OnTheGo Systems
 */
class OTGS_WP_Option_Log extends OTGS_Log_Adapter {

	private $option_key;
	private $max_entries;

	/**
	 * OTGS_WP_Option_Log constructor.
	 *
	 * @param string $option_key
	 * @param int    $max_entries
	 */
	public function __construct( $option_key, $max_entries = 0 ) {
		$this->option_key  = $option_key;
		$this->max_entries = $max_entries;
	}

	public function hasTemplate() {
		return true;
	}

	/**
	 * @param string $entry
	 *
	 * @return bool
	 */
	public function addFormatted( $entry ) {
		$entries = $this->getEntries();

		$entries[] = $entry;

		if ( $this->max_entries ) {
			$entries = array_slice( $entries, -$this->max_entries, $this->max_entries );
		}

		return update_option( $this->option_key, $entries, false );
	}

	/**
	 * @return array
	 */
	public function getEntries() {
		return get_option( $this->option_key, array() );
	}

	/**
	 * @param array $entry
	 *
	 * @throws \OTGS_ExpectedFormattedEntryException
	 */
	public function add( array $entry ) {
		$this->throwExpectedFormattedEntryException();
	}
}