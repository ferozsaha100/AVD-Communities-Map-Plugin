<?php
/**
 * Plugin Name:       AVD Communities Map
 * Plugin URI:        https://agentviewdigital.com
 * Description:       Interactive communities areas embed with Google Maps. Display location cards with a live map panel. Manage everything from the WordPress dashboard.
 * Version:           1.0.2
 * Author:            Feroj Hossain — Agent View Digital
 * Author URI:        https://agentviewdigital.com
 * License:           GPL-2.0+
 * Text Domain:       avd-communities-map
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'AVDC_VERSION',      '1.0.2' );
define( 'AVDC_PATH',         plugin_dir_path( __FILE__ ) );
define( 'AVDC_URL',          plugin_dir_url( __FILE__ ) );
define( 'AVDC_AREAS_TABLE',  'avdc_areas' );

require_once AVDC_PATH . 'includes/class-areas-db.php';
require_once AVDC_PATH . 'includes/class-settings.php';
require_once AVDC_PATH . 'includes/class-areas-shortcode.php';
require_once AVDC_PATH . 'includes/class-admin.php';

// Elementor integration — loaded only when Elementor is present.
add_action( 'elementor/init', function () {
    require_once AVDC_PATH . 'includes/class-elementor.php';
    AVDC_Elementor::init();
} );

register_activation_hook( __FILE__, 'avdc_activate' );
function avdc_activate() {
    AVDC_Areas_DB::maybe_upgrade();
}

add_action( 'plugins_loaded', 'avdc_boot' );
function avdc_boot() {
    AVDC_Areas_DB::maybe_upgrade();
    AVDC_Admin::init();
    AVDC_Areas_Shortcode::init();
}
