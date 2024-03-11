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
            $tax_id = (isset($tax_id) && !empty($tax_id)) ? $tax_id : '';
            $tax_name = (isset($tax_name) && !empty($tax_name)) ? $tax_name : '';
            $post_types = (isset($post_types) && !empty($post_types)) ? $post_types : [];
            $hirarchial = (isset($hirarchial) && $hirarchial == 'on') ? true : false;
            $query_var = (isset($query_var) && $query_var == 'on') ? true : false;
            $show_admin_column = (isset($show_admin_column) && $show_admin_column == 'on') ? true : false;
            
            function kmfdtr_texonomy_temp($tax_name = '', $tax_id = '', $hirarchial = false, $query_var = false, $show_admin_column = false, $post_types = []){
                register_taxonomy($tax_id, $post_types, [
                    'hierarchical' => $hirarchial,
                    'label' => $tax_name,
                    'query_var' => $query_var,
                    'show_admin_column' => $show_admin_column,
                ]);
            }
            if(function_exists('kmfdtr_texonomy_temp')){
                kmfdtr_texonomy_temp($tax_name, $tax_id, $hirarchial, $query_var, $show_admin_column, $post_types);
            }
    }
}
}
if(function_exists('getData')){
    add_action('init', 'getData');
}