<p><?php
	if ( is_user_logged_in() ) {
		_e( 'Для редактирования данной публикации необходимо быть ее автором.', 'publisher' );
	} else {
		echo sprintf( __( 'Для создания/редактирования публикации необходимо %s.', 'publisher' ), '<a href="' . wp_login_url() . '">' . __( 'авторизоваться', 'publisher' ) . '</a>' );
	}
	?></p>
