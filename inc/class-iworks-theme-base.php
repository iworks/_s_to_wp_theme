<?php

abstract class iWorks_Theme_Base {

	/**
	 * Theme url.
	 *
	 * @since 1.0.0
	 * @var string $url Base url for theme files..
	 */
	protected $url = '';

	/**
	 * Option name, used to save data on postmeta table.
	 *
	 * @since 1.0.0
	 * @var string $version Theme version.
	 */
	protected $version = 'THEME_VERSION';

	/**
	 * Debug
	 *
	 * @since 1.0.0
	 * @var boolean $debug
	 */
	protected $debug = 'false';

	/**
	 * media
	 *
	 */
	protected $option_name_media = '_iworks_media';

	/**
	 * Nounce value
	 */
	protected $nonce_value = '4PufQi59LMAEnB1yp3r4m6y9x49RbIUy';

	protected function __construct() {
		$child_version = wp_get_theme();
		$this->version = $child_version->Version;
		$this->url     = get_stylesheet_directory_uri();
		$this->debug   = apply_filters( 'iworks_debug_theme', defined( 'WP_DEBUG' ) && WP_DEBUG );
	}

	/**
	 * Check is login page
	 */
	protected function is_wp_login() {
		$ABSPATH_MY = str_replace( array( '\\', '/' ), DIRECTORY_SEPARATOR, ABSPATH );
		return (
			(
				in_array( $ABSPATH_MY . 'wp-login.php', get_included_files() )
				|| in_array( $ABSPATH_MY . 'wp-register.php', get_included_files() )
			) || (
				isset( $_GLOBALS['pagenow'] )
				&& $GLOBALS['pagenow'] === 'wp-login.php'
			)
				|| $_SERVER['PHP_SELF'] == '/wp-login.php'
		);
	}

	/**
	 * Get assets URL
	 *
	 * @since 1.0.0
	 *
	 * @param string $file File name.
	 * @param string $group Group, default "images".
	 * @param boolean $add_version Add theme version, default "false", but always true for images.
	 *
	 * @return string URL into asset.
	 */
	protected function get_asset_url( $file, $group = 'images', $add_version = false ) {
		$url = sprintf(
			'%s/assets/%s/%s',
			$this->url,
			$group,
			$file
		);
		/**
		 * add version
		 */
		if (
			$add_version
			|| 'images' === $group
		) {
			$url = add_query_arg(
				array(
					'ver' => $this->version,
				),
				$url
			);
		}
		return esc_url( $url );
	}

	/**
	 * Media files
	 *
	 * @since 1.0.0
	 */
	public function html_media( $post ) {
		wp_enqueue_media();
		wp_nonce_field( $this->nonce_value, '_iworks_media_nonce' );
		$rows_name = 'iworks-media-file-rows';
		$classes   = array(
			'iworks-media-container',
			'image-wrapper',
			empty( $src ) ? '' : ( 0 < $value ? ' has-file' : ' has-old-file' ),
		);
		printf(
			'<div data-rows="%s" class="%s">',
			esc_attr( $rows_name ),
			esc_attr( implode( ' ', $classes ) )
		);
		echo '<p>';
		printf(
			'<button type="button" class="button button-add-file">%s</button>',
			esc_html__( 'Add file', 'THEME_SLUG' )
		);
		echo '</p>';
		printf(
			'<div class="%s" aria-hidden="true">',
			esc_attr( $rows_name )
		);
		$value = get_post_meta( $post->ID, $this->option_name_media, true );
		if ( is_array( $value ) ) {
			foreach ( $value as $attachment_ID ) {
				$this->media_row( $this->get_attachment_data( $attachment_ID ) );
			}
		}
		echo '</div>';
		echo '</div>';
		echo '<script type="text/html" id="tmpl-iworks-media-file-row">';
		$this->media_row();
		echo '</script>';
	}

	/**
	 * Media row helper
	 *
	 * @since 1.0.0
	 */
	protected function media_row( $data = array() ) {
		$data = wp_parse_args(
			$data,
			array(
				'id'      => '{{{data.id}}}',
				'type'    => '{{{data.type}}}',
				'subtype' => '{{{data.subtype}}}',
				'url'     => '{{{data.url}}}',
				'icon'    => '{{{data.icon}}}',
				'caption' => '{{{data.caption}}}',
			)
		);
		?>
	<div class="iworks-media-file-row">
		<span class="dashicons dashicons-move"></span>
		<span class="icon iworks-media-<?php echo esc_attr( $data['type'] ); ?>-<?php echo esc_attr( $data['subtype'] ); ?>" style="background-image:url(<?php echo esc_attr( $data['icon'] ); ?>"></span>
		<span><a href="<?php echo esc_attr( $data['url'] ); ?>" target="_blank"><?php echo esc_html( $data['url'] ); ?></a><br /><small><?php echo esc_html( $data['caption'] ); ?></small></span>
		<button type="button" aria-label="<?php esc_attr_e( 'Remove file', 'THEME_SLUG' ); ?>"><span class="dashicons dashicons-trash"></span></button>
		<input type="hidden" name="<?php echo esc_attr( $this->option_name_media ); ?>[]" value="<?php echo esc_attr( $data['id'] ); ?>" />
	</div>
		<?php
	}

	/**
	 * Add post meta
	 */
	protected function update_meta( $post_id, $option_name, $option_value ) {
		if ( empty( $option_value ) ) {
			delete_post_meta( $post_id, $option_name );
			return;
		}
		$result = add_post_meta( $post_id, $option_name, $option_value, true );
		if ( ! $result ) {
			update_post_meta( $post_id, $option_name, $option_value );
		}
	}

	private function get_attachment_data( $attachment_ID ) {
		$content_type = explode( '/', get_post_mime_type( $attachment_ID ) );
		if ( ! is_array( $content_type ) ) {
			$content_type = array(
				'unknown',
				'unknown',
			);
		}
		return         array(
			'id'      => $attachment_ID,
			'caption' => wp_get_attachment_caption( $attachment_ID ),
			'url'     => wp_get_attachment_url( $attachment_ID ),
			'type'    => $content_type[0],
			'subtype' => isset( $content_type[1] ) ? $content_type[1] : '',
			'icon'    => wp_get_attachment_image_src( $attachment_ID, 'thumbnail', true )[0],
		);
	}

	/**
	 * Check is REST API request handler
	 */
	protected function is_rest_request() {
		return defined( 'REST_REQUEST' ) && REST_REQUEST;
	}

	protected function get_post_meta_name( $name ) {
		return sprintf(
			'%s_%s',
			$this->post_meta_prefix,
			esc_attr( $name )
		);
	}
}

