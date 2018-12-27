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

function get_version() {
	return '1.0';
}

function get_plugin_path() {

	$plugin_path = plugin_dir_path( __FILE__ );
	$plugin_path = explode( '/', $plugin_path );
	$plugin_name = array_filter( $plugin_path, function ( $value ) {
		return ! empty( $value );
	} );
	$plugin_name = end( $plugin_name );

	return trailingslashit( plugins_url() ) . $plugin_name;
}


require 'includes/option-page.php';
require 'includes/ajax.php';

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

/**
 * Функция сохранения публикации
 *
 * @param $data
 *
 * @return array
 */
function post_save( $data ) {

	// проверяем, передаются ли данные формы и производится проверка валидности этой формы
	if ( ! empty( $data['action'] ) && wp_verify_nonce( $data['form-post'], 'post_save' ) ) {
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

		// если данные пришли по AJAX
		if ( ! empty( $data['ajax'] ) ) {

			// возвращаем id поста
			return array( 'post_id' => $post_id, );
		} else {

			// осуществляем редирект
			wp_redirect( get_permalink( intval( get_option( __NAMESPACE__ . 'editor_page' ) ) ) . '?id=' . $post_id . '&event=update' );
			die();
		}
	}

	return array();
}

/**
 * Функция осуществляет сохранение данных с последующим редиректом, если в браузере пользователя не работает JavaScript
 */
function classic_post_save() {
	if ( ! empty( $_POST ) && ! empty( $_POST['js_disabled'] ) ) {
		$data         = $_POST;
		$data['ajax'] = 0;
		post_save( $data );
	}
}

add_action( 'init', __NAMESPACE__ . '\classic_post_save' );


function enqueue_styles() {

	//wp_enqueue_style( 'my-plugin', trailingslashit( get_plugin_path() ) . 'style.css' );

	wp_enqueue_script( __NAMESPACE__, trailingslashit( get_plugin_path() ) . 'assets/js/functions.js', array(), get_version(), true );

	wp_localize_script( __NAMESPACE__, __NAMESPACE__, array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
	) );
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_styles' );


// eof
