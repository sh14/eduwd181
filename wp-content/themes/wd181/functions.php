<?php
/**
 * Date: 29.11.18
 * @author Isaenko Alexey <info@oiplug.com>
 */

function get_version(){
	return '1.0.3';
}


function enque_my_styles() {

	wp_enqueue_style( 'eduwd181', get_stylesheet_uri(), array(), get_version() );

}

add_action( 'wp_enqueue_scripts', 'enque_my_styles' );


function theme_setup(){
	register_nav_menus( array(
		'top'    => __( 'Верхнее меню', 'wd181' ),
	) );


	add_theme_support( 'post-thumbnails' );

}

add_action( 'after_setup_theme', 'theme_setup' );



function theme_widgets_init() {
		register_sidebar( array(
		'name'          => __( 'Widget Area', 'wd181' ),
		'id'            => 'sidebar-1',
		'description'   => __( 'Add widgets here to appear in your sidebar.', 'wd181' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'theme_widgets_init' );

// eof
