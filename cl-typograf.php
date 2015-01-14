<?php

/*
Plugin Name: CL Typograf
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: A brief description of the Plugin.
Version: 1.0
Author: Kalinichenko Ivan
Author URI: http://URI_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! is_admin() ) {
	return;
}

require 'vendor/remotetypograf.php';

add_action( 'admin_menu', 'cl_tpf_menu' );
function cl_tpf_menu() {
	add_options_page( 'Типограф', 'Типограф', 'manage_options', 'cl-typograf.php', function () {
		include 'include/options.php';
	} );
}

add_action( 'admin_init', function () {
	$types = get_post_types( array( 'public' => true ), 'names' );
	foreach ( $types as $type ) {
		register_setting( 'cl_typograf', 'cl_tpf_' . $type, '' );
	}
} );

add_action( 'save_post', 'cl_tpf_save_post' );
function cl_tpf_save_post( $post_id ) {
	$post = get_post( $post_id );
	$tp   = boolval( get_option( 'cl_tpf_' . $post->post_type ) );

	if ( ! $tp ) {
		return;
	}

	$typograf = new RemoteTypograf( get_bloginfo( 'charset' ) );
	$typograf->noEntities();
	$typograf->br( false );
	$typograf->p( false );

	$title   = $typograf->processText( strip_tags( $post->post_title ) );
	$excerpt = $typograf->processText( strip_tags( $post->post_excerpt ) );

	$typograf->htmlEntities();
	$typograf->br( true );
	$typograf->p( true );

	$content = $typograf->processText( stripslashes( $post->post_content ) );

	remove_action( 'save_post', 'cl_tpf_save_post' );

	wp_update_post( array(
		'ID'           => $post_id,
		'post_title'   => $title,
		'post_excerpt' => $excerpt,
		'post_content' => $content
	) );

	add_action( 'save_post', 'cl_tpf_save_post' );
}