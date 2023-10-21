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
?>">
	<span class="iworks-heroes-thumbnail" style="background-image:url(<?php echo get_the_post_thumbnail_url( get_the_ID(), 'full' ); ?>)"></span>
<?php the_title( '<span class="iworks-heroes-name">', '</span>' ); ?>
<br>
<span class="iworks-heroes-dates"><?php the_excerpt(); ?></span>
</li>
