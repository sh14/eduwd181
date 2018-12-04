<?php
get_header(); ?>

<div class="page">
	<div class="page__content-area">

		<?php if ( have_posts() ) : ?>

			<?php
			// Start the loop.
			while ( have_posts() ) {

				// "активация" публикации - все данные поста распределяются в глобальные переменные
				the_post();

				// подключение шаблона публикации
				get_template_part( 'content', 'page' );

			}

		endif;
		?>

	</div><!-- .content-area -->

	<div class="page__sidebar">
		<?php get_sidebar(); ?>
	</div>
</div>

<?php get_footer(); ?>
