<article>

	<header>
		<?php
		if ( is_single() ) {
			$tag  = 'h1';
			$attr = '';
		} else {
			$tag  = 'a';
			$attr = ' href="' . get_permalink() . '"';
		}


		echo '<' . $tag . $attr . '>' . get_the_title() . '</' . $tag . '>';
		?>
	</header>

	<div class="content">
		<?php
		if ( is_archive() ) {
			the_excerpt();
		} else {
			the_content();
		}
		?>
	</div>

</article>
