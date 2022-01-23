<?php


require_once get_template_directory() . '/inc/class-iworks-theme.php';
new iWorks_Theme;

// require_once get_template_directory() . '/inc/class-iworks-cache.php';
// new iWorks_Cache;

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

