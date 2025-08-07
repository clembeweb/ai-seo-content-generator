<?php
if (!defined('ABSPATH')) {
    exit; // Previene accessi diretti
}
$current_url = get_permalink();
?>
<div class="aigsc-main">
    <h1>AI Generate SEO Content</h1>
    <p>Benvenuto! Utilizza i pulsanti sottostanti per gestire le informazioni del tuo business, aggiungere o estrarre URL, e generare contenuti SEO con AI.</p>

<div class="aigsc-actions">
    <a href="<?php echo esc_url(add_query_arg('step', 'business-info', $current_url)); ?>" class="button button-primary">Aggiungi/Modifica Informazioni Business</a>
    <a href="<?php echo esc_url(add_query_arg('step', 'url-list', $current_url)); ?>" class="button button-primary">Aggiungi/Estrai Lista di URL</a>
    <a href="<?php echo esc_url(add_query_arg('step', 'seo-results', $current_url)); ?>" class="button button-primary">Visualizza Risultati SEO</a>
</div>
</div>

<style>
    .aigsc-main {
        max-width: 800px;
        margin: 0 auto;
        text-align: center;
    }

    .aigsc-actions {
        margin-top: 20px;
    }

    .aigsc-actions .button {
        margin: 5px;
        padding: 10px 20px;
        font-size: 16px;
    }
</style>
