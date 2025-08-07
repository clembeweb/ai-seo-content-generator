<?php
namespace AiGSC;

use WP_REST_Request;
use WP_REST_Response;

class RestController {
    private SeoGenerator $seoGenerator;
    private UrlManager $urlManager;

    public function __construct( SeoGenerator $seoGenerator, UrlManager $urlManager ) {
        $this->seoGenerator = $seoGenerator;
        $this->urlManager   = $urlManager;
    }

    public function registerRoutes() : void {
        register_rest_route( 'aigsc/v1', '/generate', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'generate' ],
            'permission_callback' => [ $this, 'permissions' ],
        ] );

        register_rest_route( 'aigsc/v1', '/delete', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'delete' ],
            'permission_callback' => [ $this, 'permissions' ],
        ] );

        register_rest_route( 'aigsc/v1', '/export', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'export' ],
            'permission_callback' => [ $this, 'permissions' ],
        ] );
    }

    public function permissions() : bool {
        return current_user_can( 'manage_options' );
    }

    public function generate( WP_REST_Request $request ) : WP_REST_Response {
        $type = sanitize_key( $request->get_param( 'type' ) );
        $url  = esc_url_raw( $request->get_param( 'url' ) );
        $biz  = ( new BusinessInfo( $GLOBALS['wpdb'] ) )->get();

        $content = $this->seoGenerator->generate( $type, $url, $biz );

        return new WP_REST_Response( [ 'content' => $content ], 200 );
    }

    public function delete( WP_REST_Request $request ) : WP_REST_Response {
        $ids = (array) $request->get_param( 'ids' );
        $this->seoGenerator->delete( $ids );
        return new WP_REST_Response( [ 'deleted' => $ids ], 200 );
    }

    public function export( WP_REST_Request $request ) {
        check_ajax_referer( 'wp_rest' );
        $csv = $this->seoGenerator->exportCsv();
        return new WP_REST_Response( $csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="seo-results.csv"',
        ] );
    }
}
