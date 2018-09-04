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
	/** @var \OTGS_Log_Entry_Levels */
	private $entry_types;

	/**
	 * OTGS_Log constructor.
	 *
	 * @param \OTGS_Log_Adapter[]         $adapters
	 * @param \OTGS_Log_TimeStamp         $timestamp
	 * @param string                      $entry_template
	 * @param \OTGS_Log_Entry_Levels|null $entry_types
	 */
	public function __construct( array $adapters = array(), \OTGS_Log_TimeStamp $timestamp = null, $entry_template = '%timestamp% %level% %entry% %extra_data%', OTGS_Log_Entry_Levels $entry_types = null ) {
		if ( ! $entry_types ) {
			$entry_types = new OTGS_Log_Entry_Levels_Default();
		}
		$this->entry_types = $entry_types;

		foreach ( $adapters as $adapter ) {
			$this->addAdapter( $adapter );
		}

		$this->timestamp     = $timestamp;
		$this->entryTemplate = $entry_template;
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
	 * @param int        $level
	 * @param array|null $extra_data
	 *
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function add( $entry, $level, array $extra_data = array() ) {
		if ( ! $this->current_adapter ) {
			$this->getAdapter();
		}
		$level_name = $this->entry_types->getName( $level );

		$extra_data['description'] = $this->entry_types->getDescription( $level );

		$timestamp = $this->getTimestampValue();

		if ( $this->current_adapter->hasTemplate() ) {

			$encoded_extra_data = '';
			if ( null !== $extra_data ) {
				$encoded_extra_data = $extra_data;
				if ( ! is_scalar( $extra_data ) ) {
					$encoded_extra_data = call_user_func( $this->getDataEncoding(), $extra_data );
				}
			}

			$formatted_entry = str_replace( array( '%timestamp%', '%level%', '%entry%', '%extra_data%' ), array( $timestamp, $level, $entry, $encoded_extra_data ), $this->entryTemplate );
			$this->current_adapter->addFormatted( trim( $formatted_entry ) );

			if ( $encoded_extra_data && strpos( $this->entryTemplate, '%extra_data%' ) === false ) {
				$formatted_entry = str_replace( array( '%timestamp%', '%entry%' ), array( $timestamp, $encoded_extra_data ), $this->entryTemplate );
				$this->current_adapter->addFormatted( trim( $formatted_entry ) );
			}

		} else {
			$entry1 = array(
				'timestamp'  => $timestamp,
				'level'      => $level,
				'level_name' => $level_name,
				'message'    => $entry,
				'extra'      => $extra_data,
				'datetime'   => array(
					'date'     => $timestamp,
					'timezone' => $this->getTimeStamp()->getTimeZoneValue(),
				)
			);
			$this->current_adapter->add( $entry1 );
		}
	}

	/**
	 * @param $entry
	 * @param array|null $extra_data
	 *
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function addError( $entry, array $extra_data = array() ) {
		$this->add( $entry, OTGS_Log_Entry_Levels::LEVEL_ERROR, $extra_data );
	}

	/**
	 * @param $entry
	 * @param array|null $extra_data
	 *
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function addWarning( $entry, array $extra_data = array() ) {
		$this->add( $entry, OTGS_Log_Entry_Levels::LEVEL_WARNING, $extra_data );
	}

	/**
	 * @param            $entry
	 * @param array|null $extra_data
	 *
	 * @throws \OTGS_MissingAdaptersException
	 */
	public function addInfo( $entry, array $extra_data = array() ) {
		$this->add( $entry, OTGS_Log_Entry_Levels::LEVEL_INFORMATIONAL, $extra_data );
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
	 * @param string $template Specifies the format which must be used to build the entry.
	 *                         Placeholders: `%timestamp%`, `%level%`, `%entry%`, `%extra_data%`.
	 *                         Defaults to `%timestamp% %level% %entry% %extra_data%`.
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
	 * @return false|string
	 */
	protected function getTimestampValue() {
		return $this->getTimeStamp()->get();
	}

	protected function getDataEncoding() {
		if ( ! $this->data_encoding ) {
			return 'json_encode';
		}

		return $this->data_encoding;
	}

	/**
	 * @return \OTGS_Log_TimeStamp
	 */
	protected function getTimeStamp() {
		if ( ! $this->timestamp ) {
			$this->timestamp = new OTGS_Log_Timestamp_Date( 'Y-m-d H:i:s.u' );
		}

		return $this->timestamp;
	}
}