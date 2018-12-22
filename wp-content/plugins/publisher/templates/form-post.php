<?php
echo 'editor_page<pre>';
print_r( esc_attr( get_option( 'editor_page' ) ) );
echo '</pre>';

if ( ! empty( $_GET['id'] ) ) {
	$post_id = $_GET['id'];
	$post    = (array) get_post( $post_id );
		echo '<pre>';
		print_r( $post['post_author'] );
		echo '</pre>';
	$cat  = wp_get_post_categories( $post_id );
	$tags = get_the_tag_list( '#', ' #', '', $post_id );

	$tags = strip_tags( $tags );

	$meta = get_post_meta( $post_id );
	$meta = array_map(function ($val){
		return $val[0];
	},$meta);

} else {
	$post = array(
		'ID'           => 0,
		'post_author'  => 0,
		'post_date'    => '',
		'post_content' => '',
		'post_title'   => '',
		'post_excerpt' => '',
		'post_status'  => '',
		'post_parent'  => 0,
	);
	$cat  = array( 0 );
	$tags = '';
	$meta = '';
}
$post['post_type'] = 'post';

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
	if ( $status['value'] == $post['post_status'] ) {
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
		<input type="text" class="form__control" name="_p[post_title]" value="<?php echo $post['post_title']; ?>">
	</div>
	<div class="form__group">
		<div class="form__label"><?php _e( 'Заголовок', 'publisher' ); ?></div>
		<select type="text" class="form__control" name="_p[post_status]">
			<?php echo $options; ?>
		</select>
	</div>
	<div class="form__group">
		<div class="form__label"><?php _e( 'Контент', 'publisher' ); ?></div>
		<textarea class="form__control" name="_p[post_content]"><?php echo $post['post_content'] ?></textarea>
	</div>
	<div class="form__group">
		<div class="form__label"><?php _e( 'Теги', 'publisher' ); ?></div>
		<input type="text" class="form__control" name="_t[tag]" value="<?php echo $tags; ?>"/>

	</div>
	<div class="form__group">
		<div class="form__label"><?php _e( 'Рейтинг', 'publisher' ); ?></div>
		<input
				type="number"
				class="form__control"
				min="0"
				max="5"
				step="1"
				name="_m[rating]"
				value="<?php echo $meta['rating']; ?>"
		/>

	</div>
	<div class="form__group">
		<div class="form__label"><?php _e( 'Вкусно', 'publisher' ); ?></div>
		<input
				type="number"
				class="form__control"
				min="0"
				max="5"
				step="1"
				name="_m[taste]"
				value="<?php echo $meta['taste']; ?>"
		/>

	</div>

	<button type="submit" class="form__button"><?php _e( 'Сохранить', 'publisher' ); ?></button>
	<input type="hidden" name="_p[ID]" value="<?php echo $post_id; ?>">
	<input type="hidden" name="action" value="post_save">
	<?php wp_nonce_field( 'post_save', 'form-post' ); ?>

	<?php
	if ( ! empty( $post_id ) ) {
		?>
		<a target="_blank"
		   href="<?php echo home_url( '?p=' . $post_id . '&preview=true' ); ?>"><?php _e( 'Предпросмотр', 'publisher' ); ?></a>
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
