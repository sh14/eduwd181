<?php
/**
 * Plugin Name: Rassilatel
 * Plugin URI: https://oiplug.com/plugin/
 * Description: --
 * Version: 1.0
 * Author: sh14ru
 * Author URI: https://oiplug.com/member/isaenko_alexei
 * License: A "Slug" license name e.g. GPL2
 * Text domain: rassilatel
 * Date: 25/12/2018
 * @author Isaenko Alexey <info@oiplug.com>
 */

namespace rassilatel;
date_default_timezone_set( 'Europe/Moscow' );

/**
 * Создаем задачу, которая будет выполняться один раз в час и указываем хук, к которому нужно цепляться, чтобы
 * выполнять какие-то задачи раз в час.
 */
function my_activation() {
	if ( ! wp_next_scheduled( 'my_hourly_event' ) ) {
		wp_schedule_event( time(), 'hourly', 'my_hourly_event' );
	}
}

register_activation_hook( __FILE__, 'my_activation' );

/**
 * Функция удаления хука по которому запускаются какие-либо функции, созданные в этом плагине.
 */
function my_deactivation() {
	wp_clear_scheduled_hook('my_hourly_event');
}

register_deactivation_hook(__FILE__, 'my_deactivation');


/**
 * Функция-заглушка для отправки смсок
 *
 * @param $phone
 * @param $message
 *
 * @return bool
 */
function send_sms( $phone, $message ) {
	return true;
}


function get_options( $key ) {
	$options = array(
		'card_number' => '1234 4321 5678 9876',
		'price'       => 500,
	);
	if ( ! empty( $key ) ) {
		return $options[ $key ];
	}

	return $options;
}


function get_clients() {
	$clients = array(
		'remont_odejdi' => array(
			'contact'      => array(
				'name'    => 'Иван',
				'surname' => 'Иванович',
				'family'  => 'Петров',
			),
			'pay_day'      => '15',
			'address'      => 'ул.Бирюлевская, 12',
			'phone_number' => '79031234567',
			'email'        => 'remont@test.ru',
			'destination'  => 'sms',
			'user_id'      => 2,
		),
		'magazin_piva'  => array(
			'contact'      => array(
				'name'    => 'Семен',
				'surname' => 'Карлович',
			),
			'pay_day'      => '2',
			'address'      => 'ул.Бирюлевская, 18',
			'phone_number' => '79039876543',
			'email'        => 'pivo@test.ru',
			'destination'  => 'email',
			'user_id'      => 3,
		),
	);

	return $clients;
}

function send_messages() {

	$clients = get_clients();


	foreach ( $clients as $i => $client ) {

		$pay_day   = date( 'd', strtotime( '+1day' ) );
		$today     = date( 'Y-m-d' );
		$sent_date = get_user_meta( $client['user_id'], 'last_message', true );

		// если сегодня день, предшествующий дню оплаты и сегодняшняя дата не совпадает с датой последней отправки и сейчас больше 8 утра
		if ( $pay_day == $client['pay_day'] && $sent_date != $today && date( 'H' ) > 8 ) {

			$message = array();
			$name    = $client['contact']['name'];
			if ( ! empty( $client['contact']['surname'] ) ) {
				$name .= ' ' . $client['contact']['surname'];
			}

			$date      = date( 'd.m.Y', strtotime( '+1day' ) );
			$message[] = sprintf( 'Уважаемый %s!', $name );
			$message[] = sprintf( 'Завтра, %s необходимо оплатить услугу "Сайт", переведя на карту с номером %s сумму, равную %sр.', $date, get_options( 'card_number' ), get_options( 'price' ) );
			$message[] = sprintf( 'Спасибо за своевременную оплату!', $name );
			$message   = implode( "\n", $message );

			switch ( $client['destination'] ) {
				case 'email':
					wp_mail( $client['email'], 'Близится срок оплаты сайта', $message );
					break;
				case 'sms':
					send_sms( $client['phone'], $message );
					break;
			}

			update_user_meta( $client['user_id'], 'last_message', $today );
		}
	}
}

add_action( 'my_hourly_event', __NAMESPACE__ . '\send_messages' );


// для того, чтобы запустить серверный крон мы указываем:
//  - Частоту обращения
//  - путь к PHP
//  - путь к файлу, который нужно запустить
//  - условие запуска
//
//При этом необходимо отключит вордпресовский крон,
// добавив в конфиг строку:
//define('DISABLE_WP_CRON', true);
//
//Задача для серверного крона на запуск файла WP,
// отвечающего за запуск задач:
// * * * * * /usr/bin/php7.2 /var/www/bigdealstore.ru/public_html/html/wp-cron.php >/dev/null 2>&1
// для постановки задачи на сервере надо выполнить команду: crontab -e

// eof
