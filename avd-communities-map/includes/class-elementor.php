<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class AVDC_Elementor {

    public static function init() {
        add_action( 'elementor/elements/categories_registered', array( __CLASS__, 'register_category' ) );
        add_action( 'elementor/widgets/register',               array( __CLASS__, 'register_widgets' ) );
    }

    public static function register_category( $elements_manager ) {
        $elements_manager->add_category( 'avd-communities', array(
            'title' => __( 'AVD Communities', 'avd-communities-map' ),
            'icon'  => 'fa fa-map-marker',
        ) );
    }

    public static function register_widgets( $widgets_manager ) {
        require_once AVDC_PATH . 'includes/elementor/class-widget-communities-map.php';
        $widgets_manager->register( new AVDC_Widget_Communities_Map() );
    }
}
