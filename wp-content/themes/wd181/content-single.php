<article>

	<header>
		<?php

		$thumb_id = get_post_thumbnail_id();

		// получение миниатюры записи указанного размера:
		// thumbnail, medium, large, full
/*		$thumb = get_the_post_thumbnail( $thumb_id, 'full' );
		print_r( $thumb_id );
		print_r( $thumb );*/
		the_post_thumbnail();
		//echo '<' . $tag . $attr . '>' . $thumb . '</' . $tag . '>';
		echo '<h1>' . get_the_title() . '</h1>';
		?>
	</header>

	<div class="content">
		<?php the_content(); ?>
	</div>


</article>

