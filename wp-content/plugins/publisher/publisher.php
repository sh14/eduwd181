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

require 'includes/option-page.php';

/**
 * Форма создания/редактирования публикации
 *
 * @return string
 */
function post_form() {

	ob_start();
	$author_id = 0;
	$user_id   = get_current_user_id();
	if ( ! empty( $_GET['id'] ) ) {
		$post_id   = $_GET['id'];
		$author_id = (array) get_post( $post_id );
		$author_id = $author_id['post_author'];
	}

	if ( current_user_can( 'edit_others_posts' ) || ( current_user_can( 'edit_posts' ) && ( $author_id == $user_id || 0 == $author_id ) ) ) {
		include 'templates/form-post.php';
	} else {
		include 'templates/access-denied.php';
	}

	$out = ob_get_contents();
	ob_clean();

	return $out;
}

add_shortcode( 'add_post', __NAMESPACE__ . '\post_form' );

function post_save() {

	// проверяем, передаются ли данные формы и производится проверка валидности этой формы
	if ( ! empty( $_POST['action'] ) && wp_verify_nonce( $_POST['form-post'], 'post_save' ) ) {
		$data                    = $_POST;
		$data['_p']['post_type'] = 'post';

		// сохранение публикации и полечение id данной публикации
		$post_id = wp_insert_post( $data['_p'] );

		// установка категории поста
		wp_set_post_categories( $post_id, $data['_t']['category'] );

		// преобразование тегов в массив
		$tags = $data['_t']['tag'];
		$tags = str_replace( '#', ',', $tags );
		$tags = str_replace( ' ', ',', $tags );
		$tags = explode( ',', $tags );
		$tags = array_filter( $tags, function ( $tag ) {
			return ! empty( $tag );
		} );

		// указание тегов для поста
		wp_set_post_tags( $post_id, $tags );

		// обновление мета-данных
		if ( ! empty( $data['_m'] ) ) {
			foreach ( $data['_m'] as $key => $value ) {

				$value = esc_attr( $value );
				if ( ! empty( $value ) ) {
					update_post_meta( $post_id, $key, $value );
				} /*else {
                    // удалять мета-данные следует только в крайних случаях, если вы уверены6 что это ни на что не влияет
					delete_post_meta( $post_id, $key );
				}*/
			}
		}

		wp_redirect( get_permalink( intval( get_option( __NAMESPACE__ . 'editor_page' ) ) ) . '?id=' . $post_id . '&event=update' );
		die();
	}
}

add_action( 'init', __NAMESPACE__ . '\post_save' );

// eof
