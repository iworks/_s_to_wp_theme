<?php

require_once 'class-iworks-theme-base.php';

class iWorks_Theme extends iWorks_Theme_Base {


	/**
	 * Select2 is compiled?
	 */
	private $is_select2_compiled_by_grunt = true;

	public function __construct() {
		parent::__construct();
		/**
		 * Cookie Class
		 */
		if ( ! is_admin() ) {
			include_once 'class-iworks-cookie-notice.php';
			new iWorks_Cookie_Notice;
		}
		/**
		 * integrations
		 */
		include_once 'integrations/services/class-iworks-service-google-analytics.php';
		new iWorks_Integration_Service_Google_Analitics;
		/**
		 * Post Types
		 */
		if ( apply_filters( 'iworks/theme/load-post-type-person', false ) ) {
			include_once 'class-iworks-post-type-person.php';
			new iWorks_Post_Type_Person;
		}
		if ( apply_filters( 'iworks/theme/load-post-type-faq', false ) ) {
			include_once 'class-iworks-post-type-faq.php';
			new iWorks_Post_Type_FAQ;
		}
		/**
		/**
		 * opinion
		 */
		if ( apply_filters( 'iworks/theme/load-post-type-opinion', false ) ) {
			include_once 'class-iworks-post-type-opinion.php';
			new iWorks_Post_Type_Opinion;
		}
		/**
		 * WP Cron
		 */
		if ( apply_filters( 'iworks/theme/load-wp-cron', false ) ) {
			include_once 'class-iworks-theme-wp-cron.php';
			new iWorks_Theme_WP_Cron;
		}
		/**
		 * functionalities
		 */
		if ( apply_filters( 'iworks/theme/load-cookies', false ) ) {
			include_once 'class-iworks-cookie-notice.php';
			new iWorks_Cookie_Notice;
		}
		if ( apply_filters( 'iworks/theme/load-faq', false ) ) {
			include_once 'class-iworks-toc.php';
			new iWorks_Table_Of_Content;
		}
		/**
		 * hooks
		 */
		add_action( 'after_setup_theme', array( $this, 'add_image_sizes' ) );
		add_action( 'after_setup_theme', array( $this, 'load_theme_textdomain' ) );
		add_action( 'after_setup_theme', array( $this, 'setup' ) );
		add_action( 'init', array( $this, 'register_scripts' ) );
		add_action( 'plugins_loaded', array( $this, 'set_iworks_cache_keys' ) );
		add_action( 'widgets_init', array( $this, 'register_sidebars' ) );
		add_action( 'wp_head', array( $this, 'html_head' ), PHP_INT_MAX );
		add_filter( 'body_class', array( $this, 'body_classses' ) );
		add_filter( 'excerpt_more', array( $this, 'excerpt_more' ) );
		add_action( 'after_setup_theme', array( $this, 'load_theme_textdomain' ) );
		add_action( 'after_setup_theme', array( $this, 'setup' ) );
		add_action( 'after_setup_theme', array( $this, 'content_width' ) );
		if ( apply_filters( 'iworks/theme/show/reusable_blocks_admin_menu', false ) ) {
			add_action( 'admin_menu', array( $this, 'action_admin_menu_add_reusable_blocks_admin_menu' ) );
		}
		add_shortcode( 'iworks_last_posts', array( $this, 'shortcode_iworks_last_posts' ) );
		/**
		 * js
		 */
		add_action( 'login_enqueue_scripts', array( $this, 'enqueue' ), PHP_INT_MAX );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ), PHP_INT_MAX );
		/**
		 * speed
		 */
		add_action( 'wp_enqueue_scripts', array( $this, 'dequeue_scripts' ), PHP_INT_MAX );
		add_action( 'wp_enqueue_scripts', array( $this, 'dequeue_styles' ), PHP_INT_MAX );
		add_action( 'wp_footer', array( $this, 'dequeue_styles' ), PHP_INT_MAX ); // WPML maybe_late_enqueue_template()
		add_filter( 'emoji_svg_url', '__return_false' );
		add_filter( 'wp_resource_hints', array( $this, 'resource_hints' ), 10, 2 );
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		/**
		 * clear generaor type
		 */
		add_filter( 'get_the_generator_html', '__return_empty_string' );
		add_filter( 'get_the_generator_xhtml', '__return_empty_string' );
		add_filter( 'get_the_generator_atom', '__return_empty_string' );
		add_filter( 'get_the_generator_rss2', '__return_empty_string' );
		add_filter( 'get_the_generator_rdf', '__return_empty_string' );
		add_filter( 'get_the_generator_comment', '__return_empty_string' );
		add_filter( 'get_the_generator_export', '__return_empty_string' );
		/**
		 * Plugin: Simple History
		 */
		add_filter( 'simple_history/db_purge_days_interval', array( $this, 'filter_simple_history_db_purge_days_interval' ) );
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @since 1.0.0
	 */
	public function enqueue() {
		$deps = array();
		if ( ! $this->is_select2_compiled_by_grunt ) {
			$deps[] = 'select2';
		}
		wp_enqueue_style( 'iworks-style', get_stylesheet_uri(), $deps, $this->version );
		wp_style_add_data( 'iworks-style', 'rtl', 'replace' );
		wp_enqueue_script( 'THEME_SLUG' );
	}

	public function register_scripts() {
		$deps = array();
		/**
		 * theme
		 */
		wp_register_script(
			'THEME_SLUG',
			$this->url . sprintf( '/assets/scripts/frontend.%sjs', $this->debug ? '' : 'min.' ),
			$deps,
			$this->version,
			true
		);
		$data = array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'i18n'    => array(
				'messages' => array(),
				'modules'  => array(),
			),
		);
		$data = apply_filters( 'wp_localize_script_iworks_theme', $data );
		wp_localize_script( 'THEME_SLUG', 'iworks_theme', $data );
	}

	/**
	 * Remove scripts to improve speed
	 *
	 * @since 1.0.0
	 */
	public function dequeue_scripts() {
		if ( is_admin() ) {
			return;
		}
		wp_deregister_script( 'wp-embed' );
		wp_deregister_script( 'jquery-migrate' );
		wp_deregister_script( 'jquery' );
		wp_register_script( 'jquery', '//ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js', false, '3.5.1' );
	}

	/**
	 * Remove styles to improve speed
	 *
	 * @since 1.0.0
	 */
	public function dequeue_styles() {
		if ( is_admin() ) {
			return;
		}
		wp_deregister_style( 'wp-block-library' );
		wp_deregister_style( 'global-styles' );
	}

	/**
	 *
	 * @since 1.0.0
	 */
	public function html_head() {
		/**
		 * turn off iOS phone number scraping
		 */
		echo '<meta name="format-detection" content="telephone=no" />' . PHP_EOL;
		/**
		 * preload fonts
		 */
		$fonts = array();
		foreach ( $fonts as $one ) {
			printf(
				'<link rel="preload" href="%s" as="font" type="font/woff" crossorigin />%s',
				wp_make_link_relative( $this->get_asset_url( $one, 'fonts' ) ),
				PHP_EOL
			);
		}
	}

	/**
	 * Add dns-prefetch
	 *
	 * @since 1.0.0
	 */
	public function resource_hints( $urls, $relation_types ) {
		if ( 'dns-prefetch' === $relation_types ) {
			// $urls[] = 'https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js?ver=1.12.4';
		}
		return $urls;
	}

	public function body_classses( $classes ) {
		return $classes;
	}

	public function add_image_sizes() {
		add_image_size( 'list', 600, 400, true );
	}

	public function register_sidebars() {
		register_sidebar(
			array(
				'name'          => __( 'Footer One', 'THEME_SLUG' ),
				'id'            => 'footer-1',
				'before_widget' => '<div>',
				'after_widget'  => '</div>',
			)
		);
		register_sidebar(
			array(
				'name'          => esc_html__( 'Sidebar', 'THEME_SLUG' ),
				'id'            => 'sidebar-1',
				'description'   => esc_html__( 'Add widgets here.', 'THEME_SLUG' ),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			)
		);
	}

	/**
	 * Plugin: Simple History: db purge days interval
	 */
	public function filter_simple_history_db_purge_days_interval( $days ) {
		$days = 365;
		return $days;
	}

	public function excerpt_more( $more ) {
		return '&hellip;';
	}

	public function set_iworks_cache_keys() {
		do_action( 'iworks_cache_keys', array( $this, 'add_iworks_cache_keys' ), 'slider' );
	}

	public function load_theme_textdomain() {
		load_theme_textdomain( 'THEME_SLUG', get_template_directory() . '/languages' );
	}

	public function setup() {
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		register_nav_menus(
			array(
				'primary' => esc_html__( 'Primary', 'THEME_SLUG' ),
				'footer'  => esc_html__( 'Footer Links', 'THEME_SLUG' ),
			)
		);
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
			)
		);
	}

	public function content_width() {
		$GLOBALS['content_width'] = apply_filters( 'iworks_content_width', 640 );
	}

	public function action_admin_menu_add_reusable_blocks_admin_menu() {
		add_menu_page(
			__( 'Patterns', 'THEME_SLUG' ),
			__( 'Patterns', 'THEME_SLUG' ),
			'edit_posts',
			add_query_arg( 'post_type', 'wp_block', 'edit.php' ),
			'',
			'dashicons-editor-table',
			22
		);
	}
	public function shortcode_iworks_last_posts( $atts, $content ) {
		$args      = array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => 3,
		);
		$the_query = new WP_Query( $args );
		/**
		 * No data!
		 */
		if ( ! $the_query->have_posts() ) {
			return $content;
		}
		/**
		 * Content
		 */
		ob_start();
		get_template_part( 'template-parts/last/header' );
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			get_template_part( 'template-parts/last/one', get_post_type() );
		}
		/* Restore original Post Data */
		wp_reset_postdata();
		get_template_part( 'template-parts/last/footer' );
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
}

