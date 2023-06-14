<?php
class iWorks_Table_Of_Content {
	private $tag        = 'h2';
	private $post_types = array(
		'post',
	);


	public function __construct() {
		add_filter( 'the_content', array( $this, 'filter_the_content_add' ) );
	}

	public function filter_the_content_add( $content ) {
		if ( ! in_array( get_post_type(), $this->post_types ) ) {
			return $content;
		}
		$re  = sprintf(
			'/<%1$s([^>]*)>(.+)<\/%1$s>/',
			$this->tag
		);
		$toc = '';
		if ( preg_match_all( $re, $content, $matches ) ) {
			$toc .= '<div class="iworks-toc">';
			$toc .= sprintf(
				'<p class="iworks-toc-title">%s</p>',
				esc_attr__( 'Table of content', 'seo-profi' )
			);
			$toc .= '<ol class="iworks-toc-list">';
			for ( $i = 0; $i < count( $matches[0] ); $i++ ) {
				$name = sprintf(
					'iworks-toc-%s-%d',
					crc32( $matches[2][ $i ] ),
					$i
				);
				if ( preg_match( '/id=[\'"]([^"^\]+)[\'"]/', $matches[1][ $i ], $match_id ) ) {
					$name = $match_id[1];
				} else {
					$pattern     = sprintf( '/%s/', preg_replace( '/\//', '\\/', $matches[0][ $i ] ) );
					$replacement = preg_replace(
						'/<' . $this->tag . '/',
						sprintf(
							'<%s id="%s"',
							$this->tag,
							$name
						),
						$matches[0][ $i ]
					);
					$content     = preg_replace( $pattern, $replacement, $content );
				}

				$toc .= sprintf(
					'<li class="iworks-toc-list-item"><a href="#%s">%s</a></li>',
					$name,
					$matches[2][ $i ]
				);

			}
			$toc .= '</ol>';
			$toc .= '</div>';
		}
		return $toc . $content;
	}
}
