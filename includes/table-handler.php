<?php
if (!defined('ABSPATH')) {
    exit; // Previene accessi diretti
}

/**
 * Visualizza una tabella DataTables con i risultati SEO dell'utente.
 */
function aigsc_render_seo_table() {
    $results = aigsc_get_seo_results(); // Recupera i risultati dal database

    if (empty($results)) {
        echo '<p>Nessun risultato SEO disponibile. Torna alla <a href="' . esc_url(home_url()) . '">pagina principale</a> per generarli.</p>';
        return;
    }
    ?>

    <div class="aigsc-table-container">
        <h2>Risultati SEO Generati</h2>
        <table id="seo-results-table" class="display" style="width:100%">
            <thead>
                <tr>
                    <th><input type="checkbox" id="select-all"></th>
                    <th>Nome</th>
                    <th>URL</th>
                    <th>Descrizione SEO</th>
                    <th>Meta Description</th>
                    <th>Data Creazione</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $result) : ?>
                <tr>
                    <td><input type="checkbox" class="select-row" data-id="<?php echo esc_attr($result->id); ?>"></td>
                    <td><?php echo esc_html($result->name); ?></td>
                    <td><a href="<?php echo esc_url($result->url); ?>" target="_blank"><?php echo esc_html($result->url); ?></a></td>
                    <td><?php echo esc_html($result->seo_description); ?></td>
                    <td><?php echo esc_html($result->meta_description); ?></td>
                    <td><?php echo esc_html($result->created_at); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <button id="delete-selected" class="button button-danger">Elimina Selezionati</button>
    </div>

    <script>
    jQuery(document).ready(function($) {
        // Inizializza DataTables
        var table = $('#seo-results-table').DataTable({
            dom: 'Bfrtip',
            buttons: ['copy', 'csv', 'excel', 'print'],
            order: [[5, 'desc']], // Ordina per Data Creazione
        });

        // Seleziona/Deseleziona tutte le righe
        $('#select-all').on('click', function() {
            var rows = table.rows({ 'search': 'applied' }).nodes();
            $('input[type="checkbox"]', rows).prop('checked', this.checked);
        });

        // Elimina righe selezionate
        $('#delete-selected').on('click', function() {
            var selectedIds = [];
            $('.select-row:checked').each(function() {
                selectedIds.push($(this).data('id'));
            });

            if (selectedIds.length === 0) {
                alert('Seleziona almeno una riga da eliminare.');
                return;
            }

            if (!confirm('Sei sicuro di voler eliminare le righe selezionate?')) {
                return;
            }

            $.ajax({
                url: "<?php echo esc_url(rest_url('ai-generate-seo/v1/delete-seo-results')); ?>",
                method: 'POST',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', '<?php echo wp_create_nonce('wp_rest'); ?>');
                },
                data: {
                    ids: selectedIds
                },
                success: function(response) {
                    alert(response.success || 'Righe eliminate con successo.');
                    table.rows('.select-row:checked').remove().draw();
                },
                error: function() {
                    alert('Errore durante l\'eliminazione delle righe.');
                }
            });
        });
    });
    </script>

    <?php
}

/**
 * Registra l'endpoint REST per eliminare risultati SEO.
 */
add_action('rest_api_init', function() {
    register_rest_route('ai-generate-seo/v1', '/delete-seo-results', [
        'methods' => 'POST',
        'callback' => 'aigsc_delete_seo_results_rest',
        'permission_callback' => '__return_true', // Aggiungere sicurezza in produzione
    ]);
});

/**
 * Callback per eliminare risultati SEO tramite REST API.
 *
 * @param WP_REST_Request $request La richiesta REST.
 * @return WP_REST_Response
 */
function aigsc_delete_seo_results_rest(WP_REST_Request $request) {
    $ids = $request->get_param('ids');

    if (empty($ids) || !is_array($ids)) {
        return new WP_REST_Response(['error' => 'Dati mancanti o non validi.'], 400);
    }

    $success = aigsc_delete_seo_results($ids);

    if ($success) {
        return new WP_REST_Response(['success' => 'Righe eliminate con successo.'], 200);
    } else {
        return new WP_REST_Response(['error' => 'Errore durante l\'eliminazione.'], 500);
    }
}