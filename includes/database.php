<?php
if (!defined('ABSPATH')) {
    exit; // Previene accessi diretti
}

/**
 * Crea le tabelle necessarie per il plugin.
 */
function aigsc_create_tables() {
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    // Tabella per le informazioni di Business
    $business_table = $wpdb->prefix . 'aigsc_business_info';
    $business_sql = "CREATE TABLE IF NOT EXISTS $business_table (
        id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT(20) UNSIGNED NOT NULL,
        business_info TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) $charset_collate;";

    // Tabella per l'elenco degli URL
    $url_table = $wpdb->prefix . 'aigsc_url_list';
    $url_sql = "CREATE TABLE IF NOT EXISTS $url_table (
        id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT(20) UNSIGNED NOT NULL,
        url TEXT NOT NULL,
        content_type ENUM('product', 'category') DEFAULT 'product',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) $charset_collate;";

    // Tabella per i risultati SEO generati
    $seo_results_table = $wpdb->prefix . 'aigsc_seo_results';
    $seo_results_sql = "CREATE TABLE IF NOT EXISTS $seo_results_table (
        id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT(20) UNSIGNED NOT NULL,
        url TEXT NOT NULL,
        seo_title TEXT,
        seo_meta_description TEXT,
		seo_content_page LONGTEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($business_sql);
    dbDelta($url_sql);
    dbDelta($seo_results_sql);
}

/**
 * Funzione di attivazione del plugin.
 * Chiama la creazione delle tabelle.
 */
function aigsc_activate() {
    aigsc_create_tables();
}
register_activation_hook(__FILE__, 'aigsc_activate');

/**
 * Funzione di disattivazione del plugin.
 * Qui è possibile aggiungere logica per cleanup o altro.
 */
function aigsc_deactivate() {
    // Non eliminiamo le tabelle per non perdere i dati degli utenti
}
register_deactivation_hook(__FILE__, 'aigsc_deactivate');
