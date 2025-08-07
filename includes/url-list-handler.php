<?php
if (!defined('ABSPATH')) {
    exit; // Previene accessi diretti
}

/**
 * Recupera la lista di URL per l'utente corrente.
 *
 * @param string|null $content_type Tipo di contenuto (products o categories). Se nullo, restituisce tutti.
 * @return array Lista di URL.
 */
function aigsc_get_url_list($content_type = null) {
    global $wpdb;
    $user_id = get_current_user_id();

    if (!$user_id) {
        return [];
    }

    $table_name = $wpdb->prefix . 'aigsc_url_list';

    if ($content_type) {
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT id, url, content_type, created_at FROM $table_name WHERE user_id = %d AND content_type = %s ORDER BY created_at DESC",
                $user_id,
                sanitize_text_field($content_type)
            )
        );
    } else {
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT id, url, content_type, created_at FROM $table_name WHERE user_id = %d ORDER BY created_at DESC",
                $user_id
            )
        );
    }

    return $results ? $results : [];
}

/**
 * Salva una lista di URL per l'utente corrente.
 *
 * @param array $urls Lista di URL da salvare.
 * @param string $content_type Tipo di contenuto (products o categories).
 * @return bool True se il salvataggio è riuscito, False altrimenti.
 */
function aigsc_save_url_list($urls, $content_type) {
    global $wpdb;
    $user_id = get_current_user_id();

    if (!$user_id || empty($urls) || !in_array($content_type, ['products', 'categories'])) {
        return false;
    }

    $table_name = $wpdb->prefix . 'aigsc_url_list';
    $inserted = 0;

    foreach ($urls as $url) {
        $result = $wpdb->insert(
            $table_name,
            [
                'user_id' => $user_id,
                'url' => esc_url_raw($url),
                'content_type' => $content_type,
                'created_at' => current_time('mysql'),
            ],
            ['%d', '%s', '%s', '%s']
        );

        if ($result !== false) {
            $inserted++;
        }
    }

    return $inserted === count($urls);
}



add_action('rest_api_init', function () {
    register_rest_route('ai-generate-seo/v1', '/delete-urls', [
        'methods' => 'POST',
        'callback' => 'aigsc_rest_delete_urls',
        'permission_callback' => '__return_true', // Puoi raffinare la logica di autorizzazione
    ]);
	
    register_rest_route('ai-generate-seo/v1', '/generate-descriptions', array(
        'methods' => 'POST',
        'callback' => 'handle_generate_descriptions',
        'permission_callback' => '__return_true',
    ));
    register_rest_route('ai-generate-seo/v1', '/generate-meta-tags', array(
        'methods' => 'POST',
        'callback' => 'handle_generate_meta_tags',
        'permission_callback' => '__return_true',
    ));
});


	/**
	 * Callback per eliminare URL tramite REST API.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	function aigsc_rest_delete_urls(WP_REST_Request $request) {
		$ids = $request->get_param('ids');

		if (empty($ids) || !is_array($ids)) {
			return new WP_REST_Response(['error' => 'Nessun ID valido fornito.'], 400);
		}

		$success = aigsc_delete_url_list($ids);

		if ($success) {
			return new WP_REST_Response(['success' => 'URL eliminati con successo.'], 200);
		} else {
			return new WP_REST_Response(['error' => 'Errore durante l\'eliminazione degli URL.'], 500);
		}
	}

	/**
	 * Elimina una lista di URL specifici.
	 *
	 * @param array $ids ID degli URL da eliminare.
	 * @return bool True se l'eliminazione è riuscita, False altrimenti.
	 */
	function aigsc_delete_url_list($ids) {
		global $wpdb;
		$user_id = get_current_user_id();

		if (!$user_id || empty($ids)) {
			return false;
		}

		$table_name = $wpdb->prefix . 'aigsc_url_list';
		$placeholders = implode(',', array_fill(0, count($ids), '%d'));

		$query = $wpdb->prepare(
			"DELETE FROM $table_name WHERE user_id = %d AND id IN ($placeholders)",
			array_merge([$user_id], $ids)
		);

		return $wpdb->query($query) !== false;
	}




/* function handle_generate_descriptions(WP_REST_Request $request) {
    $ids = $request->get_param('ids');
    // Implementa la logica per generare descrizioni SEO
    return new WP_REST_Response(['success' => true, 'message' => 'Descrizioni generate con successo'], 200);
	} */




	function handle_generate_meta_tags(WP_REST_Request $request) {
		$ids = $request->get_param('ids');
		// Implementa la logica per generare meta tag SEO
		return new WP_REST_Response(['success' => true, 'message' => 'Meta tag generati con successo'], 200);
	}



	function generate_description_api($row, $serial_key, $info) {
		$url = 'https://api.clementeteodonno.it/wp-json/myplugin/v1/generate-category-description?user_key='.$serial_key;
	   
	   $prompt = aigsc_crea_prompt($row);

		$postData = [
			'prompt_text_system' => 'Sei un esperto di SEO e copywriter. Segui le [istruzioni] e [informazioni] di seguito per generare descrizioni per il contenuto di questo sito web. L\'output dovrebbe essere del codice HTML che inizia direttamente con il contenuto della descrizione, senza includere i tag di apertura <html> o <body> \n\n[informazioni]:\n\n'.$info,
			'prompt_text' => $prompt,
			'chatgpt_model' => 'gpt-4-turbo'
		];

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
		$response = curl_exec($ch);

		if (curl_errno($ch)) {
			return false;
		}
		$response_data = json_decode($response, true);
		return $response_data['result']['choices'][0]['message']['content'] ?? false;
	}


	function aigsc_build_prompt($row) {
		// Costruisce il prompt specifico per la riga corrente
		$prompt = "[istruzioni]: \n\n";
		
		// Verifica il tipo di contenuto e aggiusta il prompt di conseguenza
		if ($row->content_type === 'products') {
			$prompt .= "Genera un testo descrittivo di massimo 100 parole ottimizzato per la SEO per il prodotto: ";
		} else {
			$prompt .= "Genera un testo descrittivo di massimo 100 parole ottimizzato per la SEO per la categoria: ";
		}

		$prompt .= "{$row->url}.\n\n";
		
		// Puoi includere ulteriori dettagli o istruzioni se necessario
		// $prompt .= "Altre istruzioni o dettagli specifici possono essere aggiunti qui.";

		return $prompt;
	}
