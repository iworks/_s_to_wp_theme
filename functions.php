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
add_filter( 'iworks/theme/load-post-type-faq', '__return_true' );


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

/**
 * cookie
 */
// require_once 'inc/class-iworks-cookie-notice.php';
// new iWorks_Cookie_Notice;

/**
 * TOC
 */
// require_once 'inc/class-iworks-toc.php';
// new iWorks_Table_Of_Content;

