<?php

require_once 'class-iworks-post-type.php';

class iWorks_Post_Type_Opinion extends iWorks_Post_Type {

	private $list = array();

	public function __construct() {
		parent::__construct();
		add_shortcode( 'iworks_opinions_list', array( $this, 'get_list' ) );
		add_action( 'add_meta_boxes', array( $this, 'action_add_meta_boxes_add' ) );
		add_filter( 'iworks_post_type_opinion_options_list', array( $this, 'get_options_list_array' ), 10, 2 );
		add_action( 'save_post_' . $this->post_type_name['opinion'], array( $this, 'action_save_post_page' ), 10, 3 );
		$this->meta_boxes[ $this->post_type_name['opinion'] ] = array(
			'opinion-data' => array(
				'title'  => __( 'Opinion Data', 'sellspark-io-theme-adjc-pl' ),
				'fields' => array(
					array(
						'name'    => 'stars',
						'type'    => 'select',
						'label'   => esc_html__( 'The Opinion Stars', 'sellspark-io-theme-adjc-pl' ),
						'options' => array(
							'5' => '&bigstar;&bigstar;&bigstar;&bigstar;&bigstar;',
							'4' => '&bigstar;&bigstar;&bigstar;&bigstar;',
							'3' => '&bigstar;&bigstar;&bigstar;',
							'2' => '&bigstar;&bigstar;',
							'1' => '&bigstar;',
						),
					),
					array(
						'name'  => 'opinion_url',
						'type'  => 'url',
						'label' => esc_html__( 'The Opinion URL', 'sellspark-io-theme-adjc-pl' ),
					),
					array(
						'name'  => 'author_url',
						'type'  => 'url',
						'label' => esc_html__( 'The Opinion Author URL', 'sellspark-io-theme-adjc-pl' ),
					),
				),
			),
		);
	}

	/**
	 * Get post list
	 *
	 * @since 1.3.9
	 *
	 * @param array $atts Shortcode attributes
	 * @param string $content current content
	 *
	 * @return string $content
	 */
	public function get_list( $atts, $content = '' ) {
		$args                = wp_parse_args(
			$atts,
			array(
				'orderby'        => 'rand',
				'posts_per_page' => 4,
			)
		);
		$args['post_type']   = $this->post_type_name['opinion'];
		$args['post_status'] = 'publish';
		$the_query           = new WP_Query( $args );
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
		get_template_part( 'template-parts/opinions/header' );
		$join = rand( 0, 2 );
		$i    = 0;
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			$args = array(
				'join' => $join,
				'i'    => $i++,
			);
			get_template_part( 'template-parts/opinions/one', get_post_type(), $args );
		}
		/* Restore original Post Data */
		wp_reset_postdata();
		get_template_part( 'template-parts/opinions/footer' );
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

	/**
	 * Register Custom Post Type
	 *
	 * @since 1.0.8
	 */
	public function register_post_type() {
		$labels = array(
			'name'                  => _x( 'Opinions', 'Post Type General Name', 'sellspark-io-theme-adjc-pl' ),
			'singular_name'         => _x( 'Opinion', 'Post Type Singular Name', 'sellspark-io-theme-adjc-pl' ),
			'menu_name'             => __( 'Opinions', 'sellspark-io-theme-adjc-pl' ),
			'name_admin_bar'        => __( 'Opinions', 'sellspark-io-theme-adjc-pl' ),
			'archives'              => __( 'Opinions', 'sellspark-io-theme-adjc-pl' ),
			'all_items'             => __( 'Opinions', 'sellspark-io-theme-adjc-pl' ),
			'add_new_item'          => __( 'Add New Opinion', 'sellspark-io-theme-adjc-pl' ),
			'add_new'               => __( 'Add New', 'sellspark-io-theme-adjc-pl' ),
			'new_item'              => __( 'New Opinion', 'sellspark-io-theme-adjc-pl' ),
			'edit_item'             => __( 'Edit Opinion', 'sellspark-io-theme-adjc-pl' ),
			'update_item'           => __( 'Update Opinion', 'sellspark-io-theme-adjc-pl' ),
			'view_item'             => __( 'View Opinion', 'sellspark-io-theme-adjc-pl' ),
			'view_items'            => __( 'View Opinion', 'sellspark-io-theme-adjc-pl' ),
			'search_items'          => __( 'Search Opinion', 'sellspark-io-theme-adjc-pl' ),
			'not_found'             => __( 'Not found', 'sellspark-io-theme-adjc-pl' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'sellspark-io-theme-adjc-pl' ),
			'items_list'            => __( 'Opinion list', 'sellspark-io-theme-adjc-pl' ),
			'items_list_navigation' => __( 'Opinion list navigation', 'sellspark-io-theme-adjc-pl' ),
			'filter_items_list'     => __( 'Filter items list', 'sellspark-io-theme-adjc-pl' ),
		);
		$args   = array(
			'can_export'          => true,
			'capability_type'     => 'page',
			'description'         => __( 'Opinion', 'sellspark-io-theme-adjc-pl' ),
			'exclude_from_search' => true,
			'has_archive'         => false,
			'hierarchical'        => false,
			'label'               => __( 'Opinions', 'sellspark-io-theme-adjc-pl' ),
			'labels'              => $labels,
			'menu_icon'           => 'dashicons-businessopinion',
			'public'              => false,
			'show_in_admin_bar'   => false,
			'menu_position'       => 20,
			'show_in_nav_menus'   => false,
			'show_ui'             => true,
			'show_in_menu'        => 'adjc',
			'show_in_rest'        => false,
			'supports'            => array( 'title', 'thumbnail', 'editor', 'revisions' ),
			'rewrite'             => array(
				'slug' => _x( 'opinion', 'iWorks Post Type Opinion SLUG', 'sellspark-io-theme-adjc-pl' ),
			),
		);
		register_post_type(
			$this->post_type_name['opinion'],
			apply_filters( 'iworks_post_type_opinion_args', $args )
		);
	}

	/**
	 * Register Custom Taxonomy
	 *
	 * @since 1.0.8
	 */
	public function register_taxonomy() {
	}

	/**
	 * Get post list
	 *
	 * @param array $list options list
	 * @param array $atts WP_Query attributes
	 *
	 * @return string $content
	 */
	public function get_options_list_array( $list, $atts = array() ) {
		if ( ! empty( $this->list ) ) {
			return $this->list;
		}
		$list       = $this->get_select_array( $this->post_type_name['opinion'] );
		$this->list = $list;
		return $list;
	}


	public function action_add_meta_boxes_add( $post_type ) {
		if ( $post_type !== $this->post_type_name['opinion'] ) {
			return;
		}
		$this->add_meta_boxes( $this->post_type_name['opinion'] );
	}

	public function action_save_post_page( $post_id, $post, $update ) {
		$this->save_meta( $post_id, $post, $update, $this->post_type_name['opinion'] );
	}
}

