<?php
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
add_action('init', 'kmfdtr_texonomy_post');
