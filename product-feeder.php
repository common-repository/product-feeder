<?php
    /*
    * Plugin Name:      Product Feeder
    * Requires Plugins: woocommerce
    * Plugin URI:       https://product-feeder.com/nl/sources/wordpress-woocommerce
    * Description:      Connect with various marketplaces for automated synchronization of products, orders and returns! Try it now at https://product-feeder.com
    * Version:          3.0.0
    * Author:           Product Feeder
    * Author URI:       https://product-feeder.com
    * Text Domain:      product-feeder
    * Domain Path:      /languages
    */

    if (!defined('ABSPATH')) exit; //PREVENT DIRECT ACCESS TO THE FILE
    register_activation_hook(__FILE__, 'product_feeder_plugin_activation');

    function product_feeder_plugin_activation() {
        //Create and store API Token with a length of 30 characters
        if (empty(get_option('product_feeder_api_key'))) {
            if (update_option('product_feeder_api_key', bin2hex(random_bytes(15))) === false) wp_die(__('Failed to create/store the API Key', 'product-feeder'));
        }
        if (empty(get_option('product-feeder-default-order-status'))) {
            if (update_option('product-feeder-default-order-status', 'wc-processing') === false) wp_die(sprintf(__('Failed to set default order status to: %s', 'product-feeder'), 'wc-processing'));
        }
        if (empty(get_option('product-feeder-accepted-order-statuses'))) {
            $DefaultAcceptedOrderStatuses = array('wc-processing');
            if (update_option('product-feeder-accepted-order-statuses', $DefaultAcceptedOrderStatuses) === false) wp_die(sprintf(__('Failed to set accepted statuses for orders to: %s', 'product-feeder'), implode(',', $DefaultAcceptedOrderStatuses)));
        }
        if (empty(get_option('product-feeder-rejected-order-statuses'))) {
            $DefaultRejectedOrderStatuses = array('wc-cancelled', 'wc-failed');
            if (update_option('product-feeder-rejected-order-statuses', $DefaultRejectedOrderStatuses) === false) wp_die(sprintf(__('Failed to set rejected statuses for orders to: %s', 'product-feeder'), implode(',', $DefaultRejectedOrderStatuses)));
        }
        if (empty(get_option('product-feeder-shipped-order-statuses'))) {
            $DefaultShippedOrderStatuses = array('wc-completed');
            if (update_option('product-feeder-shipped-order-statuses', $DefaultShippedOrderStatuses) === false) wp_die(sprintf(__('Failed to set shipped statuses for orders to: %s', 'product-feeder'), implode(',', $DefaultShippedOrderStatuses)));
        }
    }

    if (!function_exists('get_plugin_data')) require_once(ABSPATH.'wp-admin/includes/plugin.php');
    define('PRODUCT_FEEDER_PLUGIN_DATA', get_plugin_data( __FILE__));
    define('PRODUCT_FEEDER_PLUGIN_BASENAME', plugin_basename(__FILE__));

    require_once(__DIR__ . "/includes/product-feeder.php");
    $ProductFeeder = new Product_Feeder();
    $ProductFeeder->Run();