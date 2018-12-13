<?php
/*
Plugin Name: Publisher
Plugin URI: https://oiplug.com/plugin/
Description: --
Version: 1.0
Author: sh14ru
Author URI: https://oiplug.com/member/isaenko_alexei
License: A "Slug" license name e.g. GPL2
Text domain: publisher
Date: 13.12.18
*/

namespace publisher;

function post_form() {
	ob_start();
	include 'templates/form-post.php';
	$out = ob_get_contents();
	ob_clean();

	return $out;
}

add_shortcode( 'add_post', __NAMESPACE__ . '\post_form' );

function post_save() {
	if ( ! empty( $_POST['action'] ) ) {
		$data                    = $_POST;
		$data['_p']['post_type'] = 'post';
		$post_id                 = wp_insert_post( $data['_p'] );

		wp_set_post_categories( $post_id, $data['_t'] );
		wp_redirect( get_permalink( 87 ).'?id='.$post_id.'&event=update' );
	}
}

add_action( 'init', __NAMESPACE__ . '\post_save' );

// eof
