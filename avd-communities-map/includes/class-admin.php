<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class AVDC_Admin {

    public static function init() {
        add_action( 'admin_menu',            array( __CLASS__, 'register_menu' ) );
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
        add_action( 'admin_post_avdc_save_settings',    array( __CLASS__, 'handle_save_settings' ) );
        add_action( 'admin_post_avdc_save_community',   array( __CLASS__, 'handle_save_community' ) );
        add_action( 'admin_post_avdc_delete_community', array( __CLASS__, 'handle_delete_community' ) );
        add_action( 'admin_post_avdc_fix_db',           array( __CLASS__, 'handle_fix_db' ) );
    }

    public static function register_menu() {
        add_theme_page(
            'AVD Communities Map',
            'AVD Communities Map',
            'manage_options',
            'avdc-communities',
            array( __CLASS__, 'page_communities' )
        );
        add_submenu_page(
            'themes.php',
            'AVD Communities Map — Settings',
            'Communities Settings',
            'manage_options',
            'avdc-settings',
            array( __CLASS__, 'page_settings' )
        );
    }

    public static function enqueue_assets( $hook ) {
        $pages = array(
            'appearance_page_avdc-communities',
            'appearance_page_avdc-settings',
        );
        if ( ! in_array( $hook, $pages ) ) return;

        wp_enqueue_style( 'avdc-admin',          AVDC_URL . 'admin/css/admin.css',  array(), AVDC_VERSION );
        wp_enqueue_style( 'avdc-areas-preview',  AVDC_URL . 'public/css/areas.css', array(), AVDC_VERSION );
        wp_enqueue_script( 'avdc-admin',         AVDC_URL . 'admin/js/admin.js', array( 'jquery' ), AVDC_VERSION, true );
        wp_enqueue_style(  'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
        wp_enqueue_media();
    }

    // ── COMMUNITIES LIST / EDIT ───────────────────────────────────────────
    public static function page_communities() {
        $action  = $_GET['action'] ?? 'list';
        $id      = intval( $_GET['area_id'] ?? 0 );
        $area    = $id ? AVDC_Areas_DB::get( $id ) : null;
        $areas   = AVDC_Areas_DB::get_all();
        $saved   = isset( $_GET['saved'] );
        $deleted = isset( $_GET['deleted'] );

        if ( $action === 'edit' || $action === 'new' ) {
            include AVDC_PATH . 'admin/views/areas-edit.php';
        } else {
            include AVDC_PATH . 'admin/views/areas-list.php';
        }
    }

    public static function handle_save_community() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
        check_admin_referer( 'avdc_save_community' );
        $id = intval( $_POST['area_id'] ?? 0 );
        self::do_save_community( $_POST, $id ?: null );
        wp_redirect( admin_url( 'themes.php?page=avdc-communities&saved=1' ) );
        exit;
    }

    private static function do_save_community( $post, $id = null ) {
        $data = array(
            'area_name'   => sanitize_text_field( $post['area_name']   ?? '' ),
            'state'       => sanitize_text_field( $post['state']       ?? '' ),
            'lat'         => sanitize_text_field( $post['lat']         ?? '' ),
            'lng'         => sanitize_text_field( $post['lng']         ?? '' ),
            'zoom'        => intval(              $post['zoom']        ?? 13 ),
            'image_url'   => esc_url_raw(         $post['image_url']   ?? '' ),
            'custom_link' => esc_url_raw(         $post['custom_link'] ?? '' ),
            'group_label' => sanitize_text_field( $post['group_label'] ?? '' ),
            'sort_order'  => intval(              $post['sort_order']  ?? 0  ),
            'active'      => isset( $post['active'] ) ? 1 : 0,
        );
        if ( $id ) {
            AVDC_Areas_DB::update( $id, $data );
        } else {
            AVDC_Areas_DB::insert( $data );
        }
    }

    public static function handle_delete_community() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
        check_admin_referer( 'avdc_delete_community' );
        $id = intval( $_POST['area_id'] ?? 0 );
        if ( $id ) AVDC_Areas_DB::delete( $id );
        wp_redirect( admin_url( 'themes.php?page=avdc-communities&deleted=1' ) );
        exit;
    }

    // ── SETTINGS ──────────────────────────────────────────────────────────
    public static function page_settings() {
        $s     = AVDC_Settings::get();
        $saved = isset( $_GET['saved'] );
        include AVDC_PATH . 'admin/views/settings.php';
    }

    public static function handle_save_settings() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
        check_admin_referer( 'avdc_save_settings' );
        AVDC_Settings::save( $_POST );
        wp_redirect( admin_url( 'themes.php?page=avdc-settings&saved=1' ) );
        exit;
    }

    // ── FIX DB ────────────────────────────────────────────────────────────
    public static function handle_fix_db() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
        check_admin_referer( 'avdc_fix_db' );
        delete_option( AVDC_Areas_DB::SCHEMA_OPTION );
        AVDC_Areas_DB::maybe_upgrade();
        wp_redirect( admin_url( 'themes.php?page=avdc-communities&db_fixed=1' ) );
        exit;
    }
}
