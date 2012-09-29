<?php

/**
 * Plugin name: Self Publisher
 * Plugin URI: not-yet-here
 * Description: 'testets'
 * Version: 0.1
 * Author: The A-Team
 * License: TBA
 */

// @TODO parse the data into arrays and make them global
// @TODO ajax storing

add_action('admin_menu', sfp_menu);
function sfp_menu (){
    add_menu_page('Self Publish Settings','Self Publish Settings','administrator',__FILE__,'sfp_settings_page');
}
function sfp_settings_page(){
    //require_once('selfpublishing-save-helpers.php');
    echo sfp_display_table();
}

function bakeJS($data) {
    $addNonce = wp_create_nonce('sfp_addmodel');
    $removeNonce = wp_create_nonce('sfp_removemodel');
    $editNonce = wp_create_nonce('sfp_editmodel');
    echo "<script type=text/javascript>window.sfpSettings = {
        addNonce: '$addNonce',
        editNonce: '$editNonce',
        removeNonce: '$removeNonce',
        data: '$data'
    }</script>";
}

function sfp_display_table() {
	global $wpdb;
	global $table_name;

    $rows = $wpdb->get_results( "SELECT * FROM $table_name" );

	wp_enqueue_script('sfp_js', '/wp-content/plugins/selfpublisher/js/selfpublisher.js');
	wp_enqueue_script('model_js', '/wp-content/plugins/selfpublisher/js/model.js');

	$display = '<div class="wrap">
				<table class="wp-list-table widefat posts">
					<thead><tr>
						<th class="column-title" style="width: 75%;">Model</th>
						<th class="column-title">Operations</th>
					</tr></thead>
				';

    if(!empty($rows)){
        $temp = '{';
        foreach ($rows as $r) {
            $temp .= '"'.$r->name.'":';
            $temp .= $r->data .',';
            $display .= "<tr id='".$r->name."'>
                            <td>".$r->id.'. <b>'.$r->name.'</b> ('.sfp_display_json($r->data).")</td>
                            <td>
                                <button class='button' id='edit_button'>Edit</button>
                                <button class='button' id='delete_button'>Delete</button>
                            </td>
                        </tr>";
        }
        $temp = substr($temp,0,-1);
        $temp .= '}';
    } else {
        $temp = null;
    }
	bakeJS($temp);

    return $display;
}

function sfp_display_json($json){
    $json = json_decode($json);
    $display = '';
    foreach ($json as $key => $value) {
        $display .= $key.', ';
    }
    return substr($display,0,-2);
}

register_activation_hook(__FILE__,'sfp_install');
function sfp_install(){
    global $wpdb;
    global $sfp_db_version;

    $table_name = $wpdb->prefix . "selfpublisher";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name){
        $sql = "CREATE TABLE $table_name (
            id smallint(3) NOT NULL AUTO_INCREMENT,
            name tinytext NOT NULL,
            data text NOT NULL,
            UNIQUE KEY id (id)
        );";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    } else {
        // @TODO create custom post types
        // @TODO test database creation
    }

    add_option("sfp_db_version", $sfp_db_version);
}

// @TODO better security
// jquery deferred example
// a = jQuery.post('admin-ajax.php',{'action':'addmodel',nonce:window.sfpSettings.addNonce,data:the_data})}
add_action('wp_ajax_addmodel', 'sfp_addmodel');
function sfp_addmodel(){
    global $wpdb;
    $table_name = $wpdb->prefix . "selfpublisher";
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce,'sfp_addmodel')) {
        $wpdb->insert($table_name,
            array(
                'name' => $_POST['name'],
                'data' => $_POST['data']
            )
        );
        echo 'true';
        exit();die();
    } else {
        loadSelfPublishArrays();
        echo 'I pitty you fool!';
        exit();die();
    }
}

add_action('wp_ajax_removemodel', 'sfp_removemodel');
function sfp_removemodel(){
    global $wpdb;
    $table_name = $wpdb->prefix . "selfpublisher";
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce,'sfp_removemodel')) {
        // @TODO verify that delete works
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM '$table'
                WHERE post_id = %d",
                $_POST['id']
            )
        );
        loadSelfPublishArrays();
        echo 'true';
        exit();die();
    }
}
add_action('wp_ajax_editmodel', 'sfp_editmodel');
function sfp_editmodel(){
    global $wpdb;
    $table_name = $wpdb->prefix . "selfpublisher";
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce,'sfp_editmodel')) {
        $wpdb->update(
            $table_name,
            array(
                'name' => $_POST['name']
                //'data' => $_POST['data']
            ),
            array( 'ID' => $_POST['id'] ),
            array(
                '%s'
            ),
            array('%d')
        );
        loadSelfPublishArrays();
        echo 'true';
        exit();die();
    }
}

function loadSelfPublishArrays(){
    // @TODO generatePostTypeArgs(name) for all custom post types
}

?>
