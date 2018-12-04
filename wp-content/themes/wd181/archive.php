<?php
$description = category_description();
get_header(); ?>

<div class="content-area">


	<div class="category">
		<div class="category__title">
			<?php echo single_cat_title(); ?>
		</div>
		<?php
		if ( ! empty( $description ) ) {
			?>
			<div class="category__description">
				<?php echo $description; ?>
			</div>
			<?php
		}
		?>
	</div>


	<?php


	if ( have_posts() ) { ?>

		<?php
		// Start the loop.
		while ( have_posts() ) {

			// "активация" публикации - все данные поста распределяются в глобальные переменные
			the_post();

			// подключение шаблона публикации
			get_template_part( 'content','archive' );

		}
	}
	?>

</div><!-- .content-area -->

<?php get_footer(); ?>
