<?php

// @TODO remove table not working wtf

if( ! defined( ‘ABSPATH’ ) && ! defined( ‘WP_UNINSTALL_ PLUGIN’ ) )
    exit ();

global $wpdb;

$table_name = $wpdb->prefix . "selfpublisher";
$sql = "DROP TABLE " . $table_name . ";";
$wpdb->query($sql);
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
db_delta($sql);
?>
