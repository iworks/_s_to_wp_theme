<?php

require_once 'class-iworks-theme-base.php';

abstract class iWorks_Custom_Post_Type extends iWorks_Theme_Base {

	protected $post_type_name = '';

	public function __construct() {
		parent::__construct();
		/**
		 * WordPress Hooks
		 */
		add_action( 'init', array( $this, 'custom_post_type' ), 0 );
	}

	/**
	 * Register Custom Post Type
	 *
	 * @since 1.0.0
	 */
	abstract public function custom_post_type();

}

