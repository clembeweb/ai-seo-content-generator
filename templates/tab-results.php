<?php
/** @var array $results */
?>
<div class="mt-4">
    <button id="aigsc-generate-selected" class="button button-primary"><?php esc_html_e( 'Generate', 'ai-generate-seo-content' ); ?></button>
    <button id="aigsc-export" class="button"><?php esc_html_e( 'Export CSV', 'ai-generate-seo-content' ); ?></button>
</div>
<table id="aigsc-results-table" class="wp-list-table widefat striped mt-4">
    <thead>
        <tr>
            <th><input type="checkbox" id="aigsc-select-all" /></th>
            <th><?php esc_html_e( 'URL', 'ai-generate-seo-content' ); ?></th>
            <th><?php esc_html_e( 'Type', 'ai-generate-seo-content' ); ?></th>
            <th><?php esc_html_e( 'Content', 'ai-generate-seo-content' ); ?></th>
            <th><?php esc_html_e( 'Created', 'ai-generate-seo-content' ); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ( $results as $row ) : ?>
            <tr>
                <td><input type="checkbox" class="aigsc-row-id" value="<?php echo (int) $row['id']; ?>" /></td>
                <td><?php echo esc_url( $row['url'] ); ?></td>
                <td><?php echo esc_html( $row['type'] ); ?></td>
                <td><?php echo esc_html( $row['content'] ); ?></td>
                <td><?php echo esc_html( $row['created_at'] ); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
