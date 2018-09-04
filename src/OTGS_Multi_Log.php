<?php

/**
 * @author OnTheGo Systems
 */
class OTGS_Multi_Log implements OTGS_Log {
	/** @var \OTGS_Log_Adapter[] */
	private $adapters;
	/** @var \OTGS_Log_Adapter */
	private $current_adapter;
	/** @var \OTGS_Log_TimeStamp */
	private $timestamp;
	/** @var string */
	private $entryTemplate;
	/** @var callable */
	private $data_encoding;

	/**
	 * OTGS_Log constructor.
	 *
	 * @param \OTGS_Log_Adapter[] $adapters
	 * @param \OTGS_Log_TimeStamp $timestamp
	 * @param string              $entry_template
	 */
	public function __construct( array $adapters = array(), \OTGS_Log_TimeStamp $timestamp = null, $entry_template = '%timestamp% %type% %entry% %extra_data%' ) {
		foreach ( $adapters as $adapter ) {
			$this->addAdapter( $adapter );
		}

		$this->timestamp     = $timestamp;
		$this->entryTemplate = $entry_template;
	}

	/**
	 * @param string $template Specifies the format which must be used to build the entry.
	 *                       Placeholders: `%timestamp%`, `%type%`, `%entry%`.
	 *                       Defaults to `%timestamp% %type% %entry%`.
	 */
	public function setEntryTemplate( $template ) {
		$this->entryTemplate = $template;
	}

	/**
	 * @param callable $callback Specifies the function to encode non-scalar data.
	 *                           Defaults to `json_encode`.
	 */
	public function setDataEncoding( $callback ) {
		$this->data_encoding = $callback;
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
	 * @return \OTGS_Multi_Log
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function withAdapter( $adapter ) {
		$this->getAdapter( $adapter );

		return $this;
	}

	/**
	 * @param string     $entry
	 * @param string     $type
	 * @param mixed|null $extra_data
	 *
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function add( $entry, $type = 'I', $extra_data = null ) {
		if ( ! $this->current_adapter ) {
			$this->getAdapter();
		}

		$encoded_extra_data = '';
		if ( null !== $extra_data ) {
			$encoded_extra_data = $extra_data;
			if ( ! is_scalar( $extra_data ) ) {
				$encoded_extra_data = call_user_func( $this->getDataEncoding(), $extra_data );
			}
		}

		$timestamp = $this->getTimestamp();

		if ( $this->current_adapter->hasTemplate() ) {
			$formatted_entry = str_replace( array( '%timestamp%', '%type%', '%entry%', '%extra_data%' ), array( $timestamp, $type, $entry, $encoded_extra_data ), $this->entryTemplate );
			$this->current_adapter->addFormatted( trim( $formatted_entry ) );
		} else {
			$this->current_adapter->add( array(
				'timestamp'  => $timestamp,
				'type'       => $type,
				'entry'      => $entry,
				'extra_data' => $encoded_extra_data,
			) );
		}

		if ( $encoded_extra_data && strpos( $this->entryTemplate, '%extra_data%' ) === false ) {
			$formatted_entry = str_replace( array( '%timestamp%', '%type%', '%entry%' ), array( $timestamp, $type, $encoded_extra_data ), $this->entryTemplate );
			$this->current_adapter->addFormatted( trim( $formatted_entry ) );
		}
	}

	/**
	 * @param $entry
	 *
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function addError( $entry ) {
		$this->add( $entry, 'E' );
	}

	/**
	 * @param $entry
	 *
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function addWarning( $entry ) {
		$this->add( $entry, 'W' );
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

	protected function getDataEncoding() {
		if ( ! $this->data_encoding ) {
			return 'json_encode';
		}

		return $this->data_encoding;
	}
}