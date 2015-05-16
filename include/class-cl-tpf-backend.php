<?php

require __DIR__ . '/../vendor/remotetypograf.php';

class Cl_Tpf_Backend {

	var $mce_version = '20080121';

	/**
	 * Init actions
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'option_menu' ) );
		add_action( 'admin_init', array( $this, 'options_init' ) );
		add_action( 'save_post', array( $this, 'save_post_process' ) );

		add_action( 'wp_ajax_cl-tpf', array( $this, 'tpf_text' ) );

		add_filter( 'tiny_mce_before_init', array( $this, 'tiny_mce_before_init' ) );
		add_filter( 'print_scripts_array', array( $this, 'rewrite_default_script' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'admin_print_footer_scripts', array( $this, 'appthemes_add_quicktags' ) );
	}

	function appthemes_add_quicktags() {
		if ( wp_script_is( 'quicktags' ) ) {
			?>
			<script type="text/javascript">
				initQtag();
			</script>
		<?php
		}
	}

	/**
	 * Add option page in options section
	 */
	function option_menu() {
		$tpf_page = add_options_page( 'Типограф', 'Типограф', 'manage_options', 'cl-typograf.php', function () {
			include 'tpf_options.php';
		} );

		add_action( 'load-' . $tpf_page, function () {
			$screen = get_current_screen();

			$screen->add_help_tab( array(
				'id'      => 'cl_tpf_general_help',
				'title'   => 'Типограф',
				'content' => '<p>Укажите галками в каких типах записей применять типограф.</p>' .
				             '<p>Типограф применяется автоматически при сохранениии записи.</p>' .
				             '<p>В редактировании отдельной записи сожно отключить использование типографа.</p>',
			) );

			$screen->add_help_tab( array(
				'id'      => 'cl_tpf_format_help',
				'title'   => 'Форматирование',
				'content' => '<p>При сохранениии и при выводе контента WordPress пытается автоматически расставить параграфы и переносы строк.</p>' .
				             '<p>Если это нарушает оформление можно отключить эту возможность.</p>' .
				             '<p>Галками указываются поля для которых эта возможность будет отключена.</p>',
			) );

			$screen->add_help_tab( array(
				'id'      => 'cl_tpf_options_help',
				'title'   => 'Опции',
				'content' => '<p><strong>Отключить типограф для редактора</strong> — убирает кнопку "Типограф" на панели инструментов в редакторе</p>'
			) );

		} );
	}

	/**
	 * Register plugin's settings
	 */
	function options_init() {
		$types = get_post_types( array( 'public' => true ), 'names' );
		foreach ( $types as $type ) {
			register_setting( 'cl_typograf', 'cl_tpf_' . $type, '' );
		}

		register_setting( 'cl_typograf', 'cl_autop_content', '' );
		register_setting( 'cl_typograf', 'cl_autop_excerpt', '' );
		register_setting( 'cl_typograf', 'cl_disable_mce', '' );

		$this->add_metaboxes();

		if ( ! (bool) get_option( 'cl_disable_mce' ) ) {
			add_filter( 'mce_external_plugins', array( $this, 'editor_add_buttons' ) );
			add_filter( 'mce_buttons', array( $this, 'editor_register_buttons' ) );
		}
	}

	/**
	 * Add Tiny MCE plugin
	 *
	 * @param $plugin_array
	 *
	 * @return mixed
	 */
	function editor_add_buttons( $plugin_array ) {
		$plugin_array['tpf'] = $js_path = plugins_url( '', __FILE__ ) . '/../assets/js/tpf_mce.js';

		return $plugin_array;
	}

	/**
	 * Add button to Tiny MCE tool bar
	 *
	 * @param $buttons
	 *
	 * @return mixed
	 */
	function editor_register_buttons( $buttons ) {
		array_push( $buttons, 'tpf' );

		return $buttons;
	}

	/**
	 * @param $init array
	 *
	 * @return array
	 */
	function tiny_mce_before_init( $init ) {
		$init['wpautop'] = 0;

		return $init;
	}

	/**
	 * Add js for show HTML tags in editor
	 *
	 * @param $todo
	 *
	 * @return mixed
	 */
	function rewrite_default_script( $todo ) {
		global $wp_version, $wp_scripts;

		$js_path = plugins_url( '', __FILE__ ) . '/../assets/js/{VERSION}/ps_editor.js';

		if ( version_compare( $wp_version, '3.9.x', '>' ) ) {
			$js_path = str_replace( '{VERSION}', '390', $js_path );
		} elseif ( version_compare( $wp_version, '3.3.x', '>' ) ) {
			$js_path = str_replace( '{VERSION}', '330', $js_path );
		} elseif ( version_compare( $wp_version, '2.8', '>=' ) ) {
			$js_path = str_replace( '{VERSION}', '280', $js_path );
		} elseif ( version_compare( $wp_version, '2.7', '>=' ) ) {
			$js_path = str_replace( '{VERSION}', '270', $js_path );
		} else {
			$js_path = str_replace( '{VERSION}', '250', $js_path );
			if ( version_compare( $wp_version, '2.6', '>=' ) ) {
				$wp_scripts->registered['editor_functions']->src = $js_path;
			} else {
				$wp_scripts->scripts['editor_functions']->src = $js_path;
			}
		}
		$wp_scripts->add( 'ps_editor', $js_path, false, $this->mce_version );
		$key = array_search( 'editor', $todo );
		if ( $key !== false ) {
			if ( version_compare( $wp_version, '2.7', '>=' ) ) {
				$todo[ $key ] = 'ps_editor';
			} else {
				unset( $todo[ $key ] );
			}
		}

		return $todo;
	}

	/**
	 * Include assets
	 */
	function enqueue_scripts() {
		wp_register_script( 'typograf', plugins_url( '', __FILE__ ) . '/../assets/js/typograf.js', array( 'jquery' ) );
		wp_enqueue_script( 'typograf' );

		add_thickbox();
	}

	/**
	 * Add meta box to post edit page
	 */
	function add_metaboxes() {

		$types = get_post_types( array( 'public' => true ), 'names' );

		foreach ( $types as $type ) {
			if ( ! get_option( 'cl_tpf_' . $type ) ) {
				continue;
			}

			add_meta_box( 'cl_tpf_edit', 'Типограф', function () {
				include 'tpf_box.php';
			}, $type, 'side', 'low' );
		}
	}

	/**
	 * Typography text
	 */
	function tpf_text() {

		if ( ! isset( $_POST['content'] ) ) {
			wp_send_json_error( 'Нет текста для обработки!' );
		}

		$fragment = (bool) $_POST['fragment'];
		$content  = $_POST['content'];

		if ( empty( $content ) ) {
			wp_send_json_error( 'Нет текста!' );
		}

		$typograf = new RemoteTypograf( get_bloginfo( 'charset' ) );
		$typograf->htmlEntities();
		$typograf->br( false );
		$typograf->p( ! $fragment );

		$result = $typograf->processText( $content );

		wp_send_json_success(wp_unslash($result));
	}

	/**
	 * Save hook, typography text
	 *
	 * @param $post_ID int
	 */
	function save_post_process( $post_ID ) {
		global $post;

		if ( ! $post ) {
			return;
		}

		$tp = (bool) get_option( 'cl_tpf_' . $post->post_type );
		if ( ! $tp ) {
			return;
		}

		if ( wp_is_post_revision( $post_ID ) ) {
			return;
		}

		if ( ! isset( $_POST['cl_tpf_use'] ) ) {
			update_post_meta( $post_ID, 'cl_tpf_disable', 'on' );

			return;
		} else {
			delete_post_meta( $post_ID, 'cl_tpf_disable' );
		}

		$post_title   = $_POST['post_title'];
		$post_excerpt = $_POST['post_excerpt'];
		$post_content = $_POST['post_content'];

		$big_length = 32768;

		$typograf = new RemoteTypograf( get_bloginfo( 'charset' ) );
		$typograf->noEntities();
		$typograf->br( false );
		$typograf->p( false );

		$title   = ( ! empty( $post_title ) && mb_strlen( $post_title ) < $big_length ) ? $typograf->processText( strip_tags( $post_title ) ) : '';
		$excerpt = ( ! empty( $post_excerpt ) && mb_strlen( $post_excerpt ) < $big_length ) ? $typograf->processText( strip_tags( $post_excerpt ) ) : '';

		$typograf->htmlEntities();
		$typograf->br( false );
		$typograf->p( true );

		$content = ( ! empty( $post_content ) && mb_strlen( $post_content ) < $big_length ) ? $typograf->processText( $post_content ) : '';

		remove_action( 'save_post', array( $this, 'save_post_process' ) );

		wp_update_post( array(
			'ID'           => $post_ID,
			'post_title'   => $title,
			'post_excerpt' => $excerpt,
			'post_content' => $content
		) );

		add_action( 'save_post', array( $this, 'save_post_process' ) );
	}
}