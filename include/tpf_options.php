<?php

$types = get_post_types( array( 'public' => true ), 'objects' );

?>
<div class="wrap">
	<h2>Типограф</h2>

	<form method="post" action="options.php">
		<?php settings_fields( 'cl_typograf' ); ?>
		<table class="form-table">
			<tbody>
			<tr>
				<th scope="row">
					Использовать типограф&nbsp;
					<a href="#" onclick="show_help('tab-link-cl_tpf_general_help');">
						<small>?</small>
					</a>
				</th>
				<td>
					<fieldset>
						<?php foreach ( $types as $type ): ?>
							<label for="cl_tpf_<?php echo $type->name; ?>">
								<input name="cl_tpf_<?php echo $type->name; ?>" type="checkbox" value="true"
								       id="cl_tpf_<?php echo $type->name; ?>"
									<?php echo checked( (bool) get_option( 'cl_tpf_' . $type->name ), true ) ?>>
								<?php echo $type->labels->name; ?>
							</label>
							<br/>
						<?php endforeach; ?>
					</fieldset>
					<p class="description">Укажите для каких типов записей будет использоваться типограф. <br/>
						Типограф применяется автоматически при сохранении записи. Корректирует поля: загаловок,
						описание, контент</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					Отключить автоматическое форматирование
					&nbsp;
					<a href="#" onclick="show_help('tab-link-cl_tpf_format_help');">
						<small>?</small>
					</a>

					<p class="description">Заменяет двойной перенос строки на параграфы. Функционал WordPress -
						wpautop</p>
				</th>
				<td>
					<fieldset>
						<label for="cl_autop_content">
							<input name="cl_autop_content" id="cl_autop_content" type="checkbox"
								<?php echo checked( get_option( 'cl_autop_content' ), 'on' ) ?> />
							Контент
						</label>
						<br/>
						<label for="cl_autop_excerpt">
							<input name="cl_autop_excerpt" id="cl_autop_excerpt" type="checkbox"
								<?php echo checked( get_option( 'cl_autop_excerpt' ), 'on' ) ?> />
							Описание
						</label>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row">
					Опции
					&nbsp;
					<a href="#" onclick="show_help('tab-link-cl_tpf_options_help');">
						<small>?</small>
					</a>
				</th>
				<td>
					<fieldset>
						<label for="cl_disable_mce">
							<input name="cl_disable_mce" id="cl_disable_mce" type="checkbox"
								<?php echo checked( get_option( 'cl_disable_mce' ), 'on' ) ?> />
							Отключить типограф для редактора
						</label>
					</fieldset>
				</td>
			</tr>
			</tbody>
		</table>
		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary"
			       value="<?php _e( 'Save Changes' ) ?>">
		</p>
	</form>
</div>

<script>
	function show_help(tab) {
		var expand_link = jQuery("#contextual-help-link");
		if (expand_link.attr('aria-expanded') == 'false') {
			expand_link.trigger('click');
		}
		jQuery(".contextual-help-tabs #" + tab + " a").trigger('click');
		return false;
	}
</script>
