<?php

global $post;

$use_tpf = false;

if ( (bool) get_option( 'cl_tpf_' . $post->post_type ) ) {
	$use_tpf = true;
}

if ( (bool) get_post_meta( $post->ID, 'cl_tpf_disable', true ) ) {
	$use_tpf = false;
}


?>

	<label>
		<input type="checkbox" name="cl_tpf_use" <?php echo checked( $use_tpf, true ) ?> />&nbsp;Использовать типограф
	</label>

<?php if ( user_can( get_current_user_id(), 'manage_options' ) ): ?>

	<br/>
	<br/>
	<a href="<?php echo admin_url( 'options-general.php?page=cl-typograf.php' ) ?>">Настройки</a>

<?php endif; ?>