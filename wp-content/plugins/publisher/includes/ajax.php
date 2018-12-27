<?php
/**
 * Date: 27/12/2018
 * @author Isaenko Alexey <info@oiplug.com>
 */

namespace publisher;

/**
 * запуск функции, к которой обратились через ajax запрос
 */
function ajax_post_save() {
	if ( ! empty( $_POST ) ) {
		$data         = $_POST;

		// установка флага, который сообщает функции сохранения, что надо вернуть данные, а не делать редирект
		$data['ajax'] = 1;

		// вызов функции сохранения
		$result       = post_save( $data );

		// возврат данных клиенту
		if ( ! empty( $result['errors'] ) ) {
			wp_send_json_error( $result );
		} else {
			wp_send_json_success( $result );
		}
	}
}
//'wp_ajax_publisher_ajax_post_save'
add_action('wp_ajax_'.__NAMESPACE__.'_ajax_'.'post_save',__NAMESPACE__.'\ajax_post_save');

// устанавливаем хук, который запустит функцию даже в том случае, если у пользователя не достаточно прав
add_action('wp_ajax_nopriv_'.__NAMESPACE__.'_ajax_'.'post_save',__NAMESPACE__.'\ajax_post_save');

// eof
