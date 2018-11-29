<?php
/**
 * Date: 29.11.18
 * @author Isaenko Alexey <info@oiplug.com>
 */

function enque_my_styles() {

	wp_enqueue_style( 'eduwd181', get_stylesheet_uri(), array(), '1.1' );

}

add_action( 'wp_enqueue_scripts', 'enque_my_styles' );


// eof
