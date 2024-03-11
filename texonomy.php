<?php
/*
Plugin Name: Dynamic CTR
Author: Kazi Mohammad Foysal
Author URI: http://profiles.wordpress.org/kmfoysal06
Description: This is a custom texonomy plugin
Version: 1.0

*/
if(!defined('ABSPATH')){
    exit;
}
require_once plugin_dir_path(__FILE__) . 'inc/register_texonomy_post.php';
require_once plugin_dir_path(__FILE__) . 'inc/metabox.inc.php';
require_once plugin_dir_path(__FILE__) . 'inc/register-texonomy.inc.php';
require_once plugin_dir_path(__FILE__) . 'inc/create_post.inc.php';
// including necessary scrips and styles
function admin_enqueue_scripts_callback(){

    //Add the Select2 CSS file
    wp_enqueue_style( 'select2-css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', array(), '4.1.0-rc.0');

    //Add the Select2 JavaScript file in footer
    wp_enqueue_script( 'select2-js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', 'jquery', '4.1.0-rc.0', true );

    //Add a JavaScript file to initialize the Select2 elements
    wp_enqueue_script( 'select2-init', plugin_dir_url( __FILE__ ) . 'js/select2-init.js', array('jquery'), '1.0.0', true );

}
add_action( 'admin_enqueue_scripts', 'admin_enqueue_scripts_callback' );
