<article>

	<header>
		<?php

		$tag  = 'h1';
		$attr = '';


		echo '<h1>' . get_the_title() . '</h1>';
		?>
	</header>

	<div class="content">
		<?php

			the_content();

		?>
	</div>

</article>
