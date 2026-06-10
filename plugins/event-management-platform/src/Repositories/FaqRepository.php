<?php

namespace EventManagementPlatform\Repositories;

use EventManagementPlatform\Entities\Faq;

class FaqRepository implements RepositoryInterface {
    private string $table_name;

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'emp_event_faqs';
    }

    public function create( array $data ): ?int {
        global $wpdb;
        $entity = new Faq( $data );
        $insert_data = array_filter( $entity->toArray(), fn($val) => $val !== null );
        unset( $insert_data['id'] );

        $result = $wpdb->insert( $this->table_name, $insert_data );
        return $result ? $wpdb->insert_id : null;
    }

    public function update( int $id, array $data ): bool {
        global $wpdb;
        $entity = new Faq( $data );
        $update_data = array_filter( $entity->toArray(), fn($val) => $val !== null );
        unset( $update_data['id'] );

        $result = $wpdb->update(
            $this->table_name,
            $update_data,
            [ 'id' => $id ],
            null,
            [ '%d' ]
        );
        return $result !== false;
    }

    public function delete( int $id ): bool {
        global $wpdb;
        $result = $wpdb->delete( $this->table_name, [ 'id' => $id ], [ '%d' ] );
        return $result !== false;
    }

    public function find( int $id ): ?Faq {
        global $wpdb;
        $query = $wpdb->prepare( "SELECT * FROM {$this->table_name} WHERE id = %d", $id );
        $row = $wpdb->get_row( $query, ARRAY_A );

        return $row ? new Faq( $row ) : null;
    }

    public function findBySlug( string $slug ) {
        return null;
    }

    public function paginate( int $page, int $per_page, array $filters = [] ): array {
        global $wpdb;

        $offset = ( $page - 1 ) * $per_page;
        $where_clauses = [];
        $placeholders = [];

        if ( ! empty( $filters['event_id'] ) ) {
            $where_clauses[] = "event_id = %d";
            $placeholders[] = (int) $filters['event_id'];
        }

        $where = ! empty( $where_clauses ) ? 'WHERE ' . implode( ' AND ', $where_clauses ) : '';

        // Total count query
        $count_query = "SELECT COUNT(*) FROM {$this->table_name} $where";
        if ( ! empty( $placeholders ) ) {
            $total = (int) $wpdb->get_var( $wpdb->prepare( $count_query, ...$placeholders ) );
        } else {
            $total = (int) $wpdb->get_var( $count_query );
        }

        // Paginated items query
        $items_query = "SELECT * FROM {$this->table_name} $where ORDER BY sort_order ASC LIMIT %d OFFSET %d";
        $params = $placeholders;
        $params[] = $per_page;
        $params[] = $offset;

        $rows = $wpdb->get_results( $wpdb->prepare( $items_query, ...$params ), ARRAY_A );
        $items = array_map( fn( $row ) => new Faq( $row ), $rows );

        return [
            'items' => $items,
            'total' => $total,
            'page'  => $page,
            'pages' => ceil( $total / $per_page ),
        ];
    }

    /**
     * Get all FAQs for an event
     */
    public function getForEvent( int $event_id ): array {
        global $wpdb;
        $query = $wpdb->prepare( "SELECT * FROM {$this->table_name} WHERE event_id = %d ORDER BY sort_order ASC", $event_id );
        $rows = $wpdb->get_results( $query, ARRAY_A );
        return array_map( fn( $row ) => new Faq( $row ), $rows );
    }

    /**
     * Delete all FAQs for an event
     */
    public function deleteForEvent( int $event_id ): bool {
        global $wpdb;
        $result = $wpdb->delete( $this->table_name, [ 'event_id' => $event_id ], [ '%d' ] );
        return $result !== false;
    }
}
