<?php

namespace EventManagementPlatform\Services;

use EventManagementPlatform\Repositories\EventRepository;
use EventManagementPlatform\Validators\EventValidator;
use Exception;

class EventService {
    private EventRepository $repository;

    public function __construct() {
        $this->repository = new EventRepository();
    }

    public function createEvent( array $data ) {
        // Auto-generate slug if not provided
        if ( empty( $data['slug'] ) && ! empty( $data['title'] ) ) {
            $data['slug'] = sanitize_title( $data['title'] );
        }

        $validator = new EventValidator();
        if ( ! $validator->validate( $data ) ) {
            throw new Exception( json_encode( $validator->getErrors() ) );
        }

        $sanitized = $this->sanitizeEventData( $data );
        return $this->repository->create( $sanitized );
    }

    public function updateEvent( int $id, array $data ): bool {
        // Auto-generate slug if not provided
        if ( empty( $data['slug'] ) && ! empty( $data['title'] ) ) {
            $data['slug'] = sanitize_title( $data['title'] );
        }

        $validator = new EventValidator( $id );
        if ( ! $validator->validate( $data ) ) {
            throw new Exception( json_encode( $validator->getErrors() ) );
        }

        $sanitized = $this->sanitizeEventData( $data );
        return $this->repository->update( $id, $sanitized );
    }

    public function deleteEvent( int $id ): bool {
        return $this->repository->delete( $id );
    }

    public function getEvent( int $id ) {
        return $this->repository->find( $id );
    }

    public function getEventBySlug( string $slug ) {
        return $this->repository->findBySlug( $slug );
    }

    public function getEventRepository(): EventRepository {
        return $this->repository;
    }

    /**
     * Helper to sanitize all incoming fields for Event entity
     */
    private function sanitizeEventData( array $data ): array {
        $sanitized = [
            'title'             => sanitize_text_field( $data['title'] ),
            'slug'              => sanitize_title( $data['slug'] ),
            'short_description' => sanitize_textarea_field( $data['short_description'] ?? '' ),
            'full_description'  => wp_kses_post( $data['full_description'] ?? '' ),
            'venue_id'          => ! empty( $data['venue_id'] ) ? (int) $data['venue_id'] : null,
            'category_id'       => ! empty( $data['category_id'] ) ? (int) $data['category_id'] : null,
            'status'            => sanitize_text_field( $data['status'] ?? 'draft' ),
            'featured_image_id' => ! empty( $data['featured_image_id'] ) ? (int) $data['featured_image_id'] : null,
            'start_datetime'    => ! empty( $data['start_datetime'] ) ? sanitize_text_field( $data['start_datetime'] ) : null,
            'end_datetime'      => ! empty( $data['end_datetime'] ) ? sanitize_text_field( $data['end_datetime'] ) : null,
            'timezone'          => sanitize_text_field( $data['timezone'] ?? 'UTC' ),
            'seo_title'         => sanitize_text_field( $data['seo_title'] ?? '' ),
            'seo_description'   => sanitize_textarea_field( $data['seo_description'] ?? '' ),
        ];

        // Pass associations directly so the Repository can sync them
        if ( isset( $data['gallery'] ) && is_array( $data['gallery'] ) ) {
            $sanitized['gallery'] = array_map( 'intval', $data['gallery'] );
        }
        if ( isset( $data['faqs'] ) && is_array( $data['faqs'] ) ) {
            $sanitized['faqs'] = [];
            foreach ( $data['faqs'] as $faq ) {
                if ( ! empty( $faq['question'] ) && ! empty( $faq['answer'] ) ) {
                    $sanitized['faqs'][] = [
                        'question' => sanitize_text_field( $faq['question'] ),
                        'answer'   => wp_kses_post( $faq['answer'] ),
                    ];
                }
            }
        }
        if ( isset( $data['organizer_ids'] ) && is_array( $data['organizer_ids'] ) ) {
            $sanitized['organizer_ids'] = array_map( 'intval', $data['organizer_ids'] );
        }
        if ( isset( $data['tags'] ) ) {
            if ( is_string( $data['tags'] ) ) {
                $sanitized['tags'] = array_filter( array_map( 'trim', explode( ',', $data['tags'] ) ) );
            } elseif ( is_array( $data['tags'] ) ) {
                $sanitized['tags'] = array_map( 'sanitize_text_field', $data['tags'] );
            }
        }

        return $sanitized;
    }
}
