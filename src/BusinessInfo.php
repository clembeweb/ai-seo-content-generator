<?php
namespace AiGSC;

class BusinessInfo {
    private \wpdb $db;
    private string $table;

    public function __construct( \wpdb $db ) {
        $this->db    = $db;
        $this->table = $db->prefix . 'aigsc_business_info';
    }

    public function get() : string {
        $row = $this->db->get_row( "SELECT info FROM {$this->table} LIMIT 1", ARRAY_A );
        return $row['info'] ?? '';
    }

    public function save( string $info ) : void {
        $existing = $this->db->get_var( "SELECT id FROM {$this->table} LIMIT 1" );
        if ( $existing ) {
            $this->db->update(
                $this->table,
                [ 'info' => $info, 'updated_at' => current_time( 'mysql' ) ],
                [ 'id' => $existing ],
                [ '%s', '%s' ],
                [ '%d' ]
            );
        } else {
            $this->db->insert(
                $this->table,
                [ 'info' => $info, 'updated_at' => current_time( 'mysql' ) ],
                [ '%s', '%s' ]
            );
        }
    }
}
