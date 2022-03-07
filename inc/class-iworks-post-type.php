<?php

require_once 'class-iworks-theme-base.php';

abstract class iWorks_Post_Type extends iWorks_Theme_Base {

	protected $post_type_name = '';

	public function __construct() {
		parent::__construct();
		/**
		 * WordPress Hooks
		 */
	}


}

