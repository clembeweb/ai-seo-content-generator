jQuery(function($){
    const restUrl = AiGSC.restUrl;
    const nonce = AiGSC.nonce;

    if ($.fn.DataTable) {
        $('#aigsc-url-table').DataTable();
        $('#aigsc-results-table').DataTable({
            dom: 'Bfrtip',
            buttons: ['csv']
        });
    }

    $('#aigsc-select-all').on('change', function(){
        $('.aigsc-row-id').prop('checked', $(this).is(':checked'));
    });

    $('#aigsc-generate-selected').on('click', function(e){
        e.preventDefault();
        const ids = $('.aigsc-row-id:checked').map(function(){ return $(this).val(); }).get();
        ids.forEach(id => {
            const row = $('#aigsc-results-table input[value="'+id+'"]').closest('tr');
            const url = row.find('td').eq(1).text();
            row.addClass('opacity-50');
            $.ajax({
                method: 'POST',
                url: restUrl + 'generate',
                beforeSend: xhr => xhr.setRequestHeader('X-WP-Nonce', nonce),
                data: { type: 'description', url: url },
                success: res => {
                    row.removeClass('opacity-50');
                    row.find('td').eq(3).text(res.content);
                },
                error: () => {
                    row.removeClass('opacity-50');
                    alert('Generation failed');
                }
            });
        });
    });

    $('#aigsc-export').on('click', function(e){
        e.preventDefault();
        window.location = restUrl + 'export?_wpnonce=' + nonce;
    });
});
