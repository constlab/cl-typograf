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

add_action( 'init', function () {

	if ( (bool) get_option( 'cl_autop_content' ) ) {
		remove_filter( 'the_content', 'wpautop' );
		remove_filter( 'the_content', 'wptexturize' );
	}

	if ( (bool) get_option( 'cl_autop_excerpt' ) ) {
		remove_filter( 'the_excerpt', 'wpautop' );
		remove_filter( 'the_excerpt', 'wptexturize' );
	}

} );

/**
 * @param $content
 * @param bool $entities
 * @param bool $p
 * @param bool $br
 *
 * @return mixed|string
 */
function cl_tpf( $content, $entities = true, $p = true, $br = false ) {

	require 'vendor/remotetypograf.php';

	$typograf = new RemoteTypograf( get_bloginfo( 'charset' ) );

	if ( $entities ) {
		$typograf->htmlEntities();
	} else {
		$typograf->noEntities();
	}

	$typograf->br( $br );
	$typograf->p( $p );


	$result = $typograf->processText(stripcslashes($content));

	return $result;
}

if ( ! is_admin() ) {
	return;
}

$cl_plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$cl_plugin", function ( $links ) {
	$settings_link = '<a href="options-general.php?page=cl-typograf.php">Настройки</a>';
	array_unshift( $links, $settings_link );

	return $links;
} );

require 'include/class-cl-tpf-backend.php';
$backend = new Cl_Tpf_Backend();





