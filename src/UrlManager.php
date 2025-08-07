<?php
namespace AiGSC;

class UrlManager {
    private \wpdb $db;
    private string $table;

    public function __construct( \wpdb $db ) {
        $this->db = $db;
        $this->table = $db->prefix . 'aigsc_urls';
    }

    public function addUrls( array $urls ) : void {
        $urls = array_unique( $urls );
        foreach ( $urls as $url ) {
            $sanitized = esc_url_raw( $url );
            if ( empty( $sanitized ) ) {
                continue;
            }
            $this->db->insert(
                $this->table,
                [ 'url' => $sanitized, 'created_at' => current_time( 'mysql' ) ],
                [ '%s', '%s' ]
            );
        }
    }

    public function all() : array {
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
}
