<?php
function kmfdtr_texonomy_post(){
    register_post_type('kmfdtr_ctr', [
        'label' => 'Custom Texonomy',
        'show_ui' => true,
        'capability_type' => 'post',
        'supports' => ['create_posts'=>'manage_options'],
    ]);
}