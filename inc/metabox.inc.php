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
    //create metabox with slug '$this->meta_slug'
    public function create_metabox() {
        add_meta_box($this->meta_slug, $this->meta_title, [$this, 'metabox_html'], 'kmfdtr_ctr');
    }
    // html output will be printed for the registered metabox
    public function metabox_html(){
        wp_nonce_field(basename(__FILE__), 'kmfdtr_meta_nonce');
        echo '
        <label for="'.esc_attr( "taxonomy" ).'">'.esc_html("Taxonomy Name:").'</label>
        <input type="'.esc_attr( "text" ).'" id="'.esc_attr( "taxonomy" ).'" name="'.esc_attr( "kmfdtr_metadata[][tax_name]" ).'" value="'.esc_attr( $this->get_the_saved_value(get_the_ID(),$this->meta_slug_og,'text','tax_name') ).'" '.esc_attr("required").'><br>
        <label for="'.esc_attr( "label" ).'">'.esc_html( "Taxonomy ID:" ).'</label>
        <input type="'.esc_attr("text").'" id="'.esc_attr( "label" ).'" name="'.esc_attr( "kmfdtr_metadata[][tax_id]" ).'"  value="'.esc_attr( $this->get_the_saved_value(get_the_ID(),$this->meta_slug_og,'text','tax_id') ).'" '.esc_attr("required").'><br>
        <label for="'.esc_attr( "hirarchial" ).'">'.esc_html( "Hirarchial:" ).'</label>
        <input type="'.esc_attr( "checkbox" ).'" id="'.esc_attr( "hirarchial" ).'" name="'.esc_attr( "kmfdtr_metadata[][hirarchial]" ).'" '.esc_attr( $this->get_the_saved_value(get_the_ID(),$this->meta_slug_og,'select','hirarchial') ).'>
        <br>
        <label for="'.esc_attr( "query_var" ).'">'.esc_html( "Query Var:" ).'</label>
        <input type="'.esc_attr( "checkbox" ).'" id="'.esc_attr( "query_var" ).'" name="'.esc_attr( "kmfdtr_metadata[][query_var]" ).'" '.esc_attr( $this->get_the_saved_value(get_the_ID(),$this->meta_slug_og,'select','query_var') ).'>
        <br>
        <label for="'.esc_attr( "show_admin_column" ).'">'.esc_html( "Show Admin Column:" ).'</label>
        <input type="'.esc_attr( "checkbox" ).'" id="'.esc_attr( "show_admin_column" ).'" name="'.esc_attr( "kmfdtr_metadata[][show_admin_column]" ).'" '.esc_attr( $this->get_the_saved_value(get_the_ID(),$this->meta_slug_og,'select','show_admin_column') ).'>
        <br>
        ';
        $post_types = get_post_types([], 'objects');
        echo '<label for="'.esc_attr( "post_types" ).'">'.esc_html("Post Types").'</label>';
        echo '<select name="'.esc_attr( "kmfdtr_metadata[][post_types]" ).'" id="'.esc_attr( "post_types" ).'" '.esc_attr( "Post Types" ).' '.esc_attr( "multiple" ).'>';
        foreach ($post_types as $post_type) {
            echo '<option value="'.esc_attr( $post_type->name ).'" '.esc_attr( $this->get_the_saved_value(get_the_ID(),$this->meta_slug_og,'multi_select',$post_type->name,'post_types') ).'>'.esc_html( $post_type->label ).'</option>';
        }
        echo '</select>';

}

    //update the metabox when the post is saved
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

        $marged_post_data = array_merge(...$this->sanitize_array($_POST[$this->meta_slug_og]));

        $tax_id = isset($marged_post_data['tax_id']) ? $marged_post_data['tax_id'] : '';
        $tax_name = isset($marged_post_data['tax_name']) ? $marged_post_data['tax_name'] : '';

        //check uniqueness of ID with existing taxonomies
        $taxonomies = get_taxonomies([], 'objects');
        foreach ($taxonomies as $taxonomy) {
            if ($taxonomy->name == $tax_id) {
                return;
            }
        }

        // final validation

        if(empty($tax_id) || empty($tax_name) || strlen($tax_id) > 20 || strlen($tax_name) > 20 || !preg_match('/^[a-zA-Z0-9_]+$/', $tax_id) || !preg_match('/^[a-zA-Z0-9_]+$/', $tax_name)){
            return;
        }

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
        //sanitize function to sanitize complex data .
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

    // sanitize array
    public function sanitize_array($input_array){
        if(is_array($input_array)){
            return array_map([$this,'sanitize_array'], $input_array);
        }else{
            return is_scalar($input_array) ? sanitize_text_field($input_array) : $input_array ;
        }
    } 
    //static function to easilly create metabox without creating instance of the class
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