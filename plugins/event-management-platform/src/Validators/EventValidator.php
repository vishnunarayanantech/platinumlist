<?php

namespace EventManagementPlatform\Validators;

class EventValidator implements ValidatorInterface {
    private array $errors = [];
    private ?int $exclude_id = null;

    public function __construct( ?int $exclude_id = null ) {
        $this->exclude_id = $exclude_id;
    }

    public function validate( array $data ): bool {
        $this->errors = [];

        // Title validation
        if ( empty( $data['title'] ) ) {
            $this->errors['title'] = __( 'Title is required.', 'event-management-platform' );
        }

        // Slug validation
        if ( empty( $data['slug'] ) ) {
            $this->errors['slug'] = __( 'Slug is required.', 'event-management-platform' );
        } elseif ( ! preg_match( '/^[a-z0-9-_]+$/', $data['slug'] ) ) {
            $this->errors['slug'] = __( 'Slug format is invalid. Use lowercase letters, numbers, hyphens, and underscores.', 'event-management-platform' );
        } elseif ( $this->slugExists( $data['slug'] ) ) {
            $this->errors['slug'] = __( 'Slug must be unique.', 'event-management-platform' );
        }

        // Status validation
        $valid_statuses = [ 'draft', 'published', 'cancelled' ];
        if ( ! empty( $data['status'] ) && ! in_array( $data['status'], $valid_statuses, true ) ) {
            $this->errors['status'] = sprintf(
                __( 'Invalid status. Allowed values: %s.', 'event-management-platform' ),
                implode( ', ', $valid_statuses )
            );
        }

        // Datetime validation
        if ( ! empty( $data['start_datetime'] ) && ! empty( $data['end_datetime'] ) ) {
            $start = strtotime( $data['start_datetime'] );
            $end   = strtotime( $data['end_datetime'] );

            if ( $start && $end && $end <= $start ) {
                $this->errors['end_datetime'] = __( 'End datetime must be after start datetime.', 'event-management-platform' );
            }
        }

        return empty( $this->errors );
    }

    public function getErrors(): array {
        return $this->errors;
    }

    private function slugExists( string $slug ): bool {
        global $wpdb;
        $table_name = $wpdb->prefix . 'emp_events';

        if ( $this->exclude_id ) {
            $query = $wpdb->prepare( "SELECT COUNT(*) FROM $table_name WHERE slug = %s AND id != %d", $slug, $this->exclude_id );
        } else {
            $query = $wpdb->prepare( "SELECT COUNT(*) FROM $table_name WHERE slug = %s", $slug );
        }

        return (int) $wpdb->get_var( $query ) > 0;
    }
}
