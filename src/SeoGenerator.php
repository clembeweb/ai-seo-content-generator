<?php
namespace AiGSC;

use OpenAI\Client;
use WP_Error;

class SeoGenerator {
    private \wpdb $db;
    private string $table;
    private Client $client;

    public function __construct( \wpdb $db ) {
        $this->db = $db;
        $this->table = $db->prefix . 'aigsc_seo_results';

        $apiKey = get_option( 'aigsc_openai_key' );
        $apiKey = $apiKey ?: getenv( 'OPENAI_API_KEY' );
        $this->client = \OpenAI::client( $apiKey );
    }

    public function generate( string $type, string $url, string $bizInfo ) : string {
        $prompt = $this->buildPrompt( $type, $url, $bizInfo );

        try {
            $response = $this->client->chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [ 'role' => 'system', 'content' => 'You are an SEO assistant.' ],
                    [ 'role' => 'user', 'content' => $prompt ],
                ],
            ]);
            $content = $response->choices[0]->message->content ?? '';
        } catch ( \Exception $e ) {
            $content = '';
        }

        if ( $content ) {
            $this->saveResult( $url, $type, $content );
        }

        return $content;
    }

    private function buildPrompt( string $type, string $url, string $bizInfo ) : string {
        $url = esc_url_raw( $url );
        $bizInfo = wp_kses_post( $bizInfo );
        if ( 'meta' === $type ) {
            return "Generate an SEO meta description for {$url}. Business info: {$bizInfo}";
        }
        return "Generate a SEO description for {$url}. Business info: {$bizInfo}";
    }

    public function saveResult( string $url, string $type, string $content ) : void {
        $this->db->replace(
            $this->table,
            [
                'url'       => esc_url_raw( $url ),
                'type'      => sanitize_key( $type ),
                'content'   => wp_kses_post( $content ),
                'status'    => 'generated',
                'created_at'=> current_time( 'mysql' ),
            ],
            [ '%s', '%s', '%s', '%s', '%s' ]
        );
    }

    public function getResults() : array {
        return $this->db->get_results( "SELECT * FROM {$this->table} ORDER BY created_at DESC", ARRAY_A );
    }

    public function delete( array $ids ) : void {
        if ( empty( $ids ) ) {
            return;
        }
        $ids = array_map( 'absint', $ids );
        $ids = implode( ',', $ids );
        $this->db->query( "DELETE FROM {$this->table} WHERE id IN ($ids)" );
    }

    public function exportCsv() : string {
        $results = $this->getResults();
        $fh = fopen( 'php://temp', 'r+' );
        fputcsv( $fh, [ 'URL', 'Type', 'Content', 'Created At' ] );
        foreach ( $results as $row ) {
            fputcsv( $fh, [ $row['url'], $row['type'], $row['content'], $row['created_at'] ] );
        }
        rewind( $fh );
        $csv = stream_get_contents( $fh );
        fclose( $fh );
        return $csv;
    }

    public function cronRegenerate() : void {
        $threshold = gmdate( 'Y-m-d H:i:s', strtotime( '-90 days' ) );
        $rows = $this->db->get_results( $this->db->prepare( "SELECT url, type FROM {$this->table} WHERE created_at < %s", $threshold ), ARRAY_A );
        $bizInfo = ( new BusinessInfo( $this->db ) )->get();
        foreach ( $rows as $row ) {
            $this->generate( $row['type'], $row['url'], $bizInfo );
        }
    }
}
