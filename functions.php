<?php
/**
 * Custom Post Type; Person
 *
 * use __return_true to turn it on
 */
add_filter( 'iworks/theme/load-post-type-person', '__return_false' );

/**
 * Custom Post Type; FAQ
 *
 * use __return_true to turn it on
 */
add_filter( 'iworks/theme/load-post-type-faq', '__return_false' );

/**
 * WP Cron
 *
 * use __return_true to turn it on
 */
add_filter( 'iworks/theme/load-wp-cron', '__return_false' );

/**
 * cookie
 */
add_filter( 'iworks/theme/load-cookies', '__return_false' );

/**
 * Table of Content
 */
add_filter( 'iworks/theme/load-toc', '__return_false' );

/**
 * Load theme class
 */
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

