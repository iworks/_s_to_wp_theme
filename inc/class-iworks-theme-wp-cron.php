<?php

require_once 'class-iworks-theme-base.php';

class iWorks_Theme_WP_Cron extends iWorks_Theme_Base {

	private $database_data_url = 'http://wordpress/ln.json';
	private $hook              = 'opi-people-of-science';

	public function __construct() {
		add_action( 'iworks_theme_wp_cron', array( $this, 'get_section_database_data' ) );
		add_action( 'shutdown', array( $this, 'action_shutdown_maybe_wp_schedule_event' ) );
	}

	public function get_section_database_data() {
		$response = wp_remote_get( $this->database_data_url );
		if ( is_wp_error( $response ) ) {
			return;
		}
		if ( ! is_array( $response ) ) {
			return;
		}
		if ( empty( $response['body'] ) ) {
			return;
		}
		$data = json_decode( $response['body'], true );
		if ( empty( $data ) ) {
			return;
		}
		if ( ! is_array( $data ) ) {
			return;
		}
		$data['updated'] = time();
		$data['version'] = $this->version;
		update_option( $this->database_data_option_name, $data );
	}

	public function action_shutdown_maybe_wp_schedule_event() {
		$when = wp_next_scheduled( $this->hook );
			d( $when );
		if ( false === $when ) {
			wp_schedule_event( time(), 'twicedaily', $this->hook );
			return;
		}
		$data = get_option( $this->database_data_option_name );
		if ( empty( $data ) ) {
			$this->get_section_database_data();
			return;
		}
		if ( DAY_IN_SECONDS * 1000 > time() - $data['updated'] ) {
			$this->get_section_database_data();
		}
	}
}

