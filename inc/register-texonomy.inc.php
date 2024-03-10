<?php

function getData(){
    $post_id = get_the_ID();
    $meta = get_post_meta($post_id, 'kmfdtr_metadata', true);
    var_dump($meta);
    die();
}
add_action('save_post', 'getData');