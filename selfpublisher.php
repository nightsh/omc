<?php

/**
 * Plugin name: Self Publisher
 * Plugin URI: not-yet-here
 * Description: 'testets'
 * Version: 0.1
 * Author: The A-Team
 * License: TBA
 */

// @TODO mechanism to get all from db
// @TODO parse the data into arrays and make them global
// @TODO ajax storing

add_action('admin_menu', sfp_menu);
function sfp_menu (){
    add_menu_page('Self Publish Settings','Self Publish Settings','administrator',__FILE__,'sfp_settings_page');
}
function sfp_settings_page(){
    // @TODO render list of models here in table
    // @TODO create javascript namespaced var with types, maybe to json or
    // something
    // @TODO add nonces to js
    $addNonce = wp_create_nonce('sfp_addmodel');
    $removeNonce = wp_create_nonce('sfp_removemodel');
    $editNonce = wp_create_nonce('sfp_editmodel');
    echo "<script type=text/javascript>window.sfpSettings = {
        addNonce: '$addNonce',
        editNonce: '$editNonce',
        removeNonce: '$removeNonce'
    }</script>";
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
            UNIQUE KEY id (id)
        );";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    } else {
        // @TODO create custom post types
    }

    add_option("sfp_db_version", $sfp_db_version);
}

// @TODO better security
// jquery deferred example
// a = jQuery.post('admin-ajax.php',{'action':'addmodel',nonce:window.sfpSettings.addNonce})}
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
        echo 'true';
        exit();die();
    }
}
?>
