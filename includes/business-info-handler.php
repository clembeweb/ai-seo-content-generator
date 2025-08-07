<?php
if (!defined('ABSPATH')) {
    exit; // Previene accessi diretti
}

/**
 * Recupera le informazioni del business per l'utente corrente.
 *
 * @return string Le informazioni del business o una stringa vuota.
 */
function aigsc_get_business_info() {
    global $wpdb;
    $user_id = get_current_user_id();

    if (!$user_id) {
        return '';
    }

    $table_name = $wpdb->prefix . 'aigsc_business_info';
    $result = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT info FROM $table_name WHERE user_id = %d ORDER BY created_at DESC LIMIT 1",
            $user_id
        )
    );

    return $result ? $result : '';
}

/**
 * Salva le informazioni del business per l'utente corrente.
 *
 * @param string $info Le informazioni del business da salvare.
 * @return bool True se il salvataggio è riuscito, False altrimenti.
 */
function aigsc_save_business_info($info) {
    global $wpdb;
    $user_id = get_current_user_id();

    if (!$user_id || empty($info)) {
        return false;
    }

    $table_name = $wpdb->prefix . 'aigsc_business_info';
    $result = $wpdb->insert(
        $table_name,
        [
            'user_id' => $user_id,
            'info' => sanitize_text_field($info),
            'created_at' => current_time('mysql'),
        ],
        ['%d', '%s', '%s']
    );

    return $result !== false;
}
