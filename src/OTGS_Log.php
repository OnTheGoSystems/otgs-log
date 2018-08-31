<?php

/**
 * @author OnTheGo Systems
 */
class OTGS_Log {
	/** @var \OTGS_Log_Adapter[] */
	private $adapters;
	/** @var \OTGS_Log_Adapter */
	private $current_adapter;
	/** @var \OTGS_Log_TimeStamp */
	private $timestamp;
	/** @var string */
	private $format;

	/**
	 * OTGS_Log constructor.
	 *
	 * @param \OTGS_Log_Adapter[] $adapters
	 * @param \OTGS_Log_TimeStamp $timestamp
	 * @param string              $format
	 */
	public function __construct( array $adapters = array(), \OTGS_Log_TimeStamp $timestamp = null, $format = '%timestamp% %type% %entry%' ) {
		foreach ( $adapters as $adapter ) {
			$this->addAdapter( $adapter );
		}

		$this->timestamp = $timestamp;
		$this->format    = $format;
	}

	/**
	 * @param string $format Specifies the format which must be used to build the entry.
	 *                       Placeholders: `%timestamp%`, `%type%`, `%entry%`.
	 *                       Defaults to `%timestamp% %type% %entry%`.
	 */
	public function setEntryFormat( $format ) {
		$this->format = $format;
	}

	/**
	 * @param \OTGS_Log_Adapter $adapter
	 */
	public function addAdapter( OTGS_Log_Adapter $adapter ) {
		$this->adapters[ get_class( $adapter ) ] = $adapter;
	}

	public function setTimestamp( OTGS_Log_TimeStamp $timestamp ) {
		$this->timestamp = $timestamp;
	}

	/**
	 * @param null|string $adapter
	 *
	 * @return \OTGS_Log
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function withAdapter( $adapter ) {
		$this->getAdapter( $adapter );

		return $this;
	}

	/**
	 * @param string $entry
	 * @param string $type
	 *
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function add( $entry, $type = 'info' ) {
		if ( ! $this->current_adapter ) {
			$this->getAdapter();
		}

		$formatted_entry = str_replace( array( '%timestamp%', '%type%', '%entry%' ), array( $this->getTimestamp(), $type, $entry ), $this->format );

		$this->current_adapter->add( $formatted_entry );
	}

	/**
	 * @param $entry
	 *
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function addMessage( $entry ) {
		$this->add( $entry, 'Message' );
	}

	/**
	 * @param $entry
	 *
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function addError( $entry ) {
		$this->add( $entry, 'Error' );
	}

	/**
	 * @param $entry
	 *
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function addWarning( $entry ) {
		$this->add( $entry, 'Warning' );
	}

	/**
	 * @param $adapter
	 *
	 * @return \OTGS_Log_Adapter
	 * @throws \OTGS_MissingAdaptersException
	 */
	protected function getAdapter( $adapter = null ) {
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

		return $this->current_adapter;
	}

	/**
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function get() {
		if ( ! $this->current_adapter ) {
			$this->getAdapter();
		}
		if ( ! $this->current_adapter ) {
			throw new OTGS_MissingAdaptersException( 'At least one adapter must be specified.' );
		}

		return $this->current_adapter->getEntries();
	}

	/**
	 * @return false|string
	 */
	protected function getTimestamp() {
		if ( $this->timestamp ) {
			$timestamp = $this->timestamp->get();
		} else {
			$timestamp = date( 'Y-m-d H:i:s.u' );
		}

		return $timestamp;
	}
}