<?php

/**
 * Plugin Name:       Address Map
 * Description:   Map to add adress of your business, homes, schools, hospitals, bars etc. 
 * Version:           1.0.0
 * Author:         Kamila Dynysiuk
 * License:           GNU v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       address-map
 * 
 */

// Exit if accessed directly

if (!defined('ABSPATH')) {
    exit;
}


// define variable for path to this plugin file
if (!class_exists('AddressMap')) {
    class AddressMap
    {
        function __construct()
        {

            $this->loader_operations();
            $this->plugin_includes();
        }

        function plugin_includes()
        {
            include_once('map.php');
            include_once('address-order.php');
            include_once('address-router.php');
        }

        function loader_operations()
        {
            add_action('init', array($this, 'plugin_init'));
            add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
            add_action('manage_address_map_posts_custom_column', 'address_map_calculation_columns', 10, 2);
            add_filter('manage_address_map_posts_columns', 'address_map_column');
            add_action('wp_enqueue_scripts', array($this, 'enqueue'));
            add_shortcode('address_map', 'display_map');
        }

        function plugin_init()
        {
            address_map_order_cpt();
        }

        function add_meta_boxes()
        {
            add_meta_box('address-map-order-box', __('Dodaj adres placówki', 'address-map'), 'address_map_order_meta_box', 'address_map', 'normal', 'high');
        }

        function enqueue()
        {
            // enqueue all our scripts

            wp_enqueue_style('leaflet-style', plugins_url('/assets/leaflet/leaflet.css', __FILE__));
            wp_enqueue_style('my-style', plugins_url('/assets/style/style.css', __FILE__));

            wp_enqueue_script('leafle-script', plugins_url('/assets/leaflet/leaflet.js', __FILE__));

            wp_enqueue_script('my-script', plugin_dir_url(__FILE__)  . '/assets/js/my-script.js', '', '', true);
            wp_enqueue_script('leaflet-src-script', plugins_url('/assets/leaflet/leaflet-src.js', __FILE__));

            // localize scripts 
            wp_localize_script('my-script', 'addressData', array(
                'root_url' => get_site_url(),
                'nonce' => wp_create_nonce('wp_rest')
            ));
        }

        function activate()
        {
            require_once plugin_dir_path(__FILE__) . 'inc/address-map-activate.php';
            AddressMapPluginActivate::activate();
        }
    }

    $addressMap = new AddressMap();


    // activation
    register_activation_hook(__FILE__, array($addressMap, 'activate'));

    // deactivation
    require_once plugin_dir_path(__FILE__) . 'inc/address-map-deactivate.php';
    register_deactivation_hook(__FILE__, array('AddressMapPluginDeactivate', 'deactivate'));
}
