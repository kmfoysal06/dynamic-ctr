<?php

function getData(){
    $query = new WP_Query(['post_type'=>'kmfdtr_ctr','posts_per_page'=>'-1']);
    if($query->have_posts()){
        while($query->have_posts()){
            $query->the_post();
            $post_id = get_the_ID();
            $meta = get_post_meta($post_id, 'kmfdtr_metadata', true);
            foreach($meta as  $key => $value){
                if(isset($value['tax_name'])){
                    $tax_name = $value['tax_name'];
                }else if(isset($value['tax_id'])){
                    $tax_id = $value['tax_id'];
                }else if(isset($value['hirarchial'])){
                    $hirarchial = $value['hirarchial'];
                }else if(isset($value['query_var'])){
                    $query_var = $value['query_var'];
                }else if(isset($value['show_admin_column'])){
                    $show_admin_column = $value['show_admin_column'];
                }else if(isset($value['post_types'])){
                    $post_types[] = $value['post_types'];
                }

            }
            function kmfdtr_texonomy_temp($tax_name = '', $tax_id = '', $hirarchial = false, $query_var = false, $show_admin_column = false, $post_types = []){
                register_taxonomy($tax_id, $post_types, [
                    'hierarchical' => (isset($hirarchial) && $hirarchial == 'on') ? true : false,
                    'label' => $tax_name,
                    'query_var' => (isset($query_var) && $query_var == 'on') ? true : false,
                    'show_admin_column' => (isset($show_admin_column) && $show_admin_column == 'on') ? true : false,
                ]);
            }
            if(isset($tax_name) && isset($tax_id) && isset($hirarchial) && isset($query_var) && isset($show_admin_column) && isset($post_types)){
                kmfdtr_texonomy_temp($tax_name, $tax_id, $hirarchial, $query_var, $show_admin_column, $post_types);
        }else{
            // get the error
            echo var_dump($hirarchial);
            die();
        }
        // if(function_exists('kmfdtr_texonomy_temp')){
        //     add_action('init', 'kmfdtr_texonomy_temp');
        // }
    }
}
}
// add_action('save_post', 'getData');
if(function_exists('getData')){
    add_action('init', 'getData');
}