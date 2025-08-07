<?php
if (is_user_logged_in()) :
    global $wpdb;

    $user_id = get_current_user_id();
	

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $action = $_POST['action'] ?? '';
        switch ($action) {
            case 'aigsc_add_urls':
                $content_type = $_POST['content_type'] ?? '';
                $urls_list = $_POST['url_list'] ?? '';
                add_urls($user_id, $content_type, $urls_list);
                break;
            case 'generate_descriptions':
                $selected_rows = $_POST['selected_rows'] ?? [];
                generate_seo_descriptions($user_id, $selected_rows);
                break;
            case 'generate_meta_tags':
                $selected_rows = $_POST['selected_rows'] ?? [];
                generate_seo_meta_tags($user_id, $selected_rows);
                break;
            case 'delete_selected':
                $selected_rows = $_POST['selected_rows'] ?? [];
                delete_selected_rows($user_id, $selected_rows);
                break;
            // Continua per altri casi...
        }
    }

    // Recupera gli URL dell'utente
    $urls = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT id, url, content_type, created_at 
             FROM {$wpdb->prefix}aigsc_url_list 
             WHERE user_id = %d 
             ORDER BY created_at DESC",
            $user_id
        )
    );

    // Mostra la tabella solo se ci sono URL
    if (!empty($urls)) :
?>
	<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.datatables.net/1.13.4/js/dataTables.buttons.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
	<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
	<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
	<div class="ai-url-list">
		<form action="" method="post">
			<input type="hidden" name="action" id="form-action" value="">
			<h2>Elenco URL</h2>
			<button onclick="window.location.href='<?php echo esc_url(add_query_arg('step', 'main')); ?>'" class="button">Torna Indietro</button>
			<table id="url-results-table" class="display" style="width: 100%; margin-top: 20px;">
				<thead>
					<tr>
						<th>
							<input class="select-row" type="checkbox" id="select-all">
						</th>
						<th>URL</th>
						<th>Tipo Contenuto</th>
						<th>Data Creazione</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($urls as $url) : ?>
					<tr>
						<td><input type="checkbox" class="select-row" data-id="<?php echo esc_attr($url->id); ?>"></td>
						<td><?php echo esc_html($url->url); ?></td>
						<td><?php echo esc_html(ucfirst($url->content_type)); ?></td>
						<td><?php echo esc_html($url->created_at); ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<button type="button" onclick="submitFormWithAction('generate_descriptions')" class="button button-primary" style="margin-top: 20px;">Genera Descrizioni SEO</button>
			<button type="button" onclick="submitFormWithAction('generate_meta_tags')" class="button button-primary" style="margin-top: 20px;">Genera Meta Tag SEO</button>
			<button type="button" onclick="submitFormWithAction('delete_selected')" class="button button-secondary" style="margin-top: 20px;">Elimina Selezionati</button>
		</form>
	</div>

	<script>
		function submitFormWithAction(action) {
			document.getElementById('form-action').value = action;
			document.querySelector('form').submit();
		}
		jQuery(document).ready(function($) {
			var table = $('#url-results-table').DataTable({
				dom: 'Bfrtip',
				buttons: ['copy', 'csv', 'excel', 'print'],
				order: [[3, 'desc']],
				columnDefs: [{ orderable: false, targets: [0] }] // Disabilita ordinamento sulla colonna Seleziona
			});

			// Gestione "Select All"
			$('#select-all').on('click', function() {
				var checked = this.checked;
				$('.select-row').each(function() {
					this.checked = checked;
				});
			});

			// Assicurati che il checkbox "Select All" segua lo stato delle righe
			$('.select-row').on('change', function() {
				if (!this.checked) {
					$('#select-all').prop('checked', false);
				} else if ($('.select-row:checked').length === $('.select-row').length) {
					$('#select-all').prop('checked', true);
				}
			});

			$('#delete-selected').on('click', function() {
				var selectedIds = [];
				$('.select-row:checked').each(function() {
					selectedIds.push($(this).data('id'));
				});

				if (selectedIds.length === 0) {
					alert('Seleziona almeno un URL da eliminare.');
					return;
				}

				if (!confirm('Vuoi eliminare gli URL selezionati?')) {
					return;
				}

				$.ajax({
					url: '<?php echo esc_url(rest_url("ai-generate-seo/v1/delete-urls")); ?>',
					method: 'POST',
					beforeSend: function(xhr) {
						xhr.setRequestHeader('X-WP-Nonce', '<?php echo wp_create_nonce("wp_rest"); ?>');
					},
					data: { ids: selectedIds },
					success: function(response) {
						alert(response.success || 'URL eliminati con successo.');
						location.reload();
					},
					error: function() {
						alert('Errore durante l\'eliminazione degli URL.');
					}
				});
			});
		});
	</script>
    <?php else : ?>
    <p>Non ci sono URL nella tua lista. Puoi aggiungerne alcuni utilizzando il modulo sottostante.</p>
    <!-- Form per inserimento URL -->
    <form action="" method="post">
		<input type="hidden" name="action" value="aigsc_add_urls">
        <label for="content_type">Scegli il tipo di contenuto:</label>
        <select name="content_type" id="content_type">
            <option value="products">Prodotto</option>
            <option value="categories">Categoria</option>
        </select><br><br>
        <label for="url_list">Inserisci gli URL (uno per linea):</label><br>
        <textarea name="url_list" id="url_list" rows="10" cols="50"></textarea><br><br>
        <input type="submit" value="Salva">
    </form>
    <?php endif; ?>
<?php endif; 


	function add_urls($user_id, $content_type, $urls_list){
		global $wpdb;
		$url_list = explode("\n", trim($urls_list));
		foreach ($url_list as $url) {
			$url = trim($url);
			if (!empty($url)) {
				$wpdb->insert(
					$wpdb->prefix . 'aigsc_url_list',
					[
						'user_id' => $user_id,
						'url' => esc_url_raw($url),
						'content_type' => $content_type,
						'created_at' => current_time('mysql'),
					],
					['%d', '%s', '%s', '%s']
				);
			}
		}

		if ($wpdb->last_error) {
			echo "Si è verificato un errore: " . $wpdb->last_error;
		} else {
			echo "URL inseriti con successo!";
		}
	}

	function generate_seo_descriptions($user_id, $selected_rows) {
		// Implementa la logica per generare descrizioni SEO qui
	}

	function generate_seo_meta_tags($user_id, $selected_rows) {
		// Implementa la logica per generare meta tag SEO qui
	}

	function delete_selected_rows($user_id, $selected_rows) {
		global $wpdb;
		foreach ($selected_rows as $row_id) {
			$wpdb->delete($wpdb->prefix . 'aigsc_url_list', ['id' => $row_id], ['%d']);
		}
		echo "Selezioni eliminate con successo!";
	}


?>


