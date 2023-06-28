<?php

class iWorks_Post_Type_Person {

	private $post_type_name = 'iworks_person';
	private $taxonomy_name  = 'iworks_person_role';

	public function __construct() {
		add_action( 'init', array( $this, 'register' ), 0 );
		add_shortcode( 'iworks_persons_list', array( $this, 'get_list' ) );
		add_filter( 'og_og_type_value', array( $this, 'filter_og_og_type_value' ) );
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
		$args      = array(
			'post_type'      => $this->post_type_name,
			'orderby'        => 'rand',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
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
		$content .= '<div class="wp-block-group alignfull work-with-us work-with-us-heroes">';
		$content .= '<div class="wp-block-group__inner-container">';
		$content .= sprintf(
			'<h2>%s</h2>',
			esc_html__( 'Learn what our employees are saying.', 'filtry' )
		);
		$content .= sprintf(
			'<p class="become-one-of-them">%s</p>',
			esc_html__( 'Become one of them!', 'filtry' )
		);
		$content .= '<ul>';
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			$content .= sprintf( '<li class="%s">', implode( ' ', get_post_class() ) );
			$content .= sprintf( '<h3>%s</h3>', get_the_title() );
			$content .= '<div class="post-inner">';
			$content .= '<blockquote class="post-content">';
			$content .= get_the_content();
			$content .= '</blockquote>';
			$content .= '</div>';
			$content .= get_the_post_thumbnail( get_the_ID(), 'full' );
			$content .= '<div class="post-excerpt">';
			$content .= get_the_excerpt();
			$content .= '</div>';
			$content .= '</li>';
		}
		/* Restore original Post Data */
		wp_reset_postdata();
		$content .= '</ul>';
		$content .= '</div>';
		$content .= '</div>';
		return $content;
	}

	/**
	 * Register CPT & CT
	 *
	 * @since 1.0.8
	 */
	public function register() {
		$this->custom_post_type();
		$this->custom_taxonomy();
	}

	/**
	 * Register Custom Post Type
	 *
	 * @since 1.0.8
	 */
	private function custom_post_type() {
		$labels = array(
			'name'                  => _x( 'Persons', 'Post Type General Name', 'filtry' ),
			'singular_name'         => _x( 'Person', 'Post Type Singular Name', 'filtry' ),
			'menu_name'             => __( 'Persons', 'filtry' ),
			'name_admin_bar'        => __( 'Persons', 'filtry' ),
			'archives'              => __( 'Persons', 'filtry' ),
			'all_items'             => __( 'Persons', 'filtry' ),
			'add_new_item'          => __( 'Add New Person', 'filtry' ),
			'add_new'               => __( 'Add New', 'filtry' ),
			'new_item'              => __( 'New Person', 'filtry' ),
			'edit_item'             => __( 'Edit Person', 'filtry' ),
			'update_item'           => __( 'Update Person', 'filtry' ),
			'view_item'             => __( 'View Person', 'filtry' ),
			'view_items'            => __( 'View Person', 'filtry' ),
			'search_items'          => __( 'Search Person', 'filtry' ),
			'not_found'             => __( 'Not found', 'filtry' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'filtry' ),
			'items_list'            => __( 'Person list', 'filtry' ),
			'items_list_navigation' => __( 'Person list navigation', 'filtry' ),
			'filter_items_list'     => __( 'Filter items list', 'filtry' ),
		);
		$args   = array(
			'can_export'          => true,
			'capability_type'     => 'page',
			'description'         => __( 'Person', 'filtry' ),
			'exclude_from_search' => true,
			'has_archive'         => false,
			'hierarchical'        => false,
			'label'               => __( 'Persons', 'filtry' ),
			'labels'              => $labels,
			'menu_icon'           => 'dashicons-businessperson',
			'public'              => true,
			'show_in_admin_bar'   => false,
			'menu_position'       => 20,
			'show_in_nav_menus'   => false,
			'show_ui'             => true,
			'show_in_rest'        => false,
			'supports'            => array( 'title', 'thumbnail', 'editor', 'revisions' ),
			'rewrite'             => array(
				'slug' => _x( 'person', 'iWorks Post Type Person SLUG', 'filtry' ),
			),
		);
		register_post_type( $this->post_type_name, $args );
	}

	/**
	 * Register Custom Taxonomy
	 *
	 * @since 1.0.8
	 */
	private function custom_taxonomy() {
		$labels = array(
			'name'                       => _x( 'Roles', 'Role General Name', 'filtry' ),
			'singular_name'              => _x( 'Role', 'Role Singular Name', 'filtry' ),
			'menu_name'                  => __( 'Roles', 'filtry' ),
			'all_items'                  => __( 'All Roles', 'filtry' ),
			'parent_item'                => __( 'Parent Role', 'filtry' ),
			'parent_item_colon'          => __( 'Parent Role:', 'filtry' ),
			'new_item_name'              => __( 'New Role Name', 'filtry' ),
			'add_new_item'               => __( 'Add New Role', 'filtry' ),
			'edit_item'                  => __( 'Edit Role', 'filtry' ),
			'update_item'                => __( 'Update Role', 'filtry' ),
			'view_item'                  => __( 'View Role', 'filtry' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'filtry' ),
			'add_or_remove_items'        => __( 'Add or remove items', 'filtry' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'filtry' ),
			'popular_items'              => __( 'Popular Roles', 'filtry' ),
			'search_items'               => __( 'Search Roles', 'filtry' ),
			'not_found'                  => __( 'Not Found', 'filtry' ),
			'no_terms'                   => __( 'No items', 'filtry' ),
			'items_list'                 => __( 'Roles list', 'filtry' ),
			'items_list_navigation'      => __( 'Roles list navigation', 'filtry' ),
		);
		$args   = array(
			'labels'            => $labels,
			'hierarchical'      => false,
			'public'            => true,
			'show_admin_column' => true,
			'show_tagcloud'     => false,
			'rewrite'           => array(
				'slug' => _x( 'role', 'iWorks Post Type Person SLUG', 'filtry' ),
			),
		);
		register_taxonomy( $this->taxonomy_name, array( $this->post_type_name ), $args );
	}

	public function filter_og_og_type_value( $value ) {
		if ( is_singular( $this->post_type_name ) ) {
			return 'profile';
		}
		return $value;
	}

}

