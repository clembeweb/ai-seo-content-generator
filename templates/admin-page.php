<?php
/** @var string $tab */
/** @var \AiGSC\Plugin $plugin */
?>
<div class="wrap aigsc-admin">
    <h1><?php esc_html_e( 'AI Generate SEO Content', 'ai-generate-seo-content' ); ?></h1>
    <?php settings_errors( 'aigsc_messages' ); ?>
    <h2 class="nav-tab-wrapper">
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=aigsc_admin&tab=business' ) ); ?>" class="nav-tab <?php echo 'business' === $tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Business Info', 'ai-generate-seo-content' ); ?></a>
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=aigsc_admin&tab=urls' ) ); ?>" class="nav-tab <?php echo 'urls' === $tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'URL List', 'ai-generate-seo-content' ); ?></a>
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=aigsc_admin&tab=results' ) ); ?>" class="nav-tab <?php echo 'results' === $tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'SEO Results', 'ai-generate-seo-content' ); ?></a>
    </h2>
    <div class="aigsc-tab-content">
        <?php
        if ( 'business' === $tab ) {
            load_template( AIGSC_PATH . 'templates/tab-business.php', false, [ 'info' => $plugin->getBusinessInfo() ] );
        } elseif ( 'urls' === $tab ) {
            load_template( AIGSC_PATH . 'templates/tab-urls.php', false, [ 'urls' => $plugin->getUrls() ] );
        } else {
            load_template( AIGSC_PATH . 'templates/tab-results.php', false, [ 'results' => $plugin->getResults() ] );
        }
        ?>
    </div>
</div>
