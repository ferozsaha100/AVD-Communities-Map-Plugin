<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class AVDC_Areas_DB {

    const SCHEMA_VERSION = '1.0.0';
    const SCHEMA_OPTION  = 'avdc_areas_schema_version';

    /**
     * Safe upgrade — uses dbDelta() which only adds missing tables/columns.
     * Never drops data. Safe to run on every plugin load and on activation.
     */
    public static function maybe_upgrade() {
        $installed = get_option( self::SCHEMA_OPTION, '0' );
        if ( version_compare( $installed, self::SCHEMA_VERSION, '<' ) ) {
            self::create_table();
            update_option( self::SCHEMA_OPTION, self::SCHEMA_VERSION );
        }
    }

    public static function create_table() {
        global $wpdb;
        $table           = $wpdb->prefix . AVDC_AREAS_TABLE;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS {$table} (
            id           BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            area_name    VARCHAR(255)        NOT NULL DEFAULT '',
            state        VARCHAR(100)        NOT NULL DEFAULT '',
            lat          VARCHAR(20)         NOT NULL DEFAULT '',
            lng          VARCHAR(20)         NOT NULL DEFAULT '',
            zoom         TINYINT(3)          NOT NULL DEFAULT 13,
            image_url    VARCHAR(500)        NOT NULL DEFAULT '',
            custom_link  VARCHAR(500)        NOT NULL DEFAULT '',
            group_label  VARCHAR(100)        NOT NULL DEFAULT '',
            sort_order   SMALLINT(5)         NOT NULL DEFAULT 0,
            active       TINYINT(1)          NOT NULL DEFAULT 1,
            created_at   DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at   DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }

    /**
     * Hard reset — only callable from the admin "Fix Database" button.
     * DROPS the table. ALL DATA WILL BE LOST. Never called automatically.
     */
    public static function rebuild_table() {
        global $wpdb;
        $table = $wpdb->prefix . AVDC_AREAS_TABLE;
        $wpdb->query( "DROP TABLE IF EXISTS {$table}" );
        self::create_table();
    }

    public static function get_all( $active_only = false ) {
        global $wpdb;
        $table = $wpdb->prefix . AVDC_AREAS_TABLE;
        $where = $active_only ? 'WHERE active = 1' : '';
        return $wpdb->get_results(
            "SELECT * FROM {$table} {$where} ORDER BY sort_order ASC, id ASC"
        );
    }

    public static function get( $id ) {
        global $wpdb;
        $table = $wpdb->prefix . AVDC_AREAS_TABLE;
        return $wpdb->get_row(
            $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", intval( $id ) )
        );
    }

    public static function insert( $data ) {
        global $wpdb;
        $result = $wpdb->insert( $wpdb->prefix . AVDC_AREAS_TABLE, $data );
        return $result ? $wpdb->insert_id : false;
    }

    public static function update( $id, $data ) {
        global $wpdb;
        return $wpdb->update(
            $wpdb->prefix . AVDC_AREAS_TABLE,
            $data,
            array( 'id' => intval( $id ) )
        );
    }

    public static function delete( $id ) {
        global $wpdb;
        return $wpdb->delete(
            $wpdb->prefix . AVDC_AREAS_TABLE,
            array( 'id' => intval( $id ) )
        );
    }

    public static function get_columns() {
        global $wpdb;
        $table = $wpdb->prefix . AVDC_AREAS_TABLE;
        return $wpdb->get_col( "DESCRIBE {$table}", 0 );
    }
}
