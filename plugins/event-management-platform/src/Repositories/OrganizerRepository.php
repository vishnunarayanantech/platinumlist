<?php

namespace EventManagementPlatform\Repositories;

use EventManagementPlatform\Entities\Organizer;

class OrganizerRepository implements RepositoryInterface {
    private string $table_name;

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'emp_organizers';
    }

    public function create( array $data ): ?int {
        global $wpdb;
        $entity = new Organizer( $data );
        $insert_data = array_filter( $entity->toArray(), fn($val) => $val !== null );
        unset( $insert_data['id'] );

        $format = $this->getFormats( $insert_data );
        $result = $wpdb->insert( $this->table_name, $insert_data, $format );
        return $result ? $wpdb->insert_id : null;
    }

    public function update( int $id, array $data ): bool {
        global $wpdb;
        $entity = new Organizer( $data );
        $update_data = array_filter( $entity->toArray(), fn($val) => $val !== null );
        unset( $update_data['id'] );

        $format = $this->getFormats( $update_data );
        $result = $wpdb->update(
            $this->table_name,
            $update_data,
            [ 'id' => $id ],
            $format,
            [ '%d' ]
        );
        return $result !== false;
    }

    public function delete( int $id ): bool {
        global $wpdb;

        // Delete references in event_organizer_map first
        $wpdb->delete(
            $wpdb->prefix . 'emp_event_organizer_map',
            [ 'organizer_id' => $id ],
            [ '%d' ]
        );

        $result = $wpdb->delete( $this->table_name, [ 'id' => $id ], [ '%d' ] );
        return $result !== false;
    }

    public function find( int $id ): ?Organizer {
        global $wpdb;
        $query = $wpdb->prepare( "SELECT * FROM {$this->table_name} WHERE id = %d", $id );
        $row = $wpdb->get_row( $query, ARRAY_A );

        return $row ? new Organizer( $row ) : null;
    }

    public function findBySlug( string $slug ) {
        return null;
    }

    public function paginate( int $page, int $per_page, array $filters = [] ): array {
        global $wpdb;

        $offset = ( $page - 1 ) * $per_page;
        $where_clauses = [];
        $placeholders = [];

        if ( ! empty( $filters['search'] ) ) {
            $where_clauses[] = "(name LIKE %s OR email LIKE %s)";
            $search_wildcard = '%' . $wpdb->esc_like( $filters['search'] ) . '%';
            $placeholders[] = $search_wildcard;
            $placeholders[] = $search_wildcard;
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
        $items_query = "SELECT * FROM {$this->table_name} $where ORDER BY name ASC LIMIT %d OFFSET %d";
        $params = $placeholders;
        $params[] = $per_page;
        $params[] = $offset;

        $rows = $wpdb->get_results( $wpdb->prepare( $items_query, ...$params ), ARRAY_A );
        $items = array_map( fn( $row ) => new Organizer( $row ), $rows );

        return [
            'items' => $items,
            'total' => $total,
            'page'  => $page,
            'pages' => ceil( $total / $per_page ),
        ];
    }

    public function all(): array {
        global $wpdb;
        $rows = $wpdb->get_results( "SELECT * FROM {$this->table_name} ORDER BY name ASC", ARRAY_A );
        return array_map( fn( $row ) => new Organizer( $row ), $rows );
    }

    /**
     * Get format specifiers for database fields
     */
    private function getFormats( array $data ): array {
        $formats = [];
        $numeric_fields = [ 'id' ];

        foreach ( array_keys( $data ) as $field ) {
            if ( in_array( $field, $numeric_fields, true ) ) {
                $formats[] = '%d';
            } else {
                $formats[] = '%s';
            }
        }

        return $formats;
    }
}
