<?php

require_once 'class-iworks-theme-base.php';

abstract class iWorks_Post_Type extends iWorks_Theme_Base {

	protected $post_type_name = array(
		'faq' => 'faq',
	);

	protected $taxonomy_name = array(
		'faq' => 'faq_cat',
	);


	public function __construct() {
		parent::__construct();
		/**
		 * WordPress Hooks
		 */
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'register_taxonomy' ) );
	}

	abstract public function register_post_type();
	abstract public function register_taxonomy();

}

