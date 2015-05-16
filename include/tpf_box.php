<?php

global $post;

$use_tpf = false;

if ( (bool) get_option( 'cl_tpf_' . $post->post_type ) ) {
	$use_tpf = true;
}

if ( (bool) get_post_meta( $post->ID, 'cl_tpf_disable', true ) ) {
	$use_tpf = false;
};
?>

	<style>
		#TB_window {
			height: auto !important;
		}

		#TB_ajaxContent {
			height: auto !important;
			width: auto !important;
			padding: 20px;
		}
	</style>

	<label>
		<input type="checkbox" name="cl_tpf_use" <?php echo checked( $use_tpf, true ) ?> />&nbsp;Использовать типограф
	</label>

	<br/>
	<br/>
	<div id="tpf-dialog" style="display:none;">
		<p>
			Скопируйте текст в поле ниже и нажмите кнопку "Оттипографить".
		</p>

		<textarea id="tpf-one" cols="30" rows="10" style="width: 100%;margin-bottom: 10px;" autofocus="autofocus"></textarea>
		<a href="#" class="button button-primary" style="float: right;" id="tpf-dialog-ok">Оттипографить</a>

	</div>

	<a href="#TB_inline?width=600&height=550&inlineId=tpf-dialog" class="thickbox" title="Типограф">Типограф</a>
<?php if ( user_can( get_current_user_id(), 'manage_options' ) ): ?>
	&nbsp;|&nbsp;<a href="<?php echo admin_url( 'options-general.php?page=cl-typograf.php' ) ?>">Настройки</a>
<?php endif; ?>