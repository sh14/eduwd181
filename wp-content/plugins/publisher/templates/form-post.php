<?php

if ( ! empty( $_GET['id'] ) ) {
	$post_id = $_GET['id'];
	$post    = get_post( $post_id );

	$cat = wp_get_post_categories( $post->ID );

}

$post_status = array(
	array(
		'value'    => 'draft',
		'title'    => __( 'Черновик', 'publisher' ),
		'selected' => false,
	),
	array(
		'value'    => 'pending',
		'title'    => __( 'Ожидает проверки', 'publisher' ),
		'selected' => false,
	),
	array(
		'value'    => 'publish',
		'title'    => __( 'Опубликовано', 'publisher' ),
		'selected' => false,
	),
);

$options = '';
foreach ( $post_status as $i => $status ) {
	if ( $status['value'] == $post->post_status ) {
		$post_status[ $i ]['selected'] = true;

	}
	$selected = $post_status[ $i ]['selected'] ? ' selected' : '';
	$options  .= '<option value="' . $post_status[ $i ]['value'] . '" ' . $selected . '>' . $post_status[ $i ]['title'] . '</option>';
}

?>

	<form action="" class="form" method="post">
	<div class="form__group">
		<div class="form__label"><?php _e( 'Заголовок', 'publisher' ); ?></div>
		<?php wp_dropdown_categories( array(
			'name'         => '_t[category]',
			'show_count'   => 1,
			'hide_empty'   => 0,
			'echo'         => 1,
			'hierarchical' => 1,
			'orderby'      => 'slug',
			'order'        => 'ASC',
			'selected'     => $cat[0],
		) ); ?>
	</div>
	<div class="form__group">
		<div class="form__label"><?php _e( 'Заголовок', 'publisher' ); ?></div>
		<input type="text" class="form__control" name="_p[post_title]" value="<?php echo $post->post_title; ?>">
	</div>
	<div class="form__group">
		<div class="form__label"><?php _e( 'Заголовок', 'publisher' ); ?></div>
		<select type="text" class="form__control" name="_p[post_status]">
			<?php echo $options; ?>
		</select>
	</div>
	<div class="form__group">
		<div class="form__label"><?php _e( 'Контент', 'publisher' ); ?></div>
		<textarea class="form__control" name="_p[post_content]"><?php echo $post->post_content; ?></textarea>
		<button type="submit" class="form__button"><?php _e( 'Сохранить', 'publisher' ); ?></button>
		<input type="hidden" name="_p[ID]" value="<?php echo $post_id; ?>">
		<input type="hidden" name="action" value="post_save">
	</div>
<?php
if ( ! empty( $post_id ) ) {
	?>
	<a target="_blank" href="<?php echo home_url( '?p=' . $post_id . '&preview=true' ); ?>"><?php _e( 'Предпросмотр', 'publisher' ); ?></a>
	<?php
	}
	?>
	<div class="form form_info">
		<?php
		if ( ! empty( $_GET['event'] ) && 'update' == $_GET['event'] ) {
			_e( 'Публикация сохранена', 'publisher' );
		}
		?>
	</div>
</form>
