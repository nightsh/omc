<?php

/**
 * Plugin name: Self Publisher
 * Plugin URI: not-yet-here
 * Description: 'testets'
 * Version: 0.1
 * Author: The A-Team
 * License: TBA
 */

// @TODO mechanism to store to db
// @TODO mechanism to get all from db
// @TODO parse the data into arrays and make them global
// @TODO ajax storing

$table_name = $wpdb->prefix . "selfpublisher";

add_action('admin_menu', sfp_menu);
function sfp_menu (){
    add_menu_page('Self Publish Settings','Self Publish Settings','administrator',__FILE__,'sfp_settings_page');
}
function sfp_settings_page(){
    // @TODO render list of models here in table
    // @TODO create javascript namespaced var with types, maybe to json or
    // something
    // @TODO add nonces to js
    
    global $wpdb;
    global $table_name;
    
    $rows = $wpdb->get_results( "SELECT * FROM $table_name" );
    
	$display = '<div class="wrap">
				<table class="wp-list-table widefat posts">
					<thead><tr>
						<th class="column-title" style="width: 75%;">Model</th>
						<th class="column-title">Operations</th>
					</tr></thead>
				';
    
    foreach ($rows as $r) {
		$display .= "<tr>
						<td>".$r->id.'. <b>'.$r->name.'</b> ('.sfp_display_json(&$r->data).")</td>
						<td>
							<button>Edit</button>
							<button>Delete</button>
						</td>
					</tr>";
	}
	
    $display .= '</table></div>';
    
    echo $display;
    
    echo '<script type=text/javascript>window.sfpSettings = {}</script>';
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
    global $table_name;

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

// @TODO test ajax calls
// jquery deferred example
// a = jQuery.post('admin-ajax.php',{'action':'addmodel'})}
add_action('wp_ajax_addmodel', 'sfp_addmodel');
function sfp_addmodel(){
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce,'sfp_add_category')) {
        print_r($_POST);
        echo 'merge add';
        exit();
    }
}
add_action('wp_ajax_removemodel', 'sfp_removemodel');
function sfp_removemodel(){
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce,'sfp_remove_category')) {
        print_r($_POST);
        echo 'merge delete';
        exit();
    }
}
?>
