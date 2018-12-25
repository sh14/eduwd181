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
		),
	);

	return $clients;
}

function send_messages() {

	$clients = get_clients();


	foreach ( $clients as $i => $client ) {

		$pay_day = date( 'd', strtotime( '+1day' ) );
		if ( $pay_day == $client['pay_day'] ) {

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
		}
	}
}

add_filter( 'wp_mail', 'my_wp_mail_filter' );
function my_wp_mail_filter( $args ) {

	$new_wp_mail = array(
		'to'          => $args['to'],
		'subject'     => $args['subject'],
		'message'     => $args['message'],
		'headers'     => $args['headers'],
		'attachments' => $args['attachments'],
	);

	return $new_wp_mail;
}


// eof
