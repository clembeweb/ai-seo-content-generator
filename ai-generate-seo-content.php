<?php
/**
 * Plugin Name: AI Generate SEO Content
 * Description: Generate SEO content using OpenAI for URLs.
 * Version: 2.0.0
 * Author: Example Author
 * Requires at least: 6.0
 * Requires PHP: 8.1
 * Text Domain: ai-generate-seo-content
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'AIGSC_FILE', __FILE__ );

define( 'AIGSC_PATH', plugin_dir_path( __FILE__ ) );

define( 'AIGSC_URL', plugin_dir_url( __FILE__ ) );

require_once __DIR__ . '/vendor/autoload.php';

use AiGSC\Plugin;

function aigsc_bootstrap() : void {
    $plugin = new Plugin();
    $plugin->init();
}
add_action( 'plugins_loaded', 'aigsc_bootstrap' );

register_activation_hook( __FILE__, [ Plugin::class, 'activate' ] );
register_deactivation_hook( __FILE__, [ Plugin::class, 'deactivate' ] );
