<?php
namespace AiGSC;

class Plugin {
    private BusinessInfo $businessInfo;
    private UrlManager $urlManager;
    private SeoGenerator $seoGenerator;
    private RestController $restController;

    public function __construct() {
        global $wpdb;
        $this->businessInfo = new BusinessInfo( $wpdb );
        $this->urlManager   = new UrlManager( $wpdb );
        $this->seoGenerator = new SeoGenerator( $wpdb );
        $this->restController = new RestController( $this->seoGenerator, $this->urlManager );
    }

    public function init() : void {
        add_action( 'admin_menu', [ $this, 'registerMenu' ] );
        add_action( 'admin_init', [ $this, 'handleFormSubmissions' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueueAssets' ] );
        add_action( 'rest_api_init', [ $this->restController, 'registerRoutes' ] );
        add_action( 'aigsc_daily_event', [ $this->seoGenerator, 'cronRegenerate' ] );
    }

    public function registerMenu() : void {
        add_menu_page(
            __( 'AI SEO Content', 'ai-generate-seo-content' ),
            __( 'AI SEO', 'ai-generate-seo-content' ),
            'manage_options',
            'aigsc_admin',
            [ $this, 'renderAdminPage' ],
            'dashicons-analytics',
            80
        );
    }

    public function renderAdminPage() : void {
        $tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'business';
        load_template( AIGSC_PATH . 'templates/admin-page.php', false, [
            'tab'    => $tab,
            'plugin' => $this,
        ] );
    }

    public function enqueueAssets( string $hook ) : void {
        if ( 'toplevel_page_aigsc_admin' !== $hook ) {
            return;
        }
        wp_enqueue_script( 'tailwind', 'https://cdn.tailwindcss.com', [], null, true );
        wp_enqueue_style( 'datatables', 'https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css', [], '1.13.4' );
        wp_enqueue_style( 'datatables-buttons', 'https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css', [], '2.3.6' );
        wp_enqueue_style( 'aigsc-admin', AIGSC_URL . 'assets/css/aigsc-admin.css', [], '2.0.0' );

        wp_enqueue_script( 'datatables', 'https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js', [ 'jquery' ], '1.13.4', true );
        wp_enqueue_script( 'datatables-buttons', 'https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js', [ 'datatables' ], '2.3.6', true );
        wp_enqueue_script( 'datatables-buttons-html5', 'https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js', [ 'datatables-buttons' ], '2.3.6', true );
        wp_enqueue_script( 'aigsc-admin', AIGSC_URL . 'assets/js/aigsc-admin.js', [ 'jquery' ], '2.0.0', true );
        wp_localize_script( 'aigsc-admin', 'AiGSC', [
            'restUrl' => esc_url_raw( rest_url( 'aigsc/v1/' ) ),
            'nonce'   => wp_create_nonce( 'wp_rest' ),
        ] );
    }

    public function handleFormSubmissions() : void {
        if ( isset( $_POST['aigsc_business_info_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['aigsc_business_info_nonce'] ) ), 'aigsc_save_business_info' ) ) {
            if ( current_user_can( 'manage_options' ) ) {
                $info = isset( $_POST['business_info'] ) ? sanitize_textarea_field( wp_unslash( $_POST['business_info'] ) ) : '';
                $this->businessInfo->save( $info );
                add_settings_error( 'aigsc_messages', 'aigsc_info_saved', __( 'Business information saved', 'ai-generate-seo-content' ), 'updated' );
            }
        }

        if ( isset( $_POST['aigsc_url_list_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['aigsc_url_list_nonce'] ) ), 'aigsc_add_urls' ) ) {
            if ( current_user_can( 'manage_options' ) ) {
                $urls_raw = isset( $_POST['url_list'] ) ? wp_unslash( $_POST['url_list'] ) : '';
                $urls = array_filter( array_map( 'trim', explode( "\n", $urls_raw ) ) );
                $this->urlManager->addUrls( $urls );
                add_settings_error( 'aigsc_messages', 'aigsc_urls_added', __( 'URLs added', 'ai-generate-seo-content' ), 'updated' );
            }
        }
    }

    public static function activate() : void {
        $database = new Database();
        $database->createTables();
        if ( ! wp_next_scheduled( 'aigsc_daily_event' ) ) {
            wp_schedule_event( time(), 'daily', 'aigsc_daily_event' );
        }
    }

    public static function deactivate() : void {
        wp_clear_scheduled_hook( 'aigsc_daily_event' );
    }

    public function getBusinessInfo() : string {
        return $this->businessInfo->get();
    }

    public function getUrls() : array {
        return $this->urlManager->all();
    }

    public function getResults() : array {
        return $this->seoGenerator->getResults();
    }
}
