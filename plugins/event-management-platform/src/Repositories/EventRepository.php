<?php

namespace EventManagementPlatform\Repositories;

use EventManagementPlatform\Entities\Event;
use EventManagementPlatform\Entities\Faq;
use EventManagementPlatform\Entities\Organizer;
use EventManagementPlatform\Entities\Section;

class EventRepository implements RepositoryInterface {
    private string $table_name;

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'emp_events';
    }

    public function create( array $data ): ?int {
        global $wpdb;
        $entity = new Event( $data );
        $insert_data = array_filter( $entity->toArray(), fn($val) => $val !== null );
        unset( $insert_data['id'] );

        $format = $this->getFormats( $insert_data );
        $result = $wpdb->insert( $this->table_name, $insert_data, $format );
        if ( ! $result ) {
            return null;
        }

        $event_id = $wpdb->insert_id;

        // Sync Associations
        $this->syncGallery( $event_id, $data['gallery'] ?? [] );
        $this->syncFaqs( $event_id, $data['faqs'] ?? [] );
        $this->syncOrganizers( $event_id, $data['organizer_ids'] ?? [] );
        $this->syncTags( $event_id, $data['tags'] ?? [] );
        $this->syncSections( $event_id, $data['sections'] ?? [] );

        return $event_id;
    }

    public function update( int $id, array $data ): bool {
        global $wpdb;
        $entity = new Event( $data );
        $update_data = array_filter( $entity->toArray(), fn($val) => $val !== null );
        unset( $update_data['id'], $update_data['created_at'] );

        $format = $this->getFormats( $update_data );
        $result = $wpdb->update(
            $this->table_name,
            $update_data,
            [ 'id' => $id ],
            $format,
            [ '%d' ]
        );

        if ( $result === false ) {
            return false;
        }

        // Sync Associations if passed in the data array
        if ( isset( $data['gallery'] ) ) {
            $this->syncGallery( $id, $data['gallery'] );
        }
        if ( isset( $data['faqs'] ) ) {
            $this->syncFaqs( $id, $data['faqs'] );
        }
        if ( isset( $data['organizer_ids'] ) ) {
            $this->syncOrganizers( $id, $data['organizer_ids'] );
        }
        if ( isset( $data['tags'] ) ) {
            $this->syncTags( $id, $data['tags'] );
        }
        if ( isset( $data['sections'] ) ) {
            $this->syncSections( $id, $data['sections'] );
        }

        return true;
    }

    public function delete( int $id ): bool {
        global $wpdb;

        // Clear all relationships
        $wpdb->delete( $wpdb->prefix . 'emp_event_images', [ 'event_id' => $id ], [ '%d' ] );
        $wpdb->delete( $wpdb->prefix . 'emp_event_faqs', [ 'event_id' => $id ], [ '%d' ] );
        $wpdb->delete( $wpdb->prefix . 'emp_event_organizer_map', [ 'event_id' => $id ], [ '%d' ] );
        $wpdb->delete( $wpdb->prefix . 'emp_event_tag_map', [ 'event_id' => $id ], [ '%d' ] );
        $wpdb->delete( $wpdb->prefix . 'emp_event_sections', [ 'event_id' => $id ], [ '%d' ] );

        $result = $wpdb->delete( $this->table_name, [ 'id' => $id ], [ '%d' ] );
        return $result !== false;
    }

    public function find( int $id ): ?Event {
        global $wpdb;
        $query = $wpdb->prepare( "SELECT * FROM {$this->table_name} WHERE id = %d", $id );
        $row = $wpdb->get_row( $query, ARRAY_A );

        if ( ! $row ) {
            return null;
        }

        $event = new Event( $row );
        $this->loadAssociations( $event );
        return $event;
    }

    public function findBySlug( string $slug ): ?Event {
        global $wpdb;
        $query = $wpdb->prepare( "SELECT * FROM {$this->table_name} WHERE slug = %s", $slug );
        $row = $wpdb->get_row( $query, ARRAY_A );

        if ( ! $row ) {
            return null;
        }

        $event = new Event( $row );
        $this->loadAssociations( $event );
        return $event;
    }

    public function paginate( int $page, int $per_page, array $filters = [] ): array {
        global $wpdb;

        $offset = ( $page - 1 ) * $per_page;
        $where_clauses = [];
        $placeholders = [];

        // Apply filters
        if ( ! empty( $filters['search'] ) ) {
            $where_clauses[] = "(title LIKE %s OR short_description LIKE %s)";
            $search_wildcard = '%' . $wpdb->esc_like( $filters['search'] ) . '%';
            $placeholders[] = $search_wildcard;
            $placeholders[] = $search_wildcard;
        }

        if ( ! empty( $filters['status'] ) ) {
            $where_clauses[] = "status = %s";
            $placeholders[] = $filters['status'];
        }

        if ( ! empty( $filters['venue_id'] ) ) {
            $where_clauses[] = "venue_id = %d";
            $placeholders[] = (int) $filters['venue_id'];
        }

        if ( ! empty( $filters['category_id'] ) ) {
            $where_clauses[] = "category_id = %d";
            $placeholders[] = (int) $filters['category_id'];
        }

        if ( ! empty( $filters['date_from'] ) ) {
            $where_clauses[] = "start_datetime >= %s";
            $placeholders[] = $filters['date_from'];
        }

        if ( ! empty( $filters['date_to'] ) ) {
            $where_clauses[] = "start_datetime <= %s";
            $placeholders[] = $filters['date_to'];
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
        $items_query = "SELECT * FROM {$this->table_name} $where ORDER BY start_datetime ASC, created_at DESC LIMIT %d OFFSET %d";
        $params = $placeholders;
        $params[] = $per_page;
        $params[] = $offset;

        $rows = $wpdb->get_results( $wpdb->prepare( $items_query, ...$params ), ARRAY_A );
        $items = [];
        foreach ( $rows as $row ) {
            $event = new Event( $row );
            // Optionally do not load full associations in lists to improve performance,
            // or do lazy/batch loading. For simple lists, we load minimal or full based on demand.
            $this->loadAssociations( $event );
            $items[] = $event;
        }

        return [
            'items' => $items,
            'total' => $total,
            'page'  => $page,
            'pages' => ceil( $total / $per_page ),
        ];
    }

    /**
     * Load associations into Event entity
     */
    private function loadAssociations( Event $event ): void {
        global $wpdb;

        // 1. Load Gallery Images
        $gallery_rows = $wpdb->get_results( $wpdb->prepare(
            "SELECT attachment_id FROM {$wpdb->prefix}emp_event_images WHERE event_id = %d ORDER BY sort_order ASC",
            $event->id
        ), ARRAY_A );
        $event->gallery = array_map( fn($r) => (int) $r['attachment_id'], $gallery_rows );

        // 2. Load FAQs
        $faq_rows = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}emp_event_faqs WHERE event_id = %d ORDER BY sort_order ASC",
            $event->id
        ), ARRAY_A );
        $event->faqs = array_map( fn($r) => new Faq($r), $faq_rows );

        // 3. Load Organizers
        $org_rows = $wpdb->get_results( $wpdb->prepare(
            "SELECT o.* FROM {$wpdb->prefix}emp_organizers o 
             INNER JOIN {$wpdb->prefix}emp_event_organizer_map m ON o.id = m.organizer_id 
             WHERE m.event_id = %d",
            $event->id
        ), ARRAY_A );
        $event->organizers = array_map( fn($r) => new Organizer($r), $org_rows );

        // 4. Load Tags
        $tag_rows = $wpdb->get_results( $wpdb->prepare(
            "SELECT t.name FROM {$wpdb->prefix}emp_event_tags t
             INNER JOIN {$wpdb->prefix}emp_event_tag_map m ON t.id = m.tag_id
             WHERE m.event_id = %d",
            $event->id
        ), ARRAY_A );
        $event->tags = array_map( fn($r) => $r['name'], $tag_rows );

        // 5. Load Sections
        $section_rows = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}emp_event_sections WHERE event_id = %d ORDER BY sort_order ASC",
            $event->id
        ), ARRAY_A );
        $event->sections = array_map( fn($r) => new Section( $r ), $section_rows );
    }

    /**
     * Sync gallery images
     */
    private function syncGallery( int $event_id, array $attachment_ids ): void {
        global $wpdb;
        $table = $wpdb->prefix . 'emp_event_images';
        $wpdb->delete( $table, [ 'event_id' => $event_id ], [ '%d' ] );

        foreach ( $attachment_ids as $order => $attachment_id ) {
            $wpdb->insert( $table, [
                'event_id'      => $event_id,
                'attachment_id' => (int) $attachment_id,
                'sort_order'    => (int) $order
            ] );
        }
    }

    /**
     * Sync FAQs
     */
    private function syncFaqs( int $event_id, array $faqs ): void {
        global $wpdb;
        $table = $wpdb->prefix . 'emp_event_faqs';
        $wpdb->delete( $table, [ 'event_id' => $event_id ], [ '%d' ] );

        foreach ( $faqs as $order => $faq_data ) {
            $question = sanitize_text_field( $faq_data['question'] ?? '' );
            $answer   = wp_kses_post( $faq_data['answer'] ?? '' );

            if ( empty( $question ) || empty( $answer ) ) {
                continue;
            }

            $wpdb->insert( $table, [
                'event_id'   => $event_id,
                'question'   => $question,
                'answer'     => $answer,
                'sort_order' => (int) $order
            ] );
        }
    }

    /**
     * Sync Organizers
     */
    private function syncOrganizers( int $event_id, array $organizer_ids ): void {
        global $wpdb;
        $table = $wpdb->prefix . 'emp_event_organizer_map';
        $wpdb->delete( $table, [ 'event_id' => $event_id ], [ '%d' ] );

        foreach ( $organizer_ids as $organizer_id ) {
            $wpdb->insert( $table, [
                'event_id'     => $event_id,
                'organizer_id' => (int) $organizer_id
            ] );
        }
    }

    /**
     * Sync Tags
     */
    private function syncTags( int $event_id, array $tags ): void {
        global $wpdb;
        $map_table = $wpdb->prefix . 'emp_event_tag_map';
        $tag_table = $wpdb->prefix . 'emp_event_tags';

        $wpdb->delete( $map_table, [ 'event_id' => $event_id ], [ '%d' ] );

        foreach ( $tags as $tag_name ) {
            $tag_name = sanitize_text_field( trim( $tag_name ) );
            if ( empty( $tag_name ) ) {
                continue;
            }
            $slug = sanitize_title( $tag_name );

            // Check if tag exists
            $tag_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $tag_table WHERE slug = %s", $slug ) );

            if ( ! $tag_id ) {
                $wpdb->insert( $tag_table, [
                    'name' => $tag_name,
                    'slug' => $slug
                ] );
                $tag_id = $wpdb->insert_id;
            }

            $wpdb->insert( $map_table, [
                'event_id' => $event_id,
                'tag_id'   => (int) $tag_id
            ] );
        }
    }

    /**
     * Sync event content sections
     */
    private function syncSections( int $event_id, array $sections ): void {
        global $wpdb;
        $table = $wpdb->prefix . 'emp_event_sections';
        $wpdb->delete( $table, [ 'event_id' => $event_id ], [ '%d' ] );

        foreach ( $sections as $order => $section_data ) {
            $title   = sanitize_text_field( $section_data['title'] ?? '' );
            $content = wp_kses_post( $section_data['content'] ?? '' );

            if ( empty( $title ) && empty( $content ) ) {
                continue;
            }

            $wpdb->insert( $table, [
                'event_id'   => $event_id,
                'title'      => $title,
                'content'    => $content,
                'sort_order' => (int) $order,
            ] );
        }
    }

    /**
     * Get format specifiers for database fields
     */
    private function getFormats( array $data ): array {
        $formats = [];
        $numeric_fields = [ 'id', 'venue_id', 'category_id', 'featured_image_id' ];

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
