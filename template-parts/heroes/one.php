<li class="<?php
echo esc_attr(
	implode(
		' ',
		get_post_class(
			sprintf(
				'position-h-%d position-v-%d position-%s',
				rand( 0, 5 ),
				rand( 0, 5 ),
				$args['join'] === $args['i'] ? 'join' : 'regular'
			)
		)
	)
);
$style = '';
if ( has_post_thumbnail() ) {
	$style = sprintf(
		'background-image:url(\'%s\')',
		get_the_post_thumbnail_url( get_the_ID(), 'full' )
	);
}
?>">
	<span class="iworks-heroes-thumbnail" style="<?php echo esc_attr( $style ); ?>"></span>
<?php the_title( '<span class="iworks-heroes-name">', '</span>' ); ?>
<br>
<span class="iworks-heroes-dates"><?php echo get_the_excerpt(); ?></span>
</li>
