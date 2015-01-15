<?php

require __DIR__ . '/../vendor/remotetypograf.php';

class Cl_Tpf_Backend {

	var $mce_version = '20080121';

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'option_menu' ) );
		add_action( 'admin_init', array( $this, 'options_init' ) );
		add_action( 'save_post', array( $this, 'save_post_process' ) );

		add_filter( 'tiny_mce_before_init', array( $this, 'tiny_mce_before_init' ) );
		add_filter( 'print_scripts_array', array( $this, 'rewrite_default_script' ) );
	}

	/**
	 * Add option page in options section
	 */
	function option_menu() {
		add_options_page( 'Типограф', 'Типограф', 'manage_options', 'cl-typograf.php', function () {
			include 'tpf_options.php';
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
	 * @param $post_ID int
	 */
	function save_post_process( $post_ID ) {
		global $post;

		$tp = (bool) get_option( 'cl_tpf_' . $post->post_type );
		if ( ! $tp ) {
			return;
		}

		if ( wp_is_post_revision( $post_ID ) ) {
			return;
		}

		$post_title   = $_POST['post_title'];
		$post_excerpt = $_POST['post_excerpt'];
		$post_content = $_POST['post_content'];

		$typograf = new RemoteTypograf( get_bloginfo( 'charset' ) );
		$typograf->noEntities();
		$typograf->br( false );
		$typograf->p( false );

		$title   = ( ! empty( $post_title ) ) ? $typograf->processText( strip_tags( $post_title ) ) : '';
		$excerpt = ( ! empty( $post_excerpt ) ) ? $typograf->processText( strip_tags( $post_excerpt ) ) : '';

		$typograf->htmlEntities();
		$typograf->br( false );
		$typograf->p( true );

		$content = ( ! empty( $post_content ) ) ? $typograf->processText( stripcslashes( $post_content ) ) : '';

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