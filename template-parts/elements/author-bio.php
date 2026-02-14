<?php
$img                   = get_avatar_url( get_the_author_meta( 'ID' ), array( 'size' => 96 ) );
$attachment_upload_url = get_the_author_meta( 'cupp_upload_meta' );
if ( ! empty( $attachment_upload_url ) ) {
	$img           = $attachment_upload_url;
	$attachment_id = attachment_url_to_postid( $attachment_upload_url );
	if ( $attachment_id ) {
		$image_thumb = wp_get_attachment_image_src( $attachment_id, array( 500, 500 ) );
		if ( is_array( $image_thumb ) ) {
			$img = $image_thumb[0];
		}
	}
}
?>
<aside class="reservante-box reservante-box-bio" itemscope itemtype="https://schema.org/Person">
	<div class="reservante-box-bio-photo-wrapper">
		<img class="reservante-box-bio-photo" itemprop="image" width="96" height="96" src="<?php echo esc_url( $img ); ?>" loading="lazy" alt="<?php echo esc_attr( get_the_author_meta( 'display_name' ) ); ?>">
	</div>
	<div class="reservante-box-bio-text-wrapper">
		<p class="reservante-box-bio-text-title"><?php esc_html_e( 'Author of the article:', 'reservante' ); ?></p>
		<p class="reservante-box-bio-text-name" itemprop="name"><?php echo get_the_author_meta( 'display_name' ); ?></p>
		<p class="reservante-box-bio-text-description" itemprop="disambiguatingDescription"><?php echo get_the_author_meta( 'description' ); ?></p>
	</div>
</aside>
