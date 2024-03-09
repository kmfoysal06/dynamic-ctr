<?php
if(!defined('ABSPATH')){
    exit;
    // exit if accessed directly
}
class KMFDTR_METS {
    public $meta_slug;
    public $meta_slug_og;
    public $meta_title;
    public $meta_id;
    
    public function create_metabox() {
        add_meta_box('kmfdtr_meta', $this->meta_title, [$this, 'metabox_html'], 'kmfdtr_ctr');
    }
    public function metabox_html(){
        wp_nonce_field(basename(__FILE__), 'kmfdtr_meta_nonce');
        echo '
        <label for="taxonomy">Taxonomy Name:</label>
        <input type="text" id="taxonomy" name="kmfdtr_metadata[][tax_name]" value="'.$this->get_the_saved_value(get_the_ID(),$this->meta_slug_og,'text','tax_name').'" required><br>
        <label for="label">Taxonomy ID:</label>
        <input type="text" id="label" name="kmfdtr_metadata[][tax_id]"  value="'.$this->get_the_saved_value(get_the_ID(),$this->meta_slug_og,'text','tax_id').'" required><br>
        <label for="post_type">Post Type Name:</label>
        <input type="text" id="post_type" name="kmfdtr_metadata[][post_type]" value="'.$this->get_the_saved_value(get_the_ID(),$this->meta_slug_og,'text','post_type').'" required><br>
        <label for="hirarchial">Hirarchial:</label>
        <input type="checkbox" id="hirarchial" name="kmfdtr_metadata[][hirarchial]" '.$this->get_the_saved_value(get_the_ID(),$this->meta_slug_og,'select','hirarchial').'>
        <br>
        <label for="query_var">Query Var:</label>
        <input type="checkbox" id="query_var" name="kmfdtr_metadata[][query_var]" '.$this->get_the_saved_value(get_the_ID(),$this->meta_slug_og,'select','query_var').'>
        <br>
        <label for="show_admin_column">Show Admin Column:</label>
        <input type="checkbox" id="show_admin_column" name="kmfdtr_metadata[][show_admin_column]" '.$this->get_the_saved_value(get_the_ID(),$this->meta_slug_og,'select','show_admin_column').'>
        ';
        $post_types = get_post_types([], 'objects');
        echo '<label for="post_types">Select Post Types:</label>';
        echo '<select id="post_types" name="kmfdtr_metadata[][post_types]" multiple>';
        foreach ($post_types as $post_type) {
            echo '<option value="'.$post_type->name.'" '.$this->get_the_saved_value(get_the_ID(),$this->meta_slug_og,"multi_select",$post_type->name,"post_types").'>'.$post_type->name.'</option>';
        }
        echo '</select>';

}


public function save_metabox($post_id, $post, $update) {
    // Check if this is a valid post object and the post type is 'cpr'
    if (!($post instanceof WP_Post) || 'kmfdtr_ctr' !== $post->post_type) {
        return;
    }

    // Nonce verification
    $kmfdtr_meta_nonce = isset($_POST['kmfdtr_meta_nonce']) ? sanitize_text_field($_POST['kmfdtr_meta_nonce']) : '';
    if (!wp_verify_nonce($kmfdtr_meta_nonce, basename(__FILE__))) {
        return;
    }

    // Avoid autosave and revision issues
    if (wp_is_post_revision($post_id) || defined('DOING_AUTOSAVE') && DOING_AUTOSAVE || wp_is_post_autosave($post_id)) {
        return;
    }

    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Sanitize and validate input data
    // $cpr_id = isset($_POST[$this->meta_slug_og]['cpr_id']) ? sanitize_key($_POST[$this->meta_slug_og]['cpr_id']) : '';
    // $cpr_name = isset($_POST[$this->meta_slug_og]['cpr_name']) ? sanitize_text_field($_POST[$this->meta_slug_og]['cpr_name']) : '';

    // if (empty($cpr_id) || empty($cpr_name) || strlen($cpr_id) > 20 || strlen($cpr_name) > 20 || !preg_match('/^[a-zA-Z0-9_]+$/', $cpr_id) || !preg_match('/^[a-zA-Z0-9_]+$/', $cpr_name)) {
    //     return;
    // }

    // Check for uniqueness of ID using WordPress API
    // $existing_ids = get_metadata('post', $post_id, $cpr_id, true);
    // if ($existing_ids) {
    //     return; // ID or name already exists, return to avoid duplicate
    // }

    // Update post meta
    update_post_meta($post_id, $this->meta_slug_og, $this->sanitize_array($_POST[$this->meta_slug_og]));
}

public function get_the_saved_value($id,$slug,$type,$key,$needle=false){
    $id = absint($id); // Ensure $id is an integer
    $slug = sanitize_key($slug); // Ensure $slug contains only alphanumeric characters and underscores
    $type = sanitize_text_field($type); // Ensure $type is a string
    $key = sanitize_key($key); // Ensure $key contains only alphanumeric characters and underscores

    $dbs = get_post_meta($id,$slug,true);
    return sanitize_text_field($this->sanitize_data($dbs,$key,$type,$needle));
}

public function sanitize_data($data,$data_key,$type,$neddle_for_multiselect=false){
            if(is_array($data)){
                switch($type){
                case 'text':
                    $data = array_merge(...$data);
                    if(array_key_exists($data_key, $data) && $data[$data_key] !== null){
                        return $data[$data_key];
                    }
                    break;
                case 'select':
                    $data = array_merge(...$data);
                    if(array_key_exists($data_key, $data) && $data[$data_key] !== null){
                        return $data[$data_key] == 'on' ? 'checked' : '' ;
                    }
                    break;
                case 'multi_select':
                    foreach ($data as $item) {
                        if (isset($item[$neddle_for_multiselect]) && $item[$neddle_for_multiselect] == $data_key) {
                            return 'selected';
                        }
                    }
                    break;
                    default:
                        return;
                    break;
                     }
            }
    }


    public function sanitize_array($input_array){
        if(is_array($input_array)){
            return array_map([$this,'sanitize_array'], $input_array);
        }else{
            return is_scalar($input_array) ? sanitize_text_field($input_array) : $input_array ;
        }
    } 

    public static function createMetabox(string $slug,array $data){
        if(empty($slug) || empty($data)){
            return;
        }
        $instance = new self();
        $instance->meta_slug = $slug.'[]';
        $instance->meta_slug_og = $slug;
        $instance->meta_title = $data['title'] ;
        add_action("add_meta_boxes", [$instance,'create_metabox'],10,1);
        add_action("save_post", [$instance,'save_metabox'],10,3);
    }
        }
if(class_exists('KMFDTR_METS')){
  // Set a unique prefix for the metabox
  $slug = 'kmfdtr_metadata';
  // Create a metabox
  KMFDTR_METS::createMetabox($slug, [
    'title'     => 'Register Texonomies',
  ]);

}