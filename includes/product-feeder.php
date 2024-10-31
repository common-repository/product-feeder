<?php
    class Product_Feeder {

        //DEPLOY STEPS
        //svn co https://plugins.svn.wordpress.org/product-feeder
        //cd product-feeder
        //COPY NEW VERSION FILES TO /tags/{VERSION}
        //svn add /tags/{VERSION}
        //svn status
        //svn commit -m "Updated to version X"

        private $WPMLActive = false;
        private $AttributeValues = array();
        private $AttributeTaxonomies = NULL;
        private $AttributeDisplayValues = array();

        public function __construct() {
            if (!defined('ABSPATH')) exit; //PREVENT DIRECT ACCESS TO THE FILE
            if (!function_exists('is_plugin_active')) require_once(ABSPATH.'wp-admin/includes/plugin.php');
            $this->WPMLActive = is_plugin_active('sitepress-multilingual-cms/sitepress.php');
        }

        public function Run() {
            add_action('admin_enqueue_scripts', function () {
                if (is_admin()) wp_enqueue_style('product-feeder-style', plugins_url('css/product-feeder.css?time='.@filemtime(__DIR__ . "/../includes/css/product-feeder.css"), __FILE__));
            });
            add_action('admin_menu', function() { add_menu_page(__('Product Feeder', 'product-feeder'), __('Product Feeder', 'product-feeder'), 'manage_options', 'product-feeder-settings', array($this, 'GetSettingsPage' ), 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxNzIuNTQgMTczLjk5Ij48ZGVmcz48c3R5bGU+LmNscy0xe2ZpbGw6IzJhMjg0YTt9PC9zdHlsZT48L2RlZnM+PHBhdGggY2xhc3M9ImNscy0xIiBkPSJtOTUuMyw0Mi43MWMwLDQuNTEtMy42Niw4LjE3LTguMTcsOC4xN3MtOC4xNy0zLjY2LTguMTctOC4xNywzLjY2LTguMTcsOC4xNy04LjE3LDguMTcsMy42Niw4LjE3LDguMTciLz48cGF0aCBjbGFzcz0iY2xzLTEiIGQ9Im0xMzUuNjMsMjMuNDZjMCwzLjc5LTMuMDcsNi44NS02Ljg1LDYuODVzLTYuODUtMy4wNy02Ljg1LTYuODUsMy4wNy02Ljg1LDYuODUtNi44NSw2Ljg1LDMuMDcsNi44NSw2Ljg1Ii8+PHBhdGggY2xhc3M9ImNscy0xIiBkPSJtMTQ3Ljc2LDI2LjFjMCwyLjE4LTEuNzcsMy45NS0zLjk2LDMuOTVzLTMuOTUtMS43Ny0zLjk1LTMuOTUsMS43Ny0zLjk2LDMuOTUtMy45NiwzLjk2LDEuNzcsMy45NiwzLjk2Ii8+PHBhdGggY2xhc3M9ImNscy0xIiBkPSJtMTEyLjYzLDIwLjAzYzUuMzEsMy4zMiw2LjkyLDEwLjMxLDMuNiwxNS42Mi0zLjMyLDUuMzEtMTAuMzIsNi45Mi0xNS42MiwzLjYtNS4zMS0zLjMyLTYuOTItMTAuMzItMy42LTE1LjYyLDMuMzItNS4zMSwxMC4zMi02LjkyLDE1LjYyLTMuNTkiLz48cGF0aCBjbGFzcz0iY2xzLTEiIGQ9Im0xMjEuMTMsNTEuNjdjMCw0LjUxLTMuNjYsOC4xNy04LjE3LDguMTdzLTguMTctMy42Ni04LjE3LTguMTcsMy42Ni04LjE3LDguMTctOC4xNyw4LjE3LDMuNjYsOC4xNyw4LjE3Ii8+PHBhdGggY2xhc3M9ImNscy0xIiBkPSJtMTY0LjYzLDYwLjYzYzAsMy43OS0zLjA3LDYuODUtNi44NSw2Ljg1cy02Ljg1LTMuMDctNi44NS02Ljg1LDMuMDctNi44NSw2Ljg1LTYuODUsNi44NSwzLjA3LDYuODUsNi44NSIvPjxwYXRoIGNsYXNzPSJjbHMtMSIgZD0ibTE3Mi41NCw3MS43YzAsMi4xOC0xLjc3LDMuOTYtMy45NiwzLjk2cy0zLjk1LTEuNzctMy45NS0zLjk2LDEuNzctMy45NSwzLjk1LTMuOTUsMy45NiwxLjc3LDMuOTYsMy45NSIvPjxwYXRoIGNsYXNzPSJjbHMtMSIgZD0ibTE0OC4wMiw1Mi40NmMwLDYuMjYtNS4wOCwxMS4zMy0xMS4zMywxMS4zM3MtMTEuMzQtNS4wOC0xMS4zNC0xMS4zMyw1LjA4LTExLjM0LDExLjM0LTExLjM0LDExLjMzLDUuMDgsMTEuMzMsMTEuMzQiLz48cGF0aCBjbGFzcz0iY2xzLTEiIGQ9Im0xMzYuOTUsNzQuMDhjMCw0LjUxLTMuNjYsOC4xNy04LjE3LDguMTdzLTguMTctMy42Ni04LjE3LTguMTcsMy42Ni04LjE3LDguMTctOC4xNyw4LjE3LDMuNjYsOC4xNyw4LjE3Ii8+PHBhdGggY2xhc3M9ImNscy0xIiBkPSJtMTY2LjQ3LDEwNy44MmMwLDMuNzktMy4wNyw2Ljg1LTYuODUsNi44NXMtNi44NS0zLjA3LTYuODUtNi44NSwzLjA3LTYuODUsNi44NS02Ljg1LDYuODUsMy4wNyw2Ljg1LDYuODUiLz48cGF0aCBjbGFzcz0iY2xzLTEiIGQ9Im0xNjEuNDYsMTE5LjE1Yy0yLjExLjI2LTMuNDMsMi4zNy0zLjE2LDQuNDguMjYsMi4xMSwyLjM3LDMuNDMsNC40OCwzLjE2LDIuMTEtLjI2LDMuNDMtMi4zNywzLjE2LTQuNDgtLjI2LTIuMTEtMi4zNy0zLjQzLTQuNDgtMy4xNloiLz48cGF0aCBjbGFzcz0iY2xzLTEiIGQ9Im0xNTguNTYsODguNTdjMCw2LjI2LTUuMDgsMTEuMzQtMTEuMzQsMTEuMzRzLTExLjMzLTUuMDgtMTEuMzMtMTEuMzQsNS4wOC0xMS4zMywxMS4zMy0xMS4zMywxMS4zNCw1LjA4LDExLjM0LDExLjMzIi8+PHBhdGggY2xhc3M9ImNscy0xIiBkPSJtMTM2LjQyLDEwMS40OWMwLDQuNTEtMy42Niw4LjE3LTguMTcsOC4xN3MtOC4xNy0zLjY2LTguMTctOC4xNywzLjY2LTguMTcsOC4xNy04LjE3LDguMTcsMy42Niw4LjE3LDguMTciLz48cGF0aCBjbGFzcz0iY2xzLTEiIGQ9Im0xMzYuMDIsMTQwLjVjMy41MiwxLjM5LDUuMjUsNS4zNywzLjg2LDguODktMS4zOSwzLjUyLTUuMzcsNS4yNS04Ljg5LDMuODYtMy41Mi0xLjM5LTUuMjUtNS4zNy0zLjg2LTguODksMS4zOS0zLjUyLDUuMzctNS4yNSw4Ljg5LTMuODYiLz48cGF0aCBjbGFzcz0iY2xzLTEiIGQ9Im0xMjguMjUsMTU3LjExYy0xLjg1LTEuMDUtNC4yMi0uMjYtNS4yNywxLjU4LTEuMDUsMS44NS0uMjYsNC4yMiwxLjU4LDUuMjcsMS44NSwxLjA1LDQuMjIuMjYsNS4yNy0xLjU4LDEuMDYtMS44NS4yNi00LjIyLTEuNTgtNS4yN1oiLz48cGF0aCBjbGFzcz0iY2xzLTEiIGQ9Im0xNDUuOTEsMTI0LjE2YzAsNi4yNi01LjA4LDExLjMzLTExLjMzLDExLjMzcy0xMS4zNC01LjA4LTExLjM0LTExLjMzLDUuMDgtMTEuMzQsMTEuMzQtMTEuMzQsMTEuMzMsNS4wOCwxMS4zMywxMS4zNCIvPjxwYXRoIGNsYXNzPSJjbHMtMSIgZD0ibTExOS44MSwxMjMuMzdjMCw0LjUxLTMuNjYsOC4xNy04LjE3LDguMTdzLTguMTctMy42Ni04LjE3LTguMTcsMy42Ni04LjE3LDguMTctOC4xNyw4LjE3LDMuNjYsOC4xNyw4LjE3Ii8+PHBhdGggY2xhc3M9ImNscy0xIiBkPSJtOTIuODUsMTU3LjQxYzMuMjMsMS45Nyw0LjI2LDYuMTksMi4yOSw5LjQyLTEuOTcsMy4yMy02LjE5LDQuMjYtOS40MiwyLjI5LTMuMjMtMS45Ny00LjI2LTYuMTktMi4yOS05LjQyLDEuOTctMy4yMyw2LjE5LTQuMjYsOS40Mi0yLjI5Ii8+PHBhdGggY2xhc3M9ImNscy0xIiBkPSJtNzkuNDgsMTcwLjAzYzAsMi4xOC0xLjc3LDMuOTYtMy45NiwzLjk2cy0zLjk1LTEuNzctMy45NS0zLjk2LDEuNzctMy45NSwzLjk1LTMuOTUsMy45NiwxLjc3LDMuOTYsMy45NSIvPjxwYXRoIGNsYXNzPSJjbHMtMSIgZD0ibTEwOS4wNywxMzUuNjNjNS40OCwzLjAzLDcuNDcsOS45Miw0LjQ0LDE1LjQtMy4wMyw1LjQ4LTkuOTIsNy40Ny0xNS40LDQuNDQtNS40OC0zLjAyLTcuNDctOS45Mi00LjQ0LTE1LjQsMy4wMy01LjQ4LDkuOTItNy40NywxNS40LTQuNDQiLz48cGF0aCBjbGFzcz0iY2xzLTEiIGQ9Im05Mi44LDEyNy41NWMyLjA2LDQuMDIuNDcsOC45NC0zLjU1LDExLTQuMDIsMi4wNi04Ljk0LjQ2LTExLTMuNTUtMi4wNi00LjAyLS40Ni04Ljk0LDMuNTUtMTEsNC4wMi0yLjA2LDguOTQtLjQ3LDExLDMuNTUiLz48cGF0aCBjbGFzcz0iY2xzLTEiIGQ9Im01MC43NSwxNTAuNTJjMCwzLjc4LTMuMDcsNi44NS02Ljg1LDYuODVzLTYuODUtMy4wNy02Ljg1LTYuODUsMy4wNy02Ljg1LDYuODUtNi44NSw2Ljg1LDMuMDcsNi44NSw2Ljg1Ii8+PHBhdGggY2xhc3M9ImNscy0xIiBkPSJtMzIuODIsMTQ3Ljg5YzAsMi4xOC0xLjc3LDMuOTYtMy45NSwzLjk2cy0zLjk2LTEuNzctMy45Ni0zLjk2LDEuNzctMy45NSwzLjk2LTMuOTUsMy45NSwxLjc3LDMuOTUsMy45NSIvPjxwYXRoIGNsYXNzPSJjbHMtMSIgZD0ibTY3LjczLDEzMy4wOGM2LjE5Ljk1LDEwLjQ0LDYuNzMsOS40OSwxMi45Mi0uOTUsNi4xOS02LjczLDEwLjQ0LTEyLjkyLDkuNDktNi4xOS0uOTUtMTAuNDQtNi43My05LjQ5LTEyLjkyLjk1LTYuMTksNi43My0xMC40NCwxMi45Mi05LjQ5Ii8+PHBhdGggY2xhc3M9ImNscy0xIiBkPSJtNjcuODgsMTIyLjMyYzAsNC41MS0zLjY2LDguMTctOC4xNyw4LjE3cy04LjE3LTMuNjYtOC4xNy04LjE3LDMuNjYtOC4xNyw4LjE3LTguMTcsOC4xNywzLjY2LDguMTcsOC4xNyIvPjxwYXRoIGNsYXNzPSJjbHMtMSIgZD0ibTIxLjc1LDExMy4zNWMwLDMuNzktMy4wNyw2Ljg1LTYuODUsNi44NXMtNi44NS0zLjA3LTYuODUtNi44NSwzLjA3LTYuODUsNi44NS02Ljg1LDYuODUsMy4wNyw2Ljg1LDYuODUiLz48cGF0aCBjbGFzcz0iY2xzLTEiIGQ9Im02LjcyLDEwNS4xOGMxLjU4LTEuNTgsMS41OC0zLjk1LDAtNS41NC0xLjU4LTEuNTgtMy45NS0xLjU4LTUuNTQsMC0xLjU4LDEuNTgtMS41OCwzLjk1LDAsNS41NCwxLjU4LDEuMzIsMy45NiwxLjMyLDUuNTQsMFoiLz48cGF0aCBjbGFzcz0iY2xzLTEiIGQ9Im00Ny4zMiwxMjEuNTNjMCw2LjI2LTUuMDgsMTEuMzQtMTEuMzQsMTEuMzRzLTExLjMzLTUuMDgtMTEuMzMtMTEuMzQsNS4wOC0xMS4zMywxMS4zMy0xMS4zMywxMS4zNCw1LjA4LDExLjM0LDExLjMzIi8+PHBhdGggY2xhc3M9ImNscy0xIiBkPSJtNTIuMDYsOTkuOTFjMCw0LjUxLTMuNjYsOC4xNy04LjE3LDguMTdzLTguMTctMy42Ni04LjE3LTguMTcsMy42Ni04LjE3LDguMTctOC4xNyw4LjE3LDMuNjYsOC4xNyw4LjE3Ii8+PHBhdGggY2xhc3M9ImNscy0xIiBkPSJtMTkuOSw2Ni4xN2MwLDMuNzgtMy4wNyw2Ljg1LTYuODUsNi44NXMtNi44NS0zLjA3LTYuODUtNi44NSwzLjA3LTYuODUsNi44NS02Ljg1LDYuODUsMy4wNyw2Ljg1LDYuODUiLz48cGF0aCBjbGFzcz0iY2xzLTEiIGQ9Im0xMS4yLDU0LjgzYzIuMTEtLjI2LDMuNDMtMi4zNywzLjE2LTQuNDgtLjI2LTIuMTEtMi4zNy0zLjQzLTQuNDgtMy4xNi0yLjExLjI2LTMuNDMsMi4zNy0zLjE2LDQuNDguNTMsMi4xMSwyLjM3LDMuNDMsNC40OCwzLjE2WiIvPjxwYXRoIGNsYXNzPSJjbHMtMSIgZD0ibTM2Ljc3LDg1LjQxYzAsNi4yNi01LjA4LDExLjMzLTExLjMzLDExLjMzcy0xMS4zNC01LjA4LTExLjM0LTExLjMzLDUuMDgtMTEuMzQsMTEuMzQtMTEuMzQsMTEuMzMsNS4wOCwxMS4zMywxMS4zNCIvPjxwYXRoIGNsYXNzPSJjbHMtMSIgZD0ibTUyLjU5LDcyLjQ5YzAsNC41MS0zLjY2LDguMTctOC4xNyw4LjE3cy04LjE3LTMuNjYtOC4xNy04LjE3LDMuNjYtOC4xNyw4LjE3LTguMTcsOC4xNywzLjY2LDguMTcsOC4xNyIvPjxwYXRoIGNsYXNzPSJjbHMtMSIgZD0ibTQyLjA0LDIwLjg4YzMuNDQsMS41OCw0Ljk1LDUuNjUsMy4zNyw5LjA5LTEuNTgsMy40NC01LjY0LDQuOTUtOS4wOSwzLjM4LTMuNDQtMS41OC00Ljk1LTUuNjUtMy4zOC05LjA5LDEuNTgtMy40NCw1LjY1LTQuOTUsOS4wOS0zLjM3Ii8+PHBhdGggY2xhc3M9ImNscy0xIiBkPSJtNDQuNDIsMTYuODdjMS44NSwxLjA1LDQuMjIuMjYsNS4yNy0xLjU4LDEuMDUtMS44NS4yNi00LjIyLTEuNTgtNS4yNy0xLjg1LTEuMDUtNC4yMi0uMjYtNS4yNywxLjU4LTEuMDYsMS44NS0uMjYsNC4yMiwxLjU4LDUuMjdaIi8+PHBhdGggY2xhc3M9ImNscy0xIiBkPSJtNDkuNDMsNDkuODJjMCw2LjI2LTUuMDgsMTEuMzQtMTEuMzQsMTEuMzRzLTExLjMzLTUuMDgtMTEuMzMtMTEuMzQsNS4wOC0xMS4zMywxMS4zMy0xMS4zMywxMS4zNCw1LjA4LDExLjM0LDExLjMzIi8+PHBhdGggY2xhc3M9ImNscy0xIiBkPSJtNjYuNzYsNDQuODFjMy4xOSwzLjE5LDMuMTksOC4zNiwwLDExLjU2LTMuMTksMy4xOS04LjM3LDMuMTktMTEuNTYsMC0zLjE5LTMuMTktMy4xOS04LjM3LDAtMTEuNTYsMy4xOS0zLjE5LDguMzYtMy4xOSwxMS41NiwwIi8+PHBhdGggY2xhc3M9ImNscy0xIiBkPSJtODUuMDEsNC4wNWMzLjY4LjksNS45Myw0LjYsNS4wNCw4LjI4LS45LDMuNjgtNC42LDUuOTMtOC4yOCw1LjA0LTMuNjgtLjktNS45My00LjYtNS4wNC04LjI4LjktMy42OCw0LjYtNS45Myw4LjI4LTUuMDQiLz48cGF0aCBjbGFzcz0iY2xzLTEiIGQ9Im0xMDEuMSwzLjk2YzAsMi4xOC0xLjc3LDMuOTUtMy45NSwzLjk1cy0zLjk2LTEuNzctMy45Ni0zLjk1LDEuNzctMy45NiwzLjk2LTMuOTYsMy45NSwxLjc3LDMuOTUsMy45NiIvPjxwYXRoIGNsYXNzPSJjbHMtMSIgZD0ibTc0LjgzLDE4LjY4YzUuMzksMy4xOSw3LjE3LDEwLjE0LDMuOTksMTUuNTMtMy4xOCw1LjM5LTEwLjE0LDcuMTctMTUuNTIsMy45OS01LjM5LTMuMTktNy4xNy0xMC4xNC0zLjk5LTE1LjUzLDMuMTgtNS4zOSwxMC4xNC03LjE3LDE1LjUyLTMuOTkiLz48L3N2Zz4=', 99); });
            add_action('admin_init', array($this, 'GetSettings' ));
            add_action('init', function() {
                load_plugin_textdomain('product-feeder', false, 'product-feeder/languages');
                foreach (wc_get_order_statuses() as $value => $label) {
                    $value = str_replace("wc-", "", $value); //REMOVE wc- from order-value
                    $value = str_replace("-", "_", $value); //REPLACE - with _ in order-value
                    add_filter('woocommerce_email_recipient_customer_'.$value.'_order', array($this, 'prevent_customer_emails_from_being_sent'), 10, 2);
                }
            });
            add_action('admin_notices', array($this, 'ShowNotices'));
            add_action('rest_api_init', array($this, 'RegisterAPIRoutes'));
            add_filter("plugin_action_links_".PRODUCT_FEEDER_PLUGIN_BASENAME, array($this, 'plugin_add_settings_link'));
        }

        public function plugin_add_settings_link($links) {
            $settings_link = '<a href="admin.php?page=product-feeder-settings">'.esc_html(__('Settings', 'product-feeder')).'</a>';
            array_unshift($links, $settings_link);
            return $links;
        }

        public function GetSettings() {
            add_settings_section('product-feeder-credentials', __('Source credentials', 'product-feeder')."<br><div class='product-feeder-sub-header'>".__('Use the source credentials shown below while creating the Product Feeder source', 'product-feeder')."</div>", null, 'product-feeder-credentials');
            add_settings_field('product-feeder-credentials_url', __('Website URL', 'product-feeder'),  function() { echo esc_url(home_url()); }, 'product-feeder-credentials', 'product-feeder-credentials');
            add_settings_field('product-feeder-credentials_token', __('API Key', 'product-feeder'),  function() { echo esc_html(get_option('product_feeder_api_key')); }, 'product-feeder-credentials', 'product-feeder-credentials');

            add_settings_section('section_product_feeder', "", function() {}, 'product-feeder-settings');
            add_settings_field('product-feeder-order-header', "<div class='product-feeder-settings-header'>".__('Order settings', 'product-feeder')."</div>",  function() {}, 'product-feeder-settings', 'section_product_feeder');
            add_settings_field('product-feeder-default-order-status', __('Order status for new orders', 'product-feeder'),  array($this, 'GetOptions'), 'product-feeder-settings', 'section_product_feeder', array('Type' => 'Default-order-status', 'Optional' => false));
            add_settings_field('product-feeder-accepted-order-statuses', __('Statuses for accepted orders', 'product-feeder'),  array($this, 'GetOptions'), 'product-feeder-settings', 'section_product_feeder', array('Type' => 'Accepted-order-statuses', 'Optional' => false));
            add_settings_field('product-feeder-rejected-order-statuses', __('Statuses for rejected orders', 'product-feeder'),  array($this, 'GetOptions'), 'product-feeder-settings', 'section_product_feeder', array('Type' => 'Rejected-order-statuses', 'Optional' => false));
            add_settings_field('product-feeder-shipped-order-statuses', __('Statuses for shipped orders', 'product-feeder'),  array($this, 'GetOptions'), 'product-feeder-settings', 'section_product_feeder', array('Type' => 'Shipped-order-statuses', 'Optional' => false));
            add_settings_field('product-feeder-product-header', "<div class='product-feeder-settings-header'>".__('Product settings', 'product-feeder')."</div>",  function() {}, 'product-feeder-settings', 'section_product_feeder');
            add_settings_field('product-feeder-sizes', __('Size field(s)', 'product-feeder'),  array($this, 'GetOptions'), 'product-feeder-settings', 'section_product_feeder', array('Type' => 'Sizes', 'Optional' => false));
            add_settings_field('product-feeder-brand', __('Brand field(s)', 'product-feeder'),  array($this, 'GetOptions'), 'product-feeder-settings', 'section_product_feeder', array('Type' => 'Brand', 'Optional' => false));
            add_settings_field('product-feeder-ean', __('EAN field(s)', 'product-feeder')."<br><div class='product-feeder-option-optional'>".__('Optional', 'product-feeder')."</div>",  array($this, 'GetOptions'), 'product-feeder-settings', 'section_product_feeder', array('Type' => 'EAN', 'Optional' => true));
            add_settings_field('product-feeder-color', __('Color field(s)', 'product-feeder')."<br><div class='product-feeder-option-optional'>".__('Optional', 'product-feeder')."</div>",  array($this, 'GetOptions'), 'product-feeder-settings', 'section_product_feeder', array('Type' => 'Color', 'Optional' => true));
            add_settings_field('product-feeder-season', __('Season field(s)', 'product-feeder')."<br><div class='product-feeder-option-optional'>".__('Optional', 'product-feeder')."</div>",  array($this, 'GetOptions'), 'product-feeder-settings', 'section_product_feeder', array('Type' => 'Season', 'Optional' => true));
            add_settings_field('product-feeder-gender', __('Gender field(s)', 'product-feeder')."<br><div class='product-feeder-option-optional'>".__('Optional', 'product-feeder')."</div>",  array($this, 'GetOptions'), 'product-feeder-settings', 'section_product_feeder', array('Type' => 'Gender', 'Optional' => true));
            add_settings_field('product-feeder-material', __('Material field(s)', 'product-feeder')."<br><div class='product-feeder-option-optional'>".__('Optional', 'product-feeder')."</div>",  array($this, 'GetOptions'), 'product-feeder-settings', 'section_product_feeder', array('Type' => 'Material', 'Optional' => true));
            add_settings_field('product-feeder-condition', __('Condition field(s)', 'product-feeder')."<br><div class='product-feeder-option-optional'>".__('Optional', 'product-feeder')."</div>",  array($this, 'GetOptions'), 'product-feeder-settings', 'section_product_feeder', array('Type' => 'Condition', 'Optional' => true));
            add_settings_field('product-feeder-delivery-terms', __('Delivery terms field(s)', 'product-feeder')."<br><div class='product-feeder-option-optional'>".__('Optional', 'product-feeder')."</div>",  array($this, 'GetOptions'), 'product-feeder-settings', 'section_product_feeder', array('Type' => 'Delivery-terms', 'Optional' => true));
            add_settings_field('product-feeder-save-header', "<div class='product-feeder-settings-header'></div>",  function() {}, 'product-feeder-settings', 'section_product_feeder');
            add_settings_field('product-feeder-submit-button', '', function() { submit_button(__('Save settings', 'product-feeder')); }, 'product-feeder-settings', 'section_product_feeder', array());

            register_setting('section_product_feeder', 'product-feeder-default-order-status');
            register_setting('section_product_feeder', 'product-feeder-accepted-order-statuses');
            register_setting('section_product_feeder', 'product-feeder-rejected-order-statuses');
            register_setting('section_product_feeder', 'product-feeder-shipped-order-statuses');
            register_setting('section_product_feeder', 'product-feeder-sizes');
            register_setting('section_product_feeder', 'product-feeder-brand');
            register_setting('section_product_feeder', 'product-feeder-ean');
            register_setting('section_product_feeder', 'product-feeder-color');
            register_setting('section_product_feeder', 'product-feeder-season');
            register_setting('section_product_feeder', 'product-feeder-gender');
            register_setting('section_product_feeder', 'product-feeder-material');
            register_setting('section_product_feeder', 'product-feeder-condition');
            register_setting('section_product_feeder', 'product-feeder-delivery-terms');
        }

        public function ShowNotices() {
            if (empty(get_option("product-feeder-sizes"))) { ?><div class="notice notice-error"><p><?php echo esc_html(__('Product Feeder', 'product-feeder')." | ".__('Size field(s) are not selected. This is required to make the plugin function correctly', 'product-feeder')); ?></p></div><?php }
            if (empty(get_option("product-feeder-brand"))) { ?><div class="notice notice-error"><p><?php echo esc_html(__('Product Feeder', 'product-feeder')." | ".__('Brand field is not selected. This is required to make the plugin function correctly', 'product-feeder')); ?></p></div><?php }
            if (empty(get_option("product-feeder-default-order-status"))) { ?><div class="notice notice-error"><p><?php echo esc_html(__('Product Feeder', 'product-feeder')." | ".__('Status for new orders is not selected. This is required to make the plugin function correctly', 'product-feeder')); ?></p></div><?php }
            if (empty(get_option("product-feeder-accepted-order-statuses"))) { ?><div class="notice notice-error"><p><?php echo esc_html(__('Product Feeder', 'product-feeder')." | ".__('Statuses for accepted orders are not selected. This is required to make the plugin function correctly', 'product-feeder')); ?></p></div><?php }
            if (empty(get_option("product-feeder-rejected-order-statuses"))) { ?><div class="notice notice-error"><p><?php echo esc_html(__('Product Feeder', 'product-feeder')." | ".__('Statuses for rejected orders are not selected. This is required to make the plugin function correctly', 'product-feeder')); ?></p></div><?php }
        }

        public function GetSettingsPage() {
            ?>
            <div class='product-feeder-settings-page wrap'>
                <h1><?php echo esc_html(__('Product Feeder', 'product-feeder')); ?></h1>
                <div class="product-feeder-sub-header"><?php echo sprintf(esc_html(__('The Product Feeder plugin enables synchronization with multiple marketplace(s) through %s', 'product-feeder')), '<a href="https://product-feeder.com" target="_blank">product-feeder.com</a>'); ?></div>
                <br>
                <?php
                    settings_fields('product-feeder-credentials');
                    do_settings_sections('product-feeder-credentials');
                ?>
                <form method='post' action='options.php' id="product-feeder-settings">
                    <?php
                        settings_fields('section_product_feeder');
                        do_settings_sections('product-feeder-settings');
                    ?>
                </form>
            </div>
            <?php
        }

        public function GetOptions($Arguments) {
            $Options = array();
            if (isset($Arguments['Type'])) {
                $IsMultiple = false;
                if (in_array($Arguments['Type'], ['Sizes'])) {
                    $IsMultiple = true;
                    if ($this->AttributeTaxonomies === NULL) {
                        $all_attribute_taxonomies = wc_get_attribute_taxonomies();
                        foreach ($all_attribute_taxonomies as $attribute_taxonomy) $this->AttributeTaxonomies[$attribute_taxonomy->attribute_name] = $attribute_taxonomy->attribute_label;
                    }
                    foreach ($this->AttributeTaxonomies as $attribute_name => $attribute_label) $Options[__('Attribute', 'product-feeder')]['pa_'.$attribute_name] = $attribute_label;
                }
                else if (in_array($Arguments['Type'], ['EAN', 'Brand', 'Color', 'Season', 'Gender', 'Material', 'Condition', 'Delivery-terms'])) {
                    $IsMultiple = true;
                    if ($this->AttributeTaxonomies === NULL) {
                        $all_attribute_taxonomies = wc_get_attribute_taxonomies();
                        foreach ($all_attribute_taxonomies as $attribute_taxonomy) $this->AttributeTaxonomies[$attribute_taxonomy->attribute_name] = $attribute_taxonomy->attribute_label;
                    }
                    foreach ($this->AttributeTaxonomies as $attribute_name => $attribute_label) $Options[__('Attribute', 'product-feeder')]['pa_'.$attribute_name] = $attribute_label;
                    foreach (get_object_taxonomies('product') as $attribute) {
                        if (preg_match("/^pa_/", $attribute)) continue;
                        else if (in_array($attribute, ['product_type', 'product_visibility', 'product_tag', 'product_shipping_class'])) continue;
                        $Options[__('Category', 'product-feeder')]['category_'.$attribute] = $attribute;
                    }
                    global $wpdb;
                    $query = $wpdb->prepare("SELECT DISTINCT(`".$wpdb->postmeta."`.meta_key) FROM `".$wpdb->posts."` LEFT JOIN `".$wpdb->postmeta."` ON `".$wpdb->posts."`.ID = `".$wpdb->postmeta."`.post_id WHERE `".$wpdb->posts."`.post_type = %s AND `".$wpdb->postmeta."`.meta_key != ''", 'product_variation');
                    foreach ($wpdb->get_col($query) as $metafield) {
                        if (in_array($metafield, ['_total_sales', '_tax_class', '_backorders', '_sold_individually', '_virtual', '_downloadable', '_download_limit', '_download_expiry', '_stock_status', '_wc_average_rating', '_wc_review_count', '_product_version', '_regular_price', '_thumbnail_id', '_crosssell_ids', '_weight', '_length', '_height'])) continue;
                        $Options[__('Meta field', 'product-feeder')]['meta_'.$metafield] = $metafield;
                    }
                }
                else if (in_array($Arguments['Type'], ['Default-order-status', 'Accepted-order-statuses', 'Rejected-order-statuses', 'Shipped-order-statuses'])) {
                    if (in_array($Arguments['Type'], ['Accepted-order-statuses', 'Rejected-order-statuses', 'Shipped-order-statuses'])) $IsMultiple = true;
                    foreach (wc_get_order_statuses() as $value => $label) {
                        if ($Arguments['Type'] == 'Default-order-status' && in_array($value, ['wc-pending', 'wc-cancelled', 'wc-refunded', 'wc-failed', 'wc-checkout-draft'])) continue;
                        else if ($Arguments['Type'] == 'Accepted-order-statuses' && in_array($value, ['wc-pending', 'wc-cancelled', 'wc-refunded', 'wc-failed', 'wc-checkout-draft'])) continue;
                        else if ($Arguments['Type'] == 'Rejected-order-statuses' && in_array($value, ['wc-pending', 'wc-processing', 'wc-on-hold', 'wc-completed', 'wc-checkout-draft'])) continue;
                        else if ($Arguments['Type'] == 'Shipped-order-statuses' && in_array($value, ['wc-pending', 'wc-cancelled', 'wc-refunded', 'wc-failed', 'wc-checkout-draft'])) continue;
                        $Options[$value] = $label;
                    }
                }
                else wp_die(sprintf(__('Invalid option type: %s', 'product-feeder'), $Arguments['Type']));
            }
            else wp_die(__('Missing option type', 'product-feeder'));
            if (empty($Options)) wp_die(__('Missing options', 'product-feeder'));
            $OptionName = "product-feeder-".strtolower($Arguments['Type']).(($IsMultiple) ? '[]' : '');
            $OptionsSelected = get_option(preg_replace("/\[\]$/", "", $OptionName)); ?>
            <select name="<?php echo esc_html($OptionName); ?>" <?php if ($IsMultiple) { echo ' multiple'; } ?>>
                <option value="" disabled>
                    <?php
                        if ($IsMultiple) echo esc_html(__('Select one or multiple options', 'product-feeder'));
                        else echo esc_html(__('Select an option', 'product-feeder'));
                    ?>
                </option>
                <?php
                    foreach ($Options as $Key => $Values) {
                        if (is_array($Values)) { ?>
                            <optgroup label="<?php echo esc_html($Key); ?>">
                            <?php foreach ($Values as $Value => $Label) {
                                echo "<option value='".esc_html($Value)."'";
                                if ((is_array($OptionsSelected) && in_array($Value, $OptionsSelected)) || $OptionsSelected === $Value) echo ' selected';
                                echo ">".esc_html($Label)."</option>";
                            } ?>
                            </optgroup>
                        <?php }
                        else {
                            $Value = $Key;
                            $Label = $Values;
                            echo "<option value='".esc_html($Value)."'";
                            if ((is_array($OptionsSelected) && in_array($Value, $OptionsSelected)) || $OptionsSelected === $Value) echo ' selected';
                            echo ">".esc_html($Label)."</option>";
                        }
                    }
                ?>
            </select>
        <?php }

        public function RegisterAPIRoutes() {
            register_rest_route( 'v1/product-feeder', '/connect', array(
                'methods' => 'GET',
                'callback' => array($this, 'Connect'),
                'permission_callback' => '__return_true',
            ));
            register_rest_route( 'v1/product-feeder', '/products/list', array(
                'methods' => 'GET',
                'callback' => array($this, 'GetProducts'),
                'permission_callback' => '__return_true',
            ));
            register_rest_route( 'v1/product-feeder', '/products/changes/(?P<Since>\d+)', array(
                'methods' => 'GET',
                'callback' => array($this, 'GetProducts'),
                'permission_callback' => '__return_true',
            ));
            register_rest_route( 'v1/product-feeder', '/orders/create', array(
                'methods' => 'POST',
                'callback' => array($this, 'CreateOrder'),
                'permission_callback' => '__return_true',
            ));
            register_rest_route( 'v1/product-feeder', '/orders/get/(?P<OrderID>\d+)', array(
                'methods' => 'GET',
                'callback' => array($this, 'GetOrder'),
                'permission_callback' => '__return_true',
            ));
            register_rest_route( 'v1/product-feeder', '/orders/modify/(?P<OrderID>\d+)', array(
                'methods' => 'PATCH',
                'callback' => array($this, 'ModifyOrder'),
                'permission_callback' => '__return_true',
            ));
            register_rest_route( 'v1/product-feeder', '/orders/reject/(?P<OrderID>\d+)', array(
                'methods' => 'PATCH',
                'callback' => array($this, 'RejectOrder'),
                'permission_callback' => '__return_true',
            ));
        }

        private function APISuccess($Response = NULL) {
            header("Content-type: application/json");
            if ($Response === NULL) wp_send_json(array("Status" => "Success", 'Version' => PRODUCT_FEEDER_PLUGIN_DATA['Version']));
            else if (is_array($Response)) wp_send_json(array_merge(array("Status" => "Success", 'Version' => PRODUCT_FEEDER_PLUGIN_DATA['Version']), $Response));
            else $this->APIError('Invalid API success response specified!');
        }

        private function APIError($Error, $Code = 400) {
            if ($Code == 0) $Code = 400;
            header("Content-type: application/json");
            if (preg_match("/(200|^4|^5)/", $Code)) http_response_code($Code);
            wp_send_json(array("Status" => "Error", "Error" => $Error, 'Version' => PRODUCT_FEEDER_PLUGIN_DATA['Version']), $Code);
        }

        private function ValidateAPIKey($Request) {
            try {
                if (in_array($Request->get_method(), ['POST', 'PATCH'])) {
                    $body = $Request->get_body();
                    $JSON = @json_decode($body, true);
                    if (json_last_error() == JSON_ERROR_NONE) {
                        if (isset($JSON['API Key'])) $APIKey = $JSON['API Key'];
                    }
                }
                else $APIKey = $Request->get_param('API_Key');
                if (!empty($APIKey)) {
                    $APIKeyLocal = get_option('product_feeder_api_key');
                    if ($APIKeyLocal === false) throw new Exception('Failed to compare API Key', 401);
                    else if ($APIKeyLocal != $APIKey) throw new Exception('Invalid API Key', 401);
                }
                else throw new Exception('Missing API Key', 400);
            }
            catch (Exception $e) {
                $this->APIError($e->getMessage(), $e->getCode());
            }
        }

        public function Connect($Request) {
            $this->ValidateAPIKey($Request);
            if (
                empty(get_option("product-feeder-sizes")) ||
                empty(get_option("product-feeder-brand")) ||
                empty(get_option("product-feeder-default-order-status")) ||
                empty(get_option("product-feeder-accepted-order-statuses")) ||
                empty(get_option("product-feeder-rejected-order-statuses"))
            ) $this->APIError('Invalid settings', 403);
            $this->APISuccess();
        }

        public function GetProducts($Request) {
            $this->ValidateAPIKey($Request);
            $Limit = $Request->get_param('Limit');
            if ($Limit < 0) $Limit = 1;
            else if ($Limit > 100) $Limit = 100;
            $Offset = $Request->get_param('Offset');
            if ($Offset < 0) $Offset = 0;
            $ChangedSince = $Request->get_param('Since');
            $args = array(
                'post_type' => 'product',
                'post_status' => 'publish',
                'posts_per_page' => $Limit,
                'offset' => $Offset,
                'fields' => 'ids',
                'suppress_filters' => true,
            );
            if ($ChangedSince !== NULL) {
                $args['date_query'] = array(
                    'column' => 'post_modified',
                    'after' => date( 'c' , $ChangedSince)
                );
            }
            $results = new WP_Query($args);
            $this->APISuccess(array('Products' => $this->CreateProductList($results->posts)));
        }

        public function CreateOrder($Request) {
            $OrderID = NULL;
            $this->ValidateAPIKey($Request);
            try {
                $Errors = array();
                $body = $Request->get_body();
                $JSON = @json_decode($body, true);
                if (json_last_error() == JSON_ERROR_NONE) {
                    if (isset($JSON['Order'])) {
                        $Order = $JSON['Order'];
                        if (isset($Order['Items']) && is_array($Order['Items']) && count($Order['Items']) > 0) {
                            global $wpdb;
                            $results_order_id = $wpdb->get_results($wpdb->prepare("SELECT post_id FROM `".$wpdb->postmeta."` WHERE meta_key='product_feeder_order' AND meta_value=%s", [$Order['ID']]), 'OBJECT');
                            $results_marketplace = $wpdb->get_results($wpdb->prepare("SELECT post_id FROM `".$wpdb->postmeta."` WHERE meta_key='product_feeder_marketplace' AND meta_value=%s", [$JSON['Marketplace']['Name']]), 'OBJECT');
                            if (isset($results_order_id[0]) && isset($results_order_id[0]->post_id) && isset($results_marketplace[0]) && isset($results_marketplace[0]->post_id) && $results_order_id[0]->post_id == $results_marketplace[0]->post_id) $this->APISuccess(array('OrderID' => $results_order_id[0]->post_id));
                            else {
                                $ItemsToAddToOrder = array();
                                $DefaultOrderStatus = get_option("product-feeder-default-order-status");
                                if (empty($DefaultOrderStatus)) $Errors[] = array('Type' => 'CONFIGURATION ERROR');
                                foreach ($Order['Items'] as $Item) {
                                    $product = wc_get_product($Item['ProductID']);
                                    if ($product instanceof WC_Product) {
                                        if (($product->get_type() == 'simple' && $Item['VariationID'] == $Item['ProductID']) || in_array($Item['VariationID'], $product->get_children())) {
                                            $variation = wc_get_product($Item['VariationID']);
                                            $VATPercentage = ($product->is_taxable()) ? ($product->get_price_including_tax() - $product->get_price_excluding_tax()) / $product->get_price_excluding_tax() : 0;
                                            if ($variation instanceof WC_Product) $ItemsToAddToOrder[] = array('Variation' => $variation, 'Quantity' => $Item['Quantity'], 'Price' => $Item['Price'], 'PriceExclVAT' => number_format($Item['Price'] / (1 + $VATPercentage), 2, ".", ""));
                                            else $Errors[] = array('Type' => 'VARIATION NOT FOUND', 'Variables' => array('Name' => $Item['Name'], 'Variant' => $Item['Variant'], 'ProductID' => $Item['ProductID'], 'VariationID' => $Item['VariationID']));
                                        }
                                        else $Errors[] = array('Type' => 'VARIATION NOT FOUND', 'Variables' => array('Name' => $Item['Name'], 'Variant' => $Item['Variant'], 'ProductID' => $Item['ProductID'], 'VariationID' => $Item['VariationID']));
                                    }
                                    else $Errors[] = array('Type' => 'PRODUCT NOT FOUND', 'Variables' => array('Name' => $Item['Name'], 'Variant' => $Item['Variant'], 'ProductID' => $Item['ProductID'], 'VariationID' => $Item['VariationID']));
                                }
                                if (empty($Errors)) {
                                    if ($order = wc_create_order()) {
                                        $OrderID = $order->get_id();
                                        $order->set_address(array(
                                            'first_name' => $Order['Billing']['Firstname'],
                                            'last_name'  => $Order['Billing']['Lastname'],
                                            'company'    => $Order['Billing']['Company'],
                                            'email'      => $Order['Billing']['Email'],
                                            'phone'      => $Order['Billing']['Phone'],
                                            'address_1'  => $Order['Billing']['Lines'][0].((!isset($Order['Billing']['Lines'][1])) ? rtrim(' '.$Order['Billing']['HouseNumber'].' '.$Order['Billing']['HouseNumberAddition'], ' ') : ''),
                                            'address_2'  => (isset($Order['Billing']['Lines'][1])) ? $Order['Billing']['Lines'][1].rtrim(' '.$Order['Billing']['HouseNumber'].' '.$Order['Billing']['HouseNumberAddition'], ' ') : '',
                                            'city'       => $Order['Billing']['City'],
                                            'state'      => $Order['Billing']['State'],
                                            'postcode'   => $Order['Billing']['Postal'],
                                            'country'    => $Order['Billing']['CountryCode']
                                        ), 'billing');
                                        $order->set_address(array(
                                            'first_name' => $Order['Shipping']['Firstname'],
                                            'last_name'  => $Order['Shipping']['Lastname'],
                                            'company'    => $Order['Shipping']['Company'],
                                            'email'      => $Order['Shipping']['Email'],
                                            'phone'      => $Order['Shipping']['Phone'],
                                            'address_1'  => $Order['Shipping']['Lines'][0].((!isset($Order['Shipping']['Lines'][1])) ? rtrim(' '.$Order['Shipping']['HouseNumber'].' '.$Order['Shipping']['HouseNumberAddition'], ' ') : ''),
                                            'address_2'  => (isset($Order['Shipping']['Lines'][1])) ? $Order['Shipping']['Lines'][1].rtrim(' '.$Order['Shipping']['HouseNumber'].' '.$Order['Shipping']['HouseNumberAddition'], ' ') : '',
                                            'city'       => $Order['Shipping']['City'],
                                            'state'      => $Order['Shipping']['State'],
                                            'postcode'   => $Order['Shipping']['Postal'],
                                            'country'    => $Order['Shipping']['CountryCode']
                                        ), 'shipping');
                                        foreach ($ItemsToAddToOrder as $ItemToAddToOrder) {
                                            $order->add_product($ItemToAddToOrder['Variation'], $ItemToAddToOrder['Quantity'], array(
                                                'subtotal' => $ItemToAddToOrder['PriceExclVAT'] * $ItemToAddToOrder['Quantity'],
                                                'total' => $ItemToAddToOrder['PriceExclVAT'] * $ItemToAddToOrder['Quantity']
                                            ));
                                        }
                                        $order->calculate_totals();
                                        $order->add_order_note(sprintf(__('Order %s from marketplace %s with a total price of %s is created by Product Feeder through channel %d - %s', 'product-feeder'), $Order['ID'], $JSON['Marketplace']['Name'], strip_tags(wc_price($JSON['Order']['Price']['Amount'], array('currency' => $JSON['Order']['Price']['Currency']))), $JSON['Channel']['ID'], $JSON['Channel']['Name']));
                                        $order->add_meta_data('_wc_order_attribution_source_type', 'utm');
                                        $order->add_meta_data('_wc_order_attribution_utm_source', $JSON['Marketplace']['Name'].' - Product Feeder');
                                        update_post_meta($OrderID, 'product_feeder_order', $Order['ID']);
                                        update_post_meta($OrderID, 'product_feeder_marketplace', $JSON['Marketplace']['Name']);
                                        $order->update_status($DefaultOrderStatus, '', true);
                                        $order->save();
                                        $this->APISuccess(array('OrderID' => $OrderID));
                                    }
                                    else throw new Exception('Failed to create order template');
                                }
                                else $this->APIError($Errors);
                            }
                        }
                        else throw new Exception('Incomplete ordered items information');
                    }
                    else throw new Exception('MISSING ORDER INFORMATION');
                }
                else throw new Exception('Failed to parse body');
            }
            catch (Exception $e) {
                if ($OrderID !== NULL) wp_delete_post($OrderID, true);
                $Message = $e->getMessage();
                if (is_array($Message)) $this->APIError(array('Reasons' => $Message));
                else $this->APIError($Message, $e->getCode());
            }
        }

        public function GetOrder($Request) {
            $this->ValidateAPIKey($Request);
            try {
                $OrderID = $Request->get_param('OrderID');
                $order = wc_get_order($OrderID);
                if ($order instanceof WC_Order) {
                    $OrderStatus = NULL;
                    $OrderItems = array();
                    $WCStatus = 'wc-'.$order->get_status();
                    $AcceptedOrderStatuses = get_option("product-feeder-accepted-order-statuses");
                    $RejectedOrderStatuses = get_option("product-feeder-rejected-order-statuses");
                    $ShippedOrderStatuses = get_option("product-feeder-shipped-order-statuses");
                    if (is_array($ShippedOrderStatuses) && in_array($WCStatus, $ShippedOrderStatuses)) $OrderStatus = 'Shipped';
                    else if (is_array($AcceptedOrderStatuses) && in_array($WCStatus, $AcceptedOrderStatuses)) $OrderStatus = 'Accepted';
                    else if (is_array($RejectedOrderStatuses) && in_array($WCStatus, $RejectedOrderStatuses)) $OrderStatus = 'Rejected';
                    else if (get_post_status($OrderID) === 'trash') $OrderStatus = 'Rejected';
                    foreach ($order->get_items() as $item) {
                        $OrderItems[] = array(
                            'ProductID' => $item->get_product_id(),
                            'VariationID' => ($item->get_variation_id() == 0) ? $item->get_product_id() : $item->get_variation_id(),
                            'Quantity' => $item->get_quantity(),
                        );
                    }
                    $this->APISuccess(array('Order' => array('Status' => $OrderStatus, 'Items' => $OrderItems)));
                }
                else throw new Exception('SOURCE ORDER NOT FOUND');
            }
            catch (Exception $e) {
                $this->APIError($e->getMessage(), $e->getCode());
            }
        }

        public function ModifyOrder($Request) {
            $this->ValidateAPIKey($Request);
            try {
                $OrderID = $Request->get_param('OrderID');
                $order = wc_get_order($OrderID);
                if ($order instanceof WC_Order) {
                    $body = $Request->get_body();
                    $JSON = @json_decode($body, true);
                    if (json_last_error() == JSON_ERROR_NONE) {
                        if (isset($JSON['ItemsToBeRejected']) && isset($JSON['Marketplace']) && isset($JSON['Order']) && isset($JSON['Channel'])) {
                            $OrderIsModified = false;
                            $ItemIDsWithNoRemainingQuantity = array();
                            foreach ($order->get_items() as $item_id => $item) {
                                $item->delete_meta_data('_reduced_stock');
                                foreach ($JSON['ItemsToBeRejected'] as $ItemToBeRejected) {
                                    $ProductID = $item->get_product_id();
                                    $VariationID = ($item->get_variation_id() == 0) ? $item->get_product_id() : $item->get_variation_id();
                                    if ($ItemToBeRejected['Item']['Product']['ID'] == $ProductID && $ItemToBeRejected['Item']['Variation']['ID'] == $VariationID) {
                                        $QuantityCurrent = $item->get_quantity();
                                        $QuantityRemaining = $ItemToBeRejected['Item']['Quantities']['Remaining'] + $ItemToBeRejected['Item']['Quantities']['Accepted'];
                                        if ($QuantityRemaining < $QuantityCurrent) {
                                            $OrderIsModified = true;
                                            $item->set_quantity($QuantityRemaining);
                                            if ($QuantityRemaining == 0) {
                                                $ItemIDsWithNoRemainingQuantity[] = $item_id;
                                                $item->update_meta_data('_reduced_stock', $QuantityCurrent);
                                            }
                                            else $item->update_meta_data('_reduced_stock', $QuantityCurrent - $QuantityRemaining);
                                        }
                                        break;
                                    }
                                }
                            }
                            if ($OrderIsModified) {
                                $order->save(); //SAVE THE ORDER. OTHERWISE THE STOCK CHANGES NOTES ARE NOT SET PROPERLY
                                wc_increase_stock_levels($OrderID);
                                foreach ($ItemIDsWithNoRemainingQuantity as $item_id) wc_delete_order_item($item_id);
                                $order = wc_get_order($OrderID); //REFRESH THE ORDER. OTHERWISE THE CALCULATE TOTALS DOESN'T WORK
                                $order->calculate_totals();
                                $order->add_order_note(sprintf(__('Changed order %s from marketplace %s with a total price of %s is synchronized by Product Feeder through channel %d - %s', 'product-feeder'), $OrderID, $JSON['Marketplace']['Name'], strip_tags(wc_price($JSON['Order']['Price']['Amount'], array('currency' => $JSON['Order']['Price']['Currency']))), $JSON['Channel']['ID'], $JSON['Channel']['Name']));
                                $order->save();
                            }
                            $this->APISuccess();
                        }
                        else throw new Exception('MISSING ORDER INFORMATION');
                    }
                    else throw new Exception('Failed to parse body');
                }
                else throw new Exception('SOURCE ORDER NOT FOUND');
            }
            catch (Exception $e) {
                $this->APIError($e->getMessage(), $e->getCode());
            }
        }

        public function RejectOrder($Request) {
            $this->ValidateAPIKey($Request);
            try {
                $OrderID = $Request->get_param('OrderID');
                $order = wc_get_order($OrderID);
                if ($order instanceof WC_Order) {
                    $body = $Request->get_body();
                    $JSON = @json_decode($body, true);
                    if (json_last_error() == JSON_ERROR_NONE) {
                        if (isset($JSON['Marketplace']) && isset($JSON['Channel'])) {
                            $RejectedOrderStatuses = get_option("product-feeder-rejected-order-statuses");
                            if (in_array('wc-cancelled', $RejectedOrderStatuses) || empty($RejectedOrderStatuses)) $order->update_status('wc-cancelled', '', true);
                            else $order->update_status($RejectedOrderStatuses[0], '', true);
                            $order->add_order_note(sprintf(__('Rejected order %s from marketplace %s is synchronized by Product Feeder through channel %d - %s', 'product-feeder'), $OrderID, $JSON['Marketplace']['Name'], $JSON['Channel']['ID'], $JSON['Channel']['Name']));
                            $order->save();
                            $this->APISuccess();
                        }
                        else throw new Exception('MISSING ORDER INFORMATION');
                    }
                    else throw new Exception('Failed to parse body');
                }
                else $this->APISuccess(); //CUSTOMER HAS DELETED THE ORDER?
            }
            catch (Exception $e) {
                $this->APIError($e->getMessage(), $e->getCode());
            }
        }

        private function GetPostMeta($ID, $Name, $Single = true) {
            if (is_string($Name) && !empty($Name)) {
                $Value = get_post_meta($ID, $Name, $Single);
                if ($Value !== false) return $Value;
            }
            return '';
        }

        private function GetCustomFields($Product, $type) {
            $CustomFields = array();
            if ($this->AttributeTaxonomies === NULL) {
                $all_attribute_taxonomies = wc_get_attribute_taxonomies();
                foreach ($all_attribute_taxonomies as $attribute_taxonomy) $this->AttributeTaxonomies[$attribute_taxonomy->attribute_name] = $attribute_taxonomy->attribute_label;
            }
            $product_meta = get_post_meta($Product->get_id());
            foreach ($product_meta as $meta_key => $meta_value) {
                if (!empty($meta_value[0])) {
                    if ($meta_key == '_product_attributes') {
                        foreach ($Product->get_attributes() as $attr_name => $attr_data) {
                            $attr_data = $attr_data->get_data();
                            if (!empty($attr_data['options'])) {
                                $attr_name_short = str_replace('pa_', '', $attr_name);
                                if (isset($this->AttributeTaxonomies[$attr_name_short])) {
                                    if (empty($CustomFields['Attributes'])) $CustomFields['Attributes'] = array();
                                    $CustomFields['Attributes'][$this->AttributeTaxonomies[$attr_name_short]] = array();
                                    foreach ($attr_data['options'] as $term_id) {
                                        if (!isset($this->AttributeValues[$term_id])) {
                                            $Term = get_term($term_id);
                                            if (!is_wp_error($Term) && isset($Term->name)) $this->AttributeValues[$term_id] = $Term->name;
                                        }
                                        if (isset($this->AttributeValues[$term_id])) $CustomFields['Attributes'][$this->AttributeTaxonomies[$attr_name_short]][] = $this->AttributeValues[$term_id];
                                    }
                                }
                            }
                        }
                    }
                    else {
                        if (empty($CustomFields['Meta'])) $CustomFields['Meta'] = array();
                        $CustomFields['Meta'][$meta_key] = $meta_value[0];
                    }
                }
            }
            if ($this->WPMLActive) {
                if ($type == 'product') {
                    $LanguageDetails = apply_filters('wpml_post_language_details', NULL, $Product->get_id());
                    if (isset($LanguageDetails['language_code'])) {
                        foreach ($LanguageDetails as $WPMLKey => $WPMLValue) {
                            if (!is_array($WPMLValue) && !is_object($WPMLValue)) $CustomFields['WPML'][$WPMLKey] = $WPMLValue;
                        }
                    }
                }
            }
            return $CustomFields;
        }

        private function CreateProductList($ProductIDs) {
            $Products = array();
            try {
                $Options = array();
                $Options['Sizes'] = get_option('product-feeder-sizes');
                $Options['Brand'] = get_option('product-feeder-brand');
                $Options['EAN'] = get_option('product-feeder-ean');
                $Options['Color'] = get_option('product-feeder-color');
                $Options['Season'] = get_option('product-feeder-season');
                $Options['Gender'] = get_option('product-feeder-gender');
                $Options['Material'] = get_option('product-feeder-material');
                $Options['Condition'] = get_option('product-feeder-condition');
                $Options['DeliveryTerms'] = get_option('product-feeder-delivery-terms');
                foreach (['Brand', 'EAN', 'Color', 'Season', 'Gender', 'Material', 'Condition', 'DeliveryTerms'] as $Type) {
                    //FIX FOR OLDER VERSIONS WITH NON MULTIPLE SAVED OPTIONS
                    if (is_string($Options[$Type])) $Options[$Type] = array($Options[$Type]);
                }
                $Currency = get_woocommerce_currency();
                foreach ($ProductIDs as $ProductID) {
                    $Product = wc_get_product($ProductID);
                    if (isset($Product->id)) {
                        $ProductEAN = '';
                        foreach ($Options['EAN'] as $OptionEAN) {
                            $ProductEAN = $this->GetPostMeta($ProductID, $OptionEAN);
                            if ($ProductEAN != '') break;
                        }
                        $Brand = '';
                        foreach ($Options['Brand'] as $OptionBrand) {
                            $Brand = $this->GetPostMeta($ProductID, $OptionBrand);
                            if ($Brand != '') break;
                        }
                        $Season = '';
                        foreach ($Options['Season'] as $OptionSeason) {
                            $Season = $this->GetPostMeta($ProductID, $OptionSeason);
                            if ($Season != '') break;
                        }
                        $Permalink = get_permalink($ProductID);
                        if ($Permalink === false) throw new Exception('Failed to determine permalink for product-id: '.$ProductID);
                        $PermalinkHost = parse_url($Permalink, PHP_URL_HOST);
                        if ($PermalinkHost === NULL) throw new Exception('Failed to parse permalink for product-id: '.$ProductID);
                        $ProductData = array(
                            "ID" => $ProductID,
                            "SKU" => $this->GetPostMeta($ProductID, '_sku'),
                            "EAN" => $ProductEAN,
                            "URL" => $Permalink,
                            "Title" => $Product->get_title(),
                            "Brand" => $Brand,
                            "Season" => $Season,
                            "Description" => array(
                                'Short' => $Product->get_short_description(),
                                'Long' => $Product->get_description(),
                            ),
                            "Images" => array(),
                            "Categories" => $this->GetCategoryPaths($ProductID),
                            "Variations" => array(),
                            "CustomFields" => $this->GetCustomFields($Product, 'product'),
                            "Updated" => get_the_modified_date('Y-m-d\TH:i:sT', $ProductID),
                        );
                        $ImageLink = get_the_post_thumbnail_url($ProductID);
                        if ($ImageLink !== false) {
                            $ImageLink = preg_replace("#^(".preg_quote(parse_url($ImageLink, PHP_URL_SCHEME), "#")."://)".preg_quote(parse_url($ImageLink, PHP_URL_HOST), "#")."#", "$1".$PermalinkHost, $ImageLink);
                            if (parse_url($ImageLink, PHP_URL_HOST) !== NULL && $PermalinkHost != parse_url($ImageLink, PHP_URL_HOST)) $ImageLink = preg_replace("#^(".preg_quote(parse_url($ImageLink, PHP_URL_SCHEME), "#")."://)".preg_quote(parse_url($ImageLink, PHP_URL_HOST), "#")."#", "$1".$PermalinkHost, $ImageLink);
                            $ProductData['Images'][] = $ImageLink;
                        }
                        foreach ($Product->get_gallery_image_ids() as $ImageID) {
                            $ImageLink = wp_get_attachment_url($ImageID);
                            if ($ImageLink !== false) {
                                if (parse_url($ImageLink, PHP_URL_HOST) !== NULL && $PermalinkHost != parse_url($ImageLink, PHP_URL_HOST)) $ImageLink = preg_replace("#^(".preg_quote(parse_url($ImageLink, PHP_URL_SCHEME), "#")."://)".preg_quote(parse_url($ImageLink, PHP_URL_HOST), "#")."#", "$1".$PermalinkHost, $ImageLink);
                                $ProductData['Images'][] = $ImageLink;
                            }
                        }
                        $ProductSize = '';
                        $ProductColor = '';
                        $ProductGender = '';
                        $ProductMaterial = '';
                        $ProductCondition = '';
                        $ProductDeliveryTerms = '';
                        foreach ($Product->get_attributes() as $attribute) {
                            $Terms = $attribute->get_terms();
                            if (!empty($Terms)) {
                                $Key = $attribute->get_taxonomy();
                                foreach ($Terms as $Term) {
                                    if (in_array($Key, $Options['Sizes'])) {
                                        if (empty($ProductSize)) $ProductSize = $Term->name;
                                        else $ProductSize .= " ".$Term->name;
                                    }
                                    if (in_array($Key, $Options['Brand']) && empty($ProductData['Brand'])) $ProductData['Brand'] = $Term->name;
                                    if (in_array($Key, $Options['Color']) && empty($ProductColor)) $ProductColor = $Term->name;
                                    if (in_array($Key, $Options['Season']) && empty($ProductData['Season'])) $ProductData['Season'] = $Term->name;
                                    if (in_array($Key, $Options['Gender']) && empty($ProductGender)) $ProductGender = $Term->name;
                                    if (in_array($Key, $Options['Material']) && empty($ProductMaterial)) $ProductMaterial = $Term->name;
                                    if (in_array($Key, $Options['Condition']) && empty($ProductCondition)) $ProductCondition = $Term->name;
                                    if (in_array($Key, $Options['DeliveryTerms']) && empty($ProductDeliveryTerms)) $ProductDeliveryTerms = $Term->name;
                                }
                            }
                        }
                        foreach (array_keys($Options) as $Key) {
                            $OptionValues = $Options[$Key];
                            if (!is_array($OptionValues)) $OptionValues = array($OptionValues);
                            foreach ($OptionValues as $OptionValue) {
                                $Terms = wp_get_post_terms($ProductID, preg_replace("/^category_/", "", $OptionValue));
                                if (!empty($Terms)) {
                                    $Terms = wp_list_pluck($Terms, 'name');
                                    if (!empty($Terms)) {
                                        $Value = implode(' ', $Terms);
                                        if (in_array($OptionValue, $Options['Brand']) && empty($ProductData['Brand'])) $ProductData['Brand'] = $Value;
                                        if (in_array($OptionValue, $Options['Color']) && empty($ProductColor)) $ProductColor = $Value;
                                        if (in_array($OptionValue, $Options['Season']) && empty($ProductData['Season'])) $ProductData['Season'] = $Value;
                                        if (in_array($OptionValue, $Options['Gender']) && empty($ProductGender)) $ProductGender = $Value;
                                        if (in_array($OptionValue, $Options['Material']) && empty($ProductMaterial)) $ProductMaterial = $Value;
                                        if (in_array($OptionValue, $Options['Condition']) && empty($ProductCondition)) $ProductCondition = $Value;
                                        if (in_array($OptionValue, $Options['DeliveryTerms']) && empty($ProductDeliveryTerms)) $ProductDeliveryTerms = $Value;
                                    }
                                }
                            }
                        }
                        foreach ($Product->get_meta_data() as $MetaField) {
                            $MetaField = $MetaField->get_data();
                            $Key = 'meta_'.$MetaField['key'];
                            if (in_array($Key, $Options['Brand']) && empty($ProductData['Brand'])) $ProductData['Brand'] = $MetaField['value'];
                            if (in_array($Key, $Options['Color']) && empty($ProductColor)) $ProductColor = $MetaField['value'];
                            if (in_array($Key, $Options['Season']) && empty($ProductData['Season'])) $ProductData['Season'] = $MetaField['value'];
                            if (in_array($Key, $Options['Gender']) && empty($ProductGender)) $ProductGender = $MetaField['value'];
                            if (in_array($Key, $Options['Material']) && empty($ProductMaterial)) $ProductMaterial = $MetaField['value'];
                            if (in_array($Key, $Options['Condition']) && empty($ProductCondition)) $ProductCondition = $MetaField['value'];
                            if (in_array($Key, $Options['DeliveryTerms']) && empty($ProductDeliveryTerms)) $ProductDeliveryTerms = $MetaField['value'];
                        }
                        if ($Product->get_type() == 'variable') {
                            $Variations = $Product->get_children();
                            foreach ($Variations as $VariationID) {
                                $Variation = wc_get_product($VariationID);
                                if (isset($Variation->id)) {
                                    $variation_data = $Variation->get_data();
                                    $VariationPermalink = get_permalink($VariationID);
                                    if ($VariationPermalink === false) $VariationPermalink = $Permalink;
                                    else {
                                        $PermalinkHost = parse_url($VariationPermalink, PHP_URL_HOST);
                                        if ($PermalinkHost === NULL) throw new Exception('Failed to parse permalink for variation-id: '.$VariationID);
                                    }
                                    $EAN = '';
                                    foreach ($Options['EAN'] as $OptionEAN) {
                                        $EAN = $this->GetPostMeta($VariationID, $OptionEAN);
                                        if ($EAN != '') break;
                                    }
                                    $ImageLinks = array();
                                    $ImageLink = get_the_post_thumbnail_url($VariationID);
                                    if ($ImageLink !== false) {
                                        if (parse_url($ImageLink, PHP_URL_HOST) !== NULL && $PermalinkHost != parse_url($ImageLink, PHP_URL_HOST)) $ImageLink = preg_replace("#^(".preg_quote(parse_url($ImageLink, PHP_URL_SCHEME), "#")."://)".preg_quote(parse_url($ImageLink, PHP_URL_HOST), "#")."#", "$1".$PermalinkHost, $ImageLink);
                                        $ImageLinks[] = $ImageLink;
                                    }
                                    $AdditionalImages = $this->GetPostMeta($VariationID, '_wc_additional_variation_images');
                                    if (!empty($AdditionalImages)) {
                                        $AdditionalImages = preg_split("/,/", $AdditionalImages, -1, PREG_SPLIT_NO_EMPTY);
                                        foreach ($AdditionalImages as $AdditionalImage) {
                                            $ImageLink = wp_get_attachment_url($AdditionalImage);
                                            if ($ImageLink !== false) {
                                                if (parse_url($ImageLink, PHP_URL_HOST) !== NULL && $PermalinkHost != parse_url($ImageLink, PHP_URL_HOST)) $ImageLink = preg_replace("#^(".preg_quote(parse_url($ImageLink, PHP_URL_SCHEME), "#")."://)".preg_quote(parse_url($ImageLink, PHP_URL_HOST), "#")."#", "$1".$PermalinkHost, $ImageLink);
                                                $ImageLinks[] = $ImageLink;
                                            }
                                        }
                                    }
                                    $VariationData = array(
                                        "ID" => $VariationID,
                                        "SKU" => $this->GetPostMeta($VariationID, '_sku'),
                                        "EAN" => $EAN,
                                        "Color" => '',
                                        "Size" => '',
                                        "Gender" => '',
                                        "Material" => '',
                                        "Condition" => '',
                                        "Delivery" => '',
                                        "URL" => $VariationPermalink,
                                        "Price" => array(
                                            "Normal"    => (double)$variation_data['regular_price'],
                                            "Discount"  => (double)$variation_data['sale_price'],
                                            "Currency"  => $Currency
                                        ),
                                        "Stock" => (int)(($Variation->get_manage_stock()) ? $Variation->get_stock_quantity() : $Variation->get_stock_status() == 'instock'),
                                        "Images" => $ImageLinks,
                                        "CustomFields" => $this->GetCustomFields($Variation, 'variation'),
                                        "Updated" => get_the_modified_date('Y-m-d\TH:i:sT', $VariationID),
                                    );
                                    $this->SetAdditionalData($ProductID, $variation_data, $Options, $ProductData, $VariationData);
                                    $ProductData['Variations'][] = $VariationData;
                                }
                                else throw new Exception('Failed to fetch variation id: '.$VariationID.' for product id: '.$ProductID);
                            }
                        }
                        else {
                            $VariationData = array(
                                "ID" => $ProductData['ID'],
                                "SKU" => $ProductData['SKU'],
                                "EAN" => $ProductData['EAN'],
                                "Color" => $ProductColor,
                                "Size" => (!empty($ProductSize)) ? $ProductSize :  'ONESIZE',
                                "Gender" => $ProductGender,
                                "Material" => $ProductMaterial,
                                "Condition" => $ProductCondition,
                                "Delivery" => $ProductDeliveryTerms,
                                "URL" => $ProductData['URL'],
                                "Price" => array(
                                    "Normal" => (double)$Product->get_regular_price(),
                                    "Discount" => (double)$Product->get_sale_price(),
                                    "Currency" => $Currency
                                ),
                                "Stock" => (int)(($Product->get_manage_stock()) ? $Product->get_stock_quantity() : $Product->get_stock_status() == 'instock'),
                                "Images" => $ProductData['Images'],
                                "CustomFields" => $this->GetCustomFields($Product, 'product'),
                                "Updated" => $ProductData['Updated'],
                            );
                            $this->SetAdditionalData($ProductID, $Product->get_data(), $Options, $ProductData, $VariationData);
                            $ProductData['Variations'][] = $VariationData;
                        }
                        foreach ($ProductData['Variations'] as &$VariationReference) {
                            if (empty($VariationReference['EAN'])) $VariationReference['EAN'] = $ProductEAN;
                            if (empty($VariationReference['SKU'])) $VariationReference['SKU'] = $ProductData['SKU'];
                            if (empty($VariationReference['Color'])) $VariationReference['Color'] = $ProductColor;
                            if (empty($VariationReference['Gender'])) $VariationReference['Gender'] = $ProductGender;
                            if (empty($VariationReference['Material'])) $VariationReference['Material'] = $ProductMaterial;
                            if (empty($VariationReference['Condition'])) $VariationReference['Condition'] = $ProductCondition;
                            if (empty($VariationReference['DeliveryTerms'])) $VariationReference['DeliveryTerms'] = $ProductDeliveryTerms;
                        }
                        $Products[] = $ProductData;
                    }
                    else throw new Exception('Failed to fetch product for id: '.$ProductID);
                }
            }
            catch (Exception $e) {
                $this->APIError($e->getMessage(), $e->getCode());
            }
            return $Products;
        }

        private function SetAdditionalData($ProductID, $Data, $Options, &$ProductData, &$VariationData) {
            if (is_array($Data['attributes'])) {
                foreach ($Data['attributes'] as $attr_name => $attr_value) {
                    if (in_array($attr_name, $Options['Sizes'])) {
                        if (empty($VariationData['Size'])) $VariationData['Size'] = $this->ResolveAttributeValue($attr_value);
                        else {
                            $VariationData['Size'] .= " ".$this->ResolveAttributeValue($attr_value);
                            $VariationData['Size'] = trim($VariationData['Size'], " ");
                        }
                    }
                    if (in_array($attr_name, $Options['Brand']) && empty($ProductData['Brand'])) $ProductData['Brand'] = $this->ResolveAttributeValue($attr_value);
                    if (in_array($attr_name, $Options['EAN']) && empty($VariationData['EAN'])) $VariationData['EAN'] = $this->ResolveAttributeValue($attr_value);
                    if (in_array($attr_name, $Options['Color']) && empty($VariationData['Color'])) $VariationData['Color'] = $this->ResolveAttributeValue($attr_value);
                    if (in_array($attr_name, $Options['Season']) && empty($ProductData['Season'])) $ProductData['Season'] = $this->ResolveAttributeValue($attr_value);
                    if (in_array($attr_name, $Options['Gender']) && empty($VariationData['Gender'])) $VariationData['Gender'] = $this->ResolveAttributeValue($attr_value);
                    if (in_array($attr_name, $Options['Material']) && empty($VariationData['Material'])) $VariationData['Material'] = $this->ResolveAttributeValue($attr_value);
                    if (in_array($attr_name, $Options['Condition']) && empty($VariationData['Condition'])) $VariationData['Condition'] = $this->ResolveAttributeValue($attr_value);
                    if (in_array($attr_name, $Options['DeliveryTerms']) && empty($VariationData['DeliveryTerms'])) $VariationData['DeliveryTerms'] = $this->ResolveAttributeValue($attr_value);
                }
            }
            foreach (array_keys($Options) as $Key) {
                $OptionValues = $Options[$Key];
                if (!is_array($OptionValues)) $OptionValues = array($OptionValues);
                foreach ($OptionValues as $OptionValue) {
                    $Terms = wp_get_post_terms($ProductID, preg_replace("/^category_/", "", $OptionValue));
                    if (!empty($Terms)) {
                        $Terms = wp_list_pluck($Terms, 'name');
                        if (!empty($Terms)) {
                            $Value = implode(' ', $Terms);
                            if (in_array($OptionValue, $Options['Brand']) && empty($ProductData['Brand'])) $ProductData['Brand'] = $Value;
                            if (in_array($OptionValue, $Options['EAN']) && empty($VariationData['EAN'])) $VariationData['EAN'] = $Value;
                            if (in_array($OptionValue, $Options['Color']) && empty($VariationData['Color'])) $VariationData['Color'] = $Value;
                            if (in_array($OptionValue, $Options['Season']) && empty($ProductData['Season'])) $ProductData['Season'] = $Value;
                            if (in_array($OptionValue, $Options['Gender']) && empty($VariationData['Gender'])) $VariationData['Gender'] = $Value;
                            if (in_array($OptionValue, $Options['Material']) && empty($VariationData['Material'])) $VariationData['Material'] = $Value;
                            if (in_array($OptionValue, $Options['Condition']) && empty($VariationData['Condition'])) $VariationData['Condition'] = $Value;
                            if (in_array($OptionValue, $Options['DeliveryTerms']) && empty($VariationData['DeliveryTerms'])) $VariationData['DeliveryTerms'] = $Value;
                        }
                    }
                }
            }
            if (is_array($Data['meta_data'])) {
                foreach ($Data['meta_data'] as $MetaField) {
                    $MetaField = $MetaField->get_data();
                    $Key = 'meta_'.$MetaField['key'];
                    if (in_array($Key, $Options['Brand']) && empty($ProductData['Brand'])) $ProductData['Brand'] = $MetaField['value'];
                    if (in_array($Key, $Options['EAN']) && empty($VariationData['EAN'])) $VariationData['EAN'] = $MetaField['value'];
                    if (in_array($Key, $Options['Color']) && empty($VariationData['Color'])) $VariationData['Color'] = $MetaField['value'];
                    if (in_array($Key, $Options['Season']) && empty($ProductData['Season'])) $ProductData['Season'] = $MetaField['value'];
                    if (in_array($Key, $Options['Gender']) && empty($VariationData['Gender'])) $VariationData['Gender'] = $MetaField['value'];
                    if (in_array($Key, $Options['Material']) && empty($VariationData['Material'])) $VariationData['Material'] = $MetaField['value'];
                    if (in_array($Key, $Options['Condition']) && empty($VariationData['Condition'])) $VariationData['Condition'] = $MetaField['value'];
                    if (in_array($Key, $Options['DeliveryTerms']) && empty($VariationData['DeliveryTerms'])) $VariationData['DeliveryTerms'] = $MetaField['value'];
                }
            }
        }

        private function ResolveAttributeValue($Value) {
            if (is_object($Value)) return '';
            if (!isset($this->AttributeDisplayValues[$Value])) {
                global $wpdb;
                $Term = $wpdb->get_results($wpdb->prepare("SELECT name FROM $wpdb->terms WHERE slug = %s", $Value), 'OBJECT');
                if (isset($Term[0]) && isset($Term[0]->name)) $this->AttributeDisplayValues[$Value] = $Term[0]->name;
                else $this->AttributeDisplayValues[$Value] = $Value;
            }
            return $this->AttributeDisplayValues[$Value];
        }

        private function GetCategoryPaths($ID) {
            $TermsByID = array();
            $IsFirstIteration = true;
            $CategoryPaths = array();
            $CategoryPathsPrevious = array();
            $terms = get_the_terms($ID, 'product_cat');
            if (is_array($terms)) {
                foreach ($terms as $term) $TermsByID[$term->term_id] = $term;
                for ($i = 0; !empty($TermsByID) && $i < 10; $i++) {
                    $CategoryPaths = array();
                    $FoundPreviousCategoryPaths = array();
                    foreach ($TermsByID as $ID => $Term) {
                        if ($IsFirstIteration && (!isset($TermsByID[$Term->parent]) && !isset($CategoryPaths[$Term->parent])) || $Term->parent == 0) {
                            //THIS IS THE ROOT LEVEL AS WE HAVE NO PRESENT PARENT IN THE CATEGORY LIST
                            $CategoryPaths[$Term->term_id] = str_replace(">", " - ", $Term->name);
                            unset($TermsByID[$ID]);
                        }
                        else if (isset($CategoryPathsPrevious[$Term->parent])) {
                            if (!in_array($Term->parent, $FoundPreviousCategoryPaths)) $FoundPreviousCategoryPaths[] = $Term->parent;
                            //THIS IS THE CURRENT ROOT LEVEL AS WE HAVE NO PRESENT PARENT IN THE CATEGORY LIST ANYMORE (ALREADY ADDED TO CATEGORY PATHS BEFORE)
                            $CategoryPaths[$Term->term_id] = $CategoryPathsPrevious[$Term->parent]." > ".str_replace(">", " - ", $Term->name);
                            unset($TermsByID[$ID]);
                        }
                    }
                    foreach ($CategoryPathsPrevious as $ID => $Path) {
                        //CHECK ALL PREVIOUS PATHS IF THEY HAVE CHILDS FOUND. IF NOT PRESERVE THE PATH AS IT'S ALREADY THE LOWEST LEVEL FOR THIS PATH
                        if (!in_array($ID, $FoundPreviousCategoryPaths)) $CategoryPaths[] = $Path;
                    }
                    $CategoryPathsPrevious = $CategoryPaths;
                    $IsFirstIteration = false;
                }
                sort($CategoryPaths);
            }
            return $CategoryPaths;
        }

        public function prevent_customer_emails_from_being_sent($recipient, $order) {
            $value = $this->GetPostMeta($order->get_id(), 'product_feeder_order');
            return (empty($value)) ? $recipient : '';
        }

    }