<?php


// @TODO render array of custom post types
// @TODO render array of custom post type metas

function custom_details_add_box() {
    for ($i = 0; $i < $array; $i++) {
         // code...
    }
    add_meta_box($array[$i]['id'], $array[$i]['title'], 'default_meta_show_box', $array[$i]['page'], $array[$i]['context'], $array[$i]['priority'], $array[$i]['fields']);
}



/***
 * used by custom post types build with the supplied model to construct the meta fields and box, based on the fields
 * described in the array supplied
 *
 * it includes elements ready for html5
 *
 * @param $post
 * @param $meta_box the meta_box array used to build the meta attached for a custom post type
 */
function default_meta_show_box($post,$meta_box) {
    // Use nonce for verification
    echo '<input type="hidden" name="meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';

    echo '<table class="form-table">';
    ///
    //@todo research strange bug - basically i wanted to send the fields of each metabox but right now it constructs a
    //different array at the callback args construction
    //
    foreach ($meta_box['args'] as $field) {
        if ($field['type'] === 'connection') {
            // get connections here from table
        } else {
            // get current post meta data
            $meta = get_post_meta($post->ID, $field['id'], true);
        }

        if(!isset($field['hide']) || !$field['hide']){
            echo '<tr>',
                '<th style="width:20%"><label for="', $field['id'], '">', $field['name'], '</label></th>',
                '<td>';
        }
        switch ($field['type']) {

        case 'connection':
            displayConnectionWidget($post,$field);
            break;
        case 'hidden':
            echo '<input type="hidden" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" />';
            break;
            //If Text
        case 'time':
        case 'date':
        case 'text':
            echo '<input type="',$field['type'], '" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" />',
                '<br />', $field['desc'];
            break;

        case 'range':
            echo '<input type="range" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" min="0" max="10"/>',
                '<br />', $field['desc'];
            break;

            //If Text Area
        case 'textarea':
            echo '<textarea name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="4" style="width:97%">', $meta ? $meta : $field['std'], '</textarea>',
                '<br />', $field['desc'];
            break;

            //If Button
        case 'button':
            echo '<input type="button" name="', $field['id'], '" id="', $field['id'], '"value="', $meta ? $meta : $field['std'], '" />';
            break;
        case 'checkbox':
            $to_echo = '';
            if($meta == 1){
                $to_echo = 'CHECKED';
            }
            echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '"value="', $meta ? $meta : $field['std'], '" ', $to_echo,'/>';
            break;
        }
        echo  '<td>',
            '</tr>';
    }

    echo '</table>';
}

/**
 * saves meta data for a custom post type
 *
 * handles the $_POST data in order to save the meta information for the custom post types;
 * gets the array describing the fields in the custom post type from a $_POST element and for each field, it queries the
 * database for existing meta value and based on the case, it either updates the meta value with the new information or
 * removes the meta field from the database if the user supplied an empty value
 */
function save_data($post_id) {
    // mambo jambo stuff to actually get the array used
    // might not be pretty, but for the backend is actually decent enough to use, and allows this function to work
    // properly
    // verify nonce
    if (isset($_POST['meta_box_nonce']) && !wp_verify_nonce($_POST['meta_box_nonce'], basename(__FILE__))) {
        return $post_id;
    }

    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    // check permissions
    if (isset($_POST['post_type']) && $_POST['post_type'] == 'page') {
        if (!current_user_can('edit_page', $post_id)) {
            return $post_id;
        }
    } elseif (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }

    if( isset($_POST['meta_box'])){
        $metabox = $_POST['meta_box'];
        global $$metabox;

        //goes through all the fields in the array with the meta fields descriptions
        //and operates based on case on the previous meta values
        foreach (${$metabox}['fields'] as $field) {
            if ($field['type'] === 'connection') {
                updateConnections($field['connection'],$post_id,$_POST[$field['connection']]);
            } else {
                $old = get_post_meta($post_id, $field['id'], true);
                $new = $_POST[$field['id']];

                if ($new && $new != $old) {
                    update_post_meta($post_id, $field['id'], $new);
                } elseif ('' == $new && $old) {
                    delete_post_meta($post_id, $field['id'], $old);
                }
            }
        }
    }

    if(WITH_ICS){
        $postType = get_post_type();
        if ($postType === "event" || $postType === "portfolio") {
            remakeICSCache($postType,$post_id);
        }
    }

}

add_action('save_post', 'save_data');

function generatePostTypeArgs(name){
    $labels =array(
        'name' => _x(name,'post type general name'),
        'singular_name' => _x(name,'post type singular name'),
        'add_new' => _x('Add '.name,'portfolio item'),
        'add_new_item' => __('Add new '.name),
        'edit_item' => __('Edit '.name),
        'new_item' => __('New '.name),
        'view_item' => __('View '.name),
        'search_items' => __('Search '.name),
        'not_found' => __('No entry was found for '.name),
        'not_found_in_trash' => __('Nothing found in Trash'),
        'parent_item_colon' => ''
    );

    $args = array(
        'labels' => $labels,
        'public' => false,
        'publicly_queryable' => true,
        'show_ui' => true,
        'query_var' => true,
        'menu_icon' => '',
        'rewrite' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title','content','thumbnail')
    );

    return $args;
}
?>
