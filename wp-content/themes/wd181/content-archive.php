<article class="post">

	<header class="post__header">
		<?php
		if ( is_single() ) {
			$tag  = 'h1';
			$attr = '';
		} else {
			$tag  = 'a';
			$attr = ' href="' . get_permalink() . '"';
		}


		echo '<' . $tag . $attr . ' class="post__thumb">';
		the_post_thumbnail();
		echo '</' . $tag . '>';
		echo '<' . $tag . $attr . '>' . get_the_title() . '</' . $tag . '>';
		?>
	</header>


</article>
