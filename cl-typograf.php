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

	remove_filter( 'the_content', 'wpautop' );
	remove_filter( 'the_excerpt', 'wpautop' );

	remove_filter( 'the_content', 'wptexturize' );
	remove_filter( 'the_excerpt', 'wptexturize' );

} );

if ( ! is_admin() ) {
	return;
}

require 'include/class-cl-tpf-backend.php';
$backend = new Cl_Tpf_Backend();





