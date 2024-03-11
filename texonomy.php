<?php
/*
Plugin Name: Dynamic CTR
Author: Kazi Mohammad Foysal
Author URI: http://profiles.wordpress.org/kmfoysal06
Description: Simple and lightweight plugin for creating and managing custom taxonomies in WordPress.
Version: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
if(!defined('ABSPATH')){
    exit;
}
require_once plugin_dir_path(__FILE__) . 'inc/register_texonomy_post.inc.php';
require_once plugin_dir_path(__FILE__) . 'inc/metabox.inc.php';
require_once plugin_dir_path(__FILE__) . 'inc/register-texonomy.inc.php';
require_once plugin_dir_path(__FILE__) . 'inc/create_post.inc.php';
// including necessary scrips and styles
function kmfdtr_load_assets(){

    //Add the Select2 CSS file
    wp_enqueue_style( 'select2-css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', array(), '4.1.0-rc.0');

    //Add the Select2 JavaScript file in footer
    wp_enqueue_script( 'select2-js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', 'jquery', '4.1.0-rc.0', false );

    //Add a JavaScript file to initialize the Select2 elements
    wp_enqueue_script( 'select2-init', plugin_dir_url( __FILE__ ) . 'assets/select2-init.js', array('jquery'), '1.0.0', false );

}
if(function_exists('kmfdtr_load_assets')){
    add_action('admin_enqueue_scripts', 'kmfdtr_load_assets');
}
