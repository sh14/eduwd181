<?php
/**
 * Date: 22/12/2018
 * @author Isaenko Alexey <info@oiplug.com>
 */

namespace publisher;

/**
 * регистрация настроек
 */
function register_settings() {
	// регистрация конкретной настройки
	register_setting( __NAMESPACE__, __NAMESPACE__ . 'editor_page' );
}

/**
 * Создание меню настроек в админке
 */
function create_menu() {

	// добавление нового пункта меню верхнего уровня
	add_menu_page(
		__( 'Публикатор', 'publisher' ),
		__( 'Публикатор', 'publisher' ),
		'administrator',
		__FILE__,
		__NAMESPACE__ . '\settings_page',
		'dashicons-admin-page'
	);

	// вызов функции регистрации настроек
	add_action( 'admin_init', __NAMESPACE__ . '\register_settings' );
}

// create custom plugin settings menu
add_action( 'admin_menu', __NAMESPACE__ . '\create_menu' );

/**
 * Описание внешнего вида страницы настроек в админке
 */
function settings_page() {
	?>
	<div class="wrap">

		<form method="post" action="options.php">
			<?php settings_fields( __NAMESPACE__ ); ?>
			<?php do_settings_sections( __NAMESPACE__ ); ?>
			<table class="form-table">

				<tr valign="top">
					<th scope="row"><?php _e( 'Страница публикации', __NAMESPACE__ ); ?></th>
					<td>
						<?php
						wp_dropdown_pages(
							array(
								'depth'    => 1,
								'selected' => esc_attr( get_option( __NAMESPACE__ . 'editor_page' ) ),
								'echo'     => 1,
								'name'     => __NAMESPACE__.'editor_page',
							)
						);
						?>
					</td>
				</tr>

			</table>

			<?php submit_button(); ?>

		</form>
	</div>
<?php }

// eof
