<?php
/** @var string $info */
?>
<form method="post" class="mt-4">
    <?php wp_nonce_field( 'aigsc_save_business_info', 'aigsc_business_info_nonce' ); ?>
    <textarea name="business_info" rows="6" placeholder="<?php esc_attr_e( 'Describe your business…', 'ai-generate-seo-content' ); ?>" class="large-text"><?php echo esc_textarea( $info ); ?></textarea>
    <?php submit_button( __( 'Save', 'ai-generate-seo-content' ) ); ?>
</form>
