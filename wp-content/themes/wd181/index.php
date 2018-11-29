<?php
get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">

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

	</main><!-- .site-main -->
</div><!-- .content-area -->

<?php get_footer(); ?>
