<?php

namespace PCMImages;

class Galleries {

	/**
	 * @var
	 */
	protected static $_instance;

	/**
	 * Only make one instance of the Galleries
	 *
	 * @return Galleries
	 */
	public static function get_instance() {
		if ( ! self::$_instance instanceof Galleries ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Add Hooks and Actions
	 */
	protected function __construct() {	}

}