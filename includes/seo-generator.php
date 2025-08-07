<?php
if (!defined('ABSPATH')) {
    exit; // Previene accessi diretti
}

/**
 * Salva i risultati SEO per l'utente corrente.
 *
 * @param array $results Array contenente i risultati da salvare.
 * @return bool True se il salvataggio è riuscito, False altrimenti.
 */
function aigsc_save_seo_results($results) {
    global $wpdb;
    $user_id = get_current_user_id();

    if (!$user_id || empty($results)) {
        return false;
    }

    $table_name = $wpdb->prefix . 'aigsc_seo_results';
    $inserted = 0;

    foreach ($results as $result) {
        $data = [
            'user_id' => $user_id,
            'name' => sanitize_text_field($result['name']),
            'url' => esc_url_raw($result['url']),
            'seo_description' => sanitize_textarea_field($result['seo_description']),
            'meta_description' => sanitize_textarea_field($result['meta_description']),
            'created_at' => current_time('mysql'),
        ];

        $insert_result = $wpdb->insert($table_name, $data, ['%d', '%s', '%s', '%s', '%s', '%s']);
        if ($insert_result !== false) {
            $inserted++;
        }
    }

    return $inserted === count($results);
}

/**
 * Recupera i risultati SEO per l'utente corrente.
 *
 * @return array Lista dei risultati SEO.
 */
function aigsc_get_seo_results() {
    global $wpdb;
    $user_id = get_current_user_id();

    if (!$user_id) {
        return [];
    }

    $table_name = $wpdb->prefix . 'aigsc_seo_results';
    $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT id, name, url, seo_description, meta_description, created_at 
             FROM $table_name 
             WHERE user_id = %d 
             ORDER BY created_at DESC",
            $user_id
        )
    );

    return $results ? $results : [];
}

/**
 * Elimina risultati SEO specifici per l'utente corrente.
 *
 * @param array $ids ID dei risultati da eliminare.
 * @return bool True se l'eliminazione è riuscita, False altrimenti.
 */
function aigsc_delete_seo_results($ids) {
    global $wpdb;
    $user_id = get_current_user_id();

    if (!$user_id || empty($ids)) {
        return false;
    }

    $table_name = $wpdb->prefix . 'aigsc_seo_results';
    $placeholders = implode(',', array_fill(0, count($ids), '%d'));

    $query = $wpdb->prepare(
        "DELETE FROM $table_name WHERE user_id = %d AND id IN ($placeholders)",
        array_merge([$user_id], $ids)
    );

    return $wpdb->query($query) !== false;
}

/**
 * Genera contenuti SEO tramite API AI.
 *
 * @param array $data Dati di input per la generazione (nome, URL, ecc.).
 * @return array Risultati generati (descrizioni SEO e meta tag).
 */
function aigsc_generate_seo_content($data) {
    // Simulazione: questa funzione dovrebbe inviare i dati a un'API AI e ricevere i risultati.
    $results = [];

    foreach ($data as $item) {
        $results[] = [
            'name' => $item['name'],
            'url' => $item['url'],
            'seo_description' => 'Descrizione SEO generata per ' . $item['name'],
            'meta_description' => 'Meta description generata per ' . $item['name'],
        ];
    }

    return $results;
}
