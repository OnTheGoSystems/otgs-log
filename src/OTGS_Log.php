<?php

/**
 * @author OnTheGo Systems
 */
class OTGS_Log {
	/** @var OTGS_Log_Adapter[] */
	private $adapters = array();
	/** @var OTGS_Log_Adapter */
	private $current_adapter;

	/**
	 * @param OTGS_Log_Adapter $adapter
	 */
	public function register_adapter( OTGS_Log_Adapter $adapter ) {
		$this->adapters[ get_class( $adapter ) ] = $adapter;
	}

	/**
	 * @param null|string $adapter
	 *
	 * @return OTGS_Log
	 * @throws OTGS_MissingAdaptersException
	 */
	public function log( $adapter = null ) {
		if ( ! $this->adapters ) {
			throw new OTGS_MissingAdaptersException( 'At least one adapter must be specified.' );
		}

		if ( ! $adapter ) {
			$adapter = array_keys( $this->adapters )[0];
		}

		if ( ! array_key_exists( $adapter, $this->adapters ) ) {
			throw new OTGS_MissingAdaptersException( $adapter . ': No matching adapter found' );
		}

		$this->current_adapter = $this->adapters[ $adapter ];

		return $this;
	}

	/**
	 * @param $entry
	 *
	 * @throws OTGS_MissingAdaptersException
	 */
	public function add( $entry ) {
		if ( ! $this->current_adapter ) {
			throw new OTGS_MissingAdaptersException( 'At least one adapter must be specified.' );
		}

		$entries   = $this->current_adapter->get_entries();
		$entries[] = date( 'Y-m-d H:i:s.u' ) . ' ' . $entry;
		$this->current_adapter->save( $entries );
	}
}