<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>

<?php
$description = get_bloginfo( 'description', 'display' );
if ( $description ) : ?>
	<p class="site-description"><?php echo $description; ?></p>
<?php endif; ?>

<div id="content" class="site-content">
