<?php
/** @var array $urls */
?>
<form method="post" class="mt-4">
    <?php wp_nonce_field( 'aigsc_add_urls', 'aigsc_url_list_nonce' ); ?>
    <textarea name="url_list" rows="5" placeholder="<?php esc_attr_e( "https://example.com/page-1\nhttps://example.com/page-2", 'ai-generate-seo-content' ); ?>" class="large-text"></textarea>
    <?php submit_button( __( 'Add URLs', 'ai-generate-seo-content' ) ); ?>
</form>

<table id="aigsc-url-table" class="wp-list-table widefat striped mt-4">
    <thead>
        <tr>
            <th><?php esc_html_e( 'ID', 'ai-generate-seo-content' ); ?></th>
            <th><?php esc_html_e( 'URL', 'ai-generate-seo-content' ); ?></th>
            <th><?php esc_html_e( 'Created', 'ai-generate-seo-content' ); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ( $urls as $url ) : ?>
            <tr>
                <td><?php echo (int) $url['id']; ?></td>
                <td><?php echo esc_url( $url['url'] ); ?></td>
                <td><?php echo esc_html( $url['created_at'] ); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
