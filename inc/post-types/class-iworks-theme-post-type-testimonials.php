<?php

require_once dirname(__DIR__) . '/class-iworks-theme-base.php';

class iWorks_Theme_Post_Type_Testimonials extends iWorks_Theme_Base {

	private $post_type_name = 'iworks_testimonials';

	public function __construct() {
		parent::__construct();
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'init', array( $this, 'custom_post_type' ), 0 );
		add_action( 'save_post', array( $this, 'save' ) );
		add_shortcode( 'iworks_testimonials', array( $this, 'get_list' ) );
		add_filter( 'manage_' . $this->post_type_name . '_posts_columns', array( $this, 'add_admin_columns' ) );
		add_action( 'manage_' . $this->post_type_name . '_posts_custom_column', array( $this, 'display_admin_columns' ), 10, 2 );
	}

	/**
	 * Get post list
	 *
	 * @since 1.0.0
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
			'posts_per_page' => 3,
			'post_status'    => 'publish',
		);
		$testimonials_query = new WP_Query( $args );
		/**
		 * No data!
		 */
		if ( ! $testimonials_query->have_posts() ) {
			return $content;
		}
		/**
		 * Content
		 */
		$content .= '<ul class="iworks-testimonials">';
		while ( $testimonials_query->have_posts() ) {
			$testimonials_query->the_post();
			$content .= sprintf( '<li class="%s">', implode( ' ', get_post_class('iworks-testimonial') ) );
			$content .= '<div class="iworks-testimonial-content">';
			$content .= get_the_content();
			$content .= '</div>';
			$content .= '<div class="iworks-testimonial-footer">';
			$content .= sprintf( '<p class="iworks-testimonial-footer-person">%s</p>', get_post_meta( get_the_ID(), 'iworks_testimonial_person', true ) );
			$content .= sprintf( '<p class="iworks-testimonial-footer-position">%s</p>', get_post_meta( get_the_ID(), 'iworks_testimonial_position', true ) );
			$content .= '</div>';
			$content .= '</li>';
		}
		/* Restore original Post Data */
		wp_reset_postdata();
		$content .= '</ul>';
		return $content;
	}

	/**
	 * Add custom columns to admin list
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns Existing columns
	 * @return array Modified columns
	 */
	public function add_admin_columns( $columns ) {
		$columns['person']    = __( 'Person', 'reservante' );
		$columns['position']  = __( 'Position', 'reservante' );
		return $columns;
	}

	/**
	 * Display custom column content
	 *
	 * @since 1.0.0
	 *
	 * @param string $column Column name
	 * @param int    $post_id Post ID
	 */
	public function display_admin_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'person':
				$person = get_post_meta( $post_id, 'iworks_testimonial_person', true );
				echo $person ? esc_html( $person ) : '&mdash;';
				break;
			case 'position':
				$position = get_post_meta( $post_id, 'iworks_testimonial_position', true );
				echo $position ? esc_html( $position ) : '&mdash;';
				break;
		}
	}

	/**
	 * Register Custom Post Type
	 *
	 * @since 1.0.0
	 */
	public function custom_post_type() {
		$labels = array(
			'name'                  => _x( 'Testimonials', 'Post Type General Name', 'reservante' ),
			'singular_name'         => _x( 'Testimonial', 'Post Type Singular Name', 'reservante' ),
			'menu_name'             => __( 'Testimonials', 'reservante' ),
			'name_admin_bar'        => __( 'Testimonials', 'reservante' ),
			'archives'              => __( 'Testimonials', 'reservante' ),
			'all_items'             => __( 'Testimonials', 'reservante' ),
			'add_new_item'          => __( 'Add New Testimonial', 'reservante' ),
			'add_new'               => __( 'Add New', 'reservante' ),
			'new_item'              => __( 'New Testimonial', 'reservante' ),
			'edit_item'             => __( 'Edit Testimonial', 'reservante' ),
			'update_item'           => __( 'Update Testimonial', 'reservante' ),
			'view_item'             => __( 'View Testimonial', 'reservante' ),
			'view_items'            => __( 'View Testimonials', 'reservante' ),
			'search_items'          => __( 'Search Testimonials', 'reservante' ),
			'not_found'             => __( 'Not found', 'reservante' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'reservante' ),
			'items_list'            => __( 'Testimonials list', 'reservante' ),
			'items_list_navigation' => __( 'Testimonials list navigation', 'reservante' ),
			'filter_items_list'     => __( 'Filter items list', 'reservante' ),
		);
		$args   = array(
			'can_export'          => true,
			'capability_type'     => 'page',
			'description'         => __( 'Testimonial', 'reservante' ),
			'exclude_from_search' => true,
			'has_archive'         => false,
			'hierarchical'        => false,
			'label'               => __( 'Testimonials', 'reservante' ),
			'labels'              => $labels,
			'menu_icon'           => 'dashicons-businessperson',
			'public'              => false,
			'show_in_admin_bar'   => false,
			'show_in_menu'        => apply_filters( 'storm_post_type_show_in_menu' . $this->post_type_name, 'edit.php' ),
			'show_in_nav_menus'   => false,
			'show_ui'             => true,
			'show_in_rest'        => true,
			'supports'            => array( 'title', 'editor', ),
		);
		register_post_type( $this->post_type_name, $args );
	}

	/**
	 * Add meta boxes
	 *
	 * @since 2.0.6
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'iworks-testimonials-data',
			__( 'Testimonial data', 'reservante' ),
			array( $this, 'html_data' ),
			$this->post_type_name,
			'side',
			'default'
		);
	}

	/**
	 * HTML for metabox
	 *
	 * @since 2.0.6
	 */
	public function html_data( $post ) {
		wp_nonce_field( __CLASS__, '_testimonials_nonce' );
		?>
<p>
	<label><?php esc_attr_e( 'Person', 'reservante' ); ?><br />
		<?php $value = get_post_meta( $post->ID, 'iworks_testimonial_person', true ); ?>
		<input type="text" class="large-text" value="<?php echo esc_attr( $value ); ?>" name="iworks_testimonial_person" />
	</label>
</p>
<p>
	<label><?php esc_attr_e( 'Position', 'reservante' ); ?><br />
		<?php $value = get_post_meta( $post->ID, 'iworks_testimonial_position', true ); ?>
		<input type="text" class="large-text" value="<?php echo esc_attr( $value ); ?>" name="iworks_testimonial_position" />
	</label>
</p>
		<?php
	}
	/**
	 * Save Testimonial data.
	 *
	 * @since 2.0.6
	 *
	 * @param integer $post_id Post ID.
	 */
	public function save( $post_ID ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		$nonce = filter_input( INPUT_POST, '_testimonials_nonce' );
		if ( ! wp_verify_nonce( $nonce, __CLASS__ ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_ID ) ) {
			return;
		}
		$this->update_meta( $post_ID, 'iworks_testimonial_person', filter_input( INPUT_POST, 'iworks_testimonial_person' ) );
		$this->update_meta( $post_ID, 'iworks_testimonial_position', filter_input( INPUT_POST, 'iworks_testimonial_position' ) );

	}

}

