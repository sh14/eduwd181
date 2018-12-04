<?php
get_header(); ?>

<div class="content-area">

		<?php if ( have_posts() ) : ?>

			<?php
			// Start the loop.
			while ( have_posts() ) {
				
				// "активация" публикации - все данные поста распределяются в глобальные переменные
				the_post();

				// подключение шаблона публикации
				get_template_part( 'content' );

			}

		endif;
		?>

</div><!-- .content-area -->

<?php get_footer(); ?>
