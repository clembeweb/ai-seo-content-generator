<?php
if (!defined('ABSPATH')) {
    exit; // Previene accessi diretti
}

/**
 * Shortcode unico per il plugin AI Generate SEO Content
 * Gestisce il routing tra i template basandosi sui parametri URL.
 * 
 * Esempio URL:
 * URL principale: https://tuodominio.com/ai-seo-tool/?step=main
 * Info Business: https://tuodominio.com/ai-seo-tool/?step=business-info
 * Lista URL: https://tuodominio.com/ai-seo-tool/?step=url-list
 * Risultati SEO: https://tuodominio.com/ai-seo-tool/?step=seo-results
 */
function aigsc_template_router() {
    ob_start();

    // Determina l'azione corrente in base al parametro 'step'
    $step = isset($_GET['step']) ? sanitize_text_field($_GET['step']) : 'main';

    // Switch dinamico per gestire il flusso
    switch ($step) {
        case 'main': // Pagina principale
            include plugin_dir_path(__FILE__) . '../templates/main-page.php';
            break;

        case 'business-info': // Modifica informazioni business
            include plugin_dir_path(__FILE__) . '../templates/business-info.php';
            break;

        case 'url-list': // Aggiunta/estrazione URL
            include plugin_dir_path(__FILE__) . '../templates/url-list.php';
            break;

        case 'seo-results': // Visualizzazione risultati SEO
            include plugin_dir_path(__FILE__) . '../templates/seo-results.php';
            break;

        default: // Caso predefinito per azioni non valide
            echo '<p>Azione non valida. Torna alla <a href="' . esc_url(home_url('ai-generate-seo-content/?step=main')) . '">pagina principale</a>.</p>';
            break;
    }

    return ob_get_clean();
}
add_shortcode('ai_generate_seo', 'aigsc_template_router');
