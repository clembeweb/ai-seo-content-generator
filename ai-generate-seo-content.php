<?php
/**
 * Plugin Name: AI Generate SEO Content
 * Description: Un plugin per generare contenuti SEO con AI.
 * Version: 1.0
 * Author: Tuo Nome
 */

// Previene accessi diretti
if (!defined('ABSPATH')) {
    exit;
}

// Definisci il percorso base del plugin
define('AIGSC_PLUGIN_DIR', plugin_dir_path(__FILE__));

// Includi i file necessari
require_once AIGSC_PLUGIN_DIR . 'includes/database.php';
require_once AIGSC_PLUGIN_DIR . 'includes/url-list-handler.php';
require_once AIGSC_PLUGIN_DIR . 'includes/seo-generator.php';
require_once AIGSC_PLUGIN_DIR . 'includes/table-handler.php';
require_once AIGSC_PLUGIN_DIR . 'includes/template-router.php'; // Nuovo file
require_once AIGSC_PLUGIN_DIR . 'includes/business-info-handler.php'; // Nuovo file

// Carica gli asset (CSS e JS)
function aigsc_enqueue_assets() {
    wp_enqueue_style('aigsc-style', plugin_dir_url(__FILE__) . 'assets/css/style.css', [], '1.0');
    wp_enqueue_script('aigsc-script', plugin_dir_url(__FILE__) . 'assets/js/script.js', ['jquery'], '1.0', true);
}
add_action('wp_enqueue_scripts', 'aigsc_enqueue_assets');

register_activation_hook(__FILE__, 'aigsc_activate');
