<?php
/*
Plugin Name: Site Tree
Plugin URI: https://oiplug.com/plugin/
Description: --
Version: 1.0.0
Author: sh14ru
Author URI: https://oiplug.com/member/isaenko_alexei
License: A "Slug" license name e.g. GPL2
Date: 06.12.18
*/

/**
 * Date: 11.12.18
 * @author Isaenko Alexey <info@oiplug.com>
 */

namespace sitetree;

function get_pages( $atts ) {
	global $post;

	$atts = wp_parse_args( $atts, array(
		'object' => true,
	) );

	// данные запроса, для получения постов
	$query = array(
		'post_type'      => 'page',
		'post_status'    => 'publish',
		'posts_per_page' => - 1,
		'order'          => 'ASC',
		'orderby'        => 'ID',
	);

	// получение постов в соответствии с запросом
	$query = new \WP_Query( $query );

	// определение переменной, которая будет содержать необходимые данные полученных постов
	$out   = array();

	// если значение ключа object не равно true
	if ( true != $atts['object'] ) {
		echo '<h3>Вывод данных с использованием свойств</h3>';

		// преобразование объекта в индексированный массив
		$query = (array) $query;

		// перебор постов
		foreach ( $query['posts'] as $i => $post ) {

			// загрузка данных конкретного поста в глобальную переменную $post
			setup_postdata( $post );

			// формирование массива из необходимых нам данных
			$out[ $i ] = array(
				'post_id'     => $post->ID,
				'post_parent' => $post->post_parent,
				'post_title'  => $post->post_title,
				'menu_order'  => $post->menu_order,
			);

			// получение и добавление в массив указанных мета-данных поста
			$out[ $i ]['age'] = get_post_meta( $post->ID, 'age', true );
		}
	} else {
		echo '<h3>Вывод данных с использованием функций</h3>';

		// определение индекса
		$i = 0;

		// пока в выдаче есть не пустой пост
		while ( $query->have_posts() ) {

			// делаем текущий пост "активным"
			$query->the_post();

			// формирование массива из необходимых нам данных
			$out[ $i ] = array(
				// получение id поста
				'post_id'     => get_the_ID(),

				// получение id родительского поста
				'post_parent' => wp_get_post_parent_id( get_the_ID() ),
				// получение заголовка
				'post_title'  => get_the_title(),
				// получение значения указанного поля поста
				'menu_order'  => get_post_field( 'menu_order', get_the_ID() ),
			);

			// получение и добавление в массив указанных мета-данных поста
			$out[ $i ]['age'] = get_post_meta( get_the_ID(), 'age', true );
			// инкремент индекса
			$i ++;
		}
	}


	// функция сброса query
	wp_reset_query();

	echo '<pre>';
	print_r( $out );
	echo '</pre>';
}

add_shortcode( 'get_pages', __NAMESPACE__ . '\get_pages' );

// eof
