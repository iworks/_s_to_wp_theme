<?php

require_once dirname(__DIR__) . '/class-iworks-theme-base.php';

class iWorks_Theme_Post_Type_Page extends iWorks_Theme_Base {

	public function __construct() {
		parent::__construct();
		add_action( 'init', array( $this, 'add_excerpt_support' ) );
	}

	/**
	 * Add excerpt support to page post type
	 *
	 * @since 1.0.0
	 */
	public function add_excerpt_support() {
		add_post_type_support( 'page', 'excerpt' );
	}
}
