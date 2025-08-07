<?php
namespace AiGSC;

class Database {
    public function createTables() : void {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $charset = $wpdb->get_charset_collate();

        $business = $wpdb->prefix . 'aigsc_business_info';
        $sql1 = "CREATE TABLE $business (
            id tinyint(1) NOT NULL AUTO_INCREMENT,
            info text NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset;";

        $urls = $wpdb->prefix . 'aigsc_urls';
        $sql2 = "CREATE TABLE $urls (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            url varchar(255) NOT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY url (url)
        ) $charset;";

        $results = $wpdb->prefix . 'aigsc_seo_results';
        $sql3 = "CREATE TABLE $results (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            url varchar(255) NOT NULL,
            type varchar(20) NOT NULL,
            content text NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'generated',
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY url_type (url,type)
        ) $charset;";

        dbDelta( $sql1 );
        dbDelta( $sql2 );
        dbDelta( $sql3 );
    }
}
