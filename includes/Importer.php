<?php

namespace PCMImages;

class Importer {

	/**
	 * @var
	 */
	protected static $_instance;

	/**
	 * Only make one instance of the Importer
	 *
	 * @return Importer
	 */
	public static function get_instance() {
		if ( ! self::$_instance instanceof Importer ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Add Hooks and Actions
	 */
	protected function __construct() {
		add_action( 'init', array( $this, 'schedule_cron' ) );
		add_action( 'pcm_import_images', array( $this, 'run_importer' ) );
	}

	public function schedule_cron() {
		if ( wp_next_scheduled( 'pcm_import_images' ) ) {
			return;
		}

		$timestamp = date( 'u', strtotime( '4:00 am', current_time( 'timestamp' ) ) );
		wp_schedule_event( $timestamp, 'daily', 'pcm_import_images' );
	}

	public function run_importer() {}

}