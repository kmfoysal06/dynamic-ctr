<?php
if(!defined('ABSPATH')){
    exit; // exit if accessed directly
}
//register post type for custom taxonomy registration
function kmfdtr_texonomy_post() {
    register_post_type('kmfdtr_ctr', [
        'label' => 'CTR',
        'show_ui' => true,
        'capability_type' => 'post',
        'supports' => ['title'], 
        'capabilities' => [
            'create_posts' => 'manage_options',
        ],
        'map_meta_cap' => true,
        'menu_icon' => 'dashicons-tag',
    ]);
}
if(function_exists('kmfdtr_texonomy_post')){
    add_action('init', 'kmfdtr_texonomy_post');
}
