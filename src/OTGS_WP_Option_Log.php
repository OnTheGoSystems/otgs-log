<?php

/**
 * @author OnTheGo Systems
 */
class OTGS_WP_Option_Log extends OTGS_Log_Adapter {

	private $option_key;

	/**
	 * OTGS_WP_Option_Log constructor.
	 *
	 * @param string $option_key
	 */
	public function __construct( $option_key ) {
		$this->option_key = $option_key;
	}

	/**
	 * @param array $entries
	 */
	public function save( array $entries ) {
		update_option( $this->option_key, $entries, false );
	}

	/**
	 * @return array
	 */
	public function get_entries() {
		return get_option( $this->option_key, array() );
	}

}