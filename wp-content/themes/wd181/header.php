<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>


<div class="header">
	<div class="header__brand">
		<a class="header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>"
		   rel="home"><?php bloginfo( 'name' ); ?></a>
		<?php
		$description = get_bloginfo( 'description', 'display' );
		if ( $description ) {
			?>
			<div class="header__description">
				<?php echo $description; ?>
			</div>
			<?php
		}
		?>
	</div>
	<div class="header__menu"></div>
</div>


<div id="content" class="site-content">
