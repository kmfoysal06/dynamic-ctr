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

function kmfdtr_texonomy_temp(){
    register_taxonomy('cpt_cat', 'cpt', [
        'hierarchical' => true,
        'label' => 'Cpt Category',
        'query_var' => true,
        'show_admin_column' => true,
    ]);
}