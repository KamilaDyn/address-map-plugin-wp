<?php
/*
* file to unistall plugin
* @packgage address-map
*/

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// clean DB via sql

global $wpdb;
$tablePostMeta = $wpdb->prefix . 'postmeta';
$tablePosts = $wpdb->prefix . 'posts';
$wpdb->query("DELETE FROM $tablePosts WHERE post_type = 'address_map'");
$wpdb->query("DELETE FROM $tablePostMeta WHERE post_id NOT IN (SELECT id FROM wp_posts WHERE post_type='address_map')");
