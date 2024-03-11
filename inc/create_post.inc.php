<?php
if(!defined('ABSPATH')){
    exit;
    // exit if accessed directly
}
function kmfdtr_post(){
    if(post_type_exists('kmfdtr_ctr')){
    // Check if there are any posts of your custom post type
    $existing_posts = get_posts(['post_type' => 'kmfdtr_ctr', 'posts_per_page' => -1]);

    // If no posts are found, add a new one
    if (empty($existing_posts)) {
        $new_post = [
            'post_title'   => 'Demo Post',
            'post_status'  => 'publish',
            'post_type'    => 'kmfdtr_ctr',
        ];

        // Insert the post into the database
        if (is_wp_error(wp_insert_post($new_post))) {
            wp_die('there is an error.');
        }
    }
    }
}

if(function_exists('kmfdtr_post')){
    add_action('init', 'kmfdtr_post');
}