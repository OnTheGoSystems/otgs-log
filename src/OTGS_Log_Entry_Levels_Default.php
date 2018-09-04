<?php
/**
 * @author OnTheGo Systems
 */


class OTGS_Log_Entry_Levels_Default implements OTGS_Log_Entry_Levels {
	private $types;
	private $descriptions;

	/**
	 * OTGS_Log_Entry_Types constructor.
	 */
	public function __construct() {
		$this->types        = array(
			self::LEVEL_EMERGENCY     => self::TYPE_EMERGENCY,
			self::LEVEL_ALERT         => self::TYPE_ALERT,
			self::LEVEL_CRITICAL      => self::TYPE_CRITICAL,
			self::LEVEL_ERROR         => self::TYPE_ERROR,
			self::LEVEL_WARNING       => self::TYPE_WARNING,
			self::LEVEL_NOTICE        => self::TYPE_NOTICE,
			self::LEVEL_INFORMATIONAL => self::TYPE_INFORMATIONAL,
			self::LEVEL_DEBUG         => self::TYPE_DEBUG,
		);
		$this->descriptions = array(
			self::LEVEL_EMERGENCY     => self::DESCRIPTION_EMERGENCY,
			self::LEVEL_ALERT         => self::DESCRIPTION_ALERT,
			self::LEVEL_CRITICAL      => self::DESCRIPTION_CRITICAL,
			self::LEVEL_ERROR         => self::DESCRIPTION_ERROR,
			self::LEVEL_WARNING       => self::DESCRIPTION_WARNING,
			self::LEVEL_NOTICE        => self::DESCRIPTION_NOTICE,
			self::LEVEL_INFORMATIONAL => self::DESCRIPTION_INFORMATIONAL,
			self::LEVEL_DEBUG         => self::DESCRIPTION_DEBUG,
		);
	}

	/**
	 * @param int $level
	 *
	 * @return null|string
	 */
	public function getName( $level ) {
		if ( array_key_exists( $level, $this->types ) ) {
			return $this->types[ $level ];
		}

		return null;
	}

	/**
	 * @param int $level
	 *
	 * @return false|string
	 */
	public function getDescription( $level ) {
		if ( array_key_exists( $level, $this->descriptions ) ) {
			return $this->descriptions[ $level ];
		}

		return null;
	}
}