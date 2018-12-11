<?php
/*
Plugin Name: My plugin
Plugin URI: https://oiplug.com/plugin/
Description: --
Version: 1.0.0
Author: sh14ru
Author URI: https://oiplug.com/member/isaenko_alexei
License: A "Slug" license name e.g. GPL2
Date: 06.12.18
*/

function get_plugin_path() {

	$plugin_path = plugin_dir_path( __FILE__ );
	$plugin_path = explode( '/', $plugin_path );
	$plugin_name = array_filter( $plugin_path, function ( $value ) {
		return ! empty( $value );
	} );
	$plugin_name = end( $plugin_name );

	return trailingslashit( plugins_url() ) . $plugin_name;
}


function get_yandex_video( $atts, $content ) {

	if ( empty( $atts['url'] ) ) {
		return '';
	}

	// если какие-либо данные не указаны, они заменяются на стандартные значения
	$atts = wp_parse_args( $atts, array(
		'url'    => '',
		'width'  => 'auto',
		'height' => 'auto',
	) );

	// получение данных с удаленного адреса
	$data = wp_remote_get( $atts['url'] );

	// получение тела страницы
	$data = $data['body'];

	preg_match_all( '/id="store-prefetch">(.*?)<\/script/si', $data, $matches );
	$data = $matches[1][0];
	$data = json_decode( $data );
	$data = (array) $data->resources;
	$data = array_values( $data );
	$name = $data[0]->name;
	$data = $data[0]->videoStreams->videos;

	$videos = array();
	$ratio  = 0;

	// перебираем список видео, чтобы сформировать удобный массив
	foreach ( $data as $video ) {

		// если разрешение ролика не адаптивное
		if ( 'adaptive' != $video->dimension ) {

			// если отношение сторон ролика не определено
			if ( empty( $ratio ) ) {
				$ratio = $video->size->width / $video->size->height;
			}

			// формируем массив
			$videos[] = array(
				'url'       => $video->url,
				'width'     => $video->size->width,
				'height'    => $video->size->height,
				'dimension' => $video->dimension,
			);
		} else {
			if ( ! empty( $ratio ) ) {

				$width    = 1440;
				$height   = round( $width / $ratio );
				$videos[] = array(
					'url'       => $video->url,
					'width'     => $width,
					'height'    => $height,
					'dimension' => '720p',
				);
			}
		}
	}

	$video = end( $videos );

	$video = '<video width="' . $video['width'] . '" height="' . $video['height'] . '" controls>'
	         . '<source src="' . $video['url'] . '" '
	         .'type="video/mp4" '
	         .'type="application/x-mpegURL"'
	         .'>'
	         . 'Your browser does not support the video tag.'
	         . '</video>';

	$style = '';
	$sizes = array( 'width', 'height' );

	// перебор названий размеров
	foreach ( $sizes as $size ) {

		// если размер указан
		if ( ! empty( $atts[ $size ] ) ) {

			// если размер является числом
			if ( is_numeric( $atts[ $size ] ) ) {
				$mesure = 'px';

				// преобразуем размер в целое число
				$atts[ $size ] = intval( $atts[ $size ] );
			} else {
				$mesure = '';
			}

			// дописываем стили
			$style .= $size . ':' . $atts[ $size ] . $mesure . ';';
		}
	}


	$style   = ' style="' . $style . '"';
	$content = ! empty( $content ) ? '<div class="video-player__description">' . $content . '</div>' : '';
	$video   = '<div class="video-player"' . $style . '>'
	           . $content
	           . $video
	           . '</div>';

	return $video;
}

add_shortcode( 'yandex_video', 'get_yandex_video' );

function enqueue_styles() {

	wp_enqueue_style( 'my-plugin', trailingslashit( get_plugin_path() ) . 'style.css' );

}

add_action( 'wp_enqueue_scripts', 'enqueue_styles' );


function content_addon( $content ) {
	return $content . '<div>СЛАВА КПСС!</div>';
}

add_filter( 'the_content', 'content_addon' );


// eof
