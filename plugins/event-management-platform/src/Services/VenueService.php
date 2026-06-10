<?php

namespace EventManagementPlatform\Services;

use EventManagementPlatform\Repositories\VenueRepository;
use EventManagementPlatform\Validators\VenueValidator;
use Exception;

class VenueService {
    private VenueRepository $repository;

    public function __construct() {
        $this->repository = new VenueRepository();
    }

    public function createVenue( array $data ) {
        $validator = new VenueValidator();
        if ( ! $validator->validate( $data ) ) {
            throw new Exception( json_encode( $validator->getErrors() ) );
        }

        // Sanitization
        $sanitized = [
            'name'           => sanitize_text_field( $data['name'] ),
            'address'        => sanitize_textarea_field( $data['address'] ?? '' ),
            'city'           => sanitize_text_field( $data['city'] ?? '' ),
            'country'        => sanitize_text_field( $data['country'] ?? '' ),
            'latitude'       => isset( $data['latitude'] ) && $data['latitude'] !== '' ? (float) $data['latitude'] : null,
            'longitude'      => isset( $data['longitude'] ) && $data['longitude'] !== '' ? (float) $data['longitude'] : null,
            'google_map_url' => esc_url_raw( $data['google_map_url'] ?? '' ),
        ];

        return $this->repository->create( $sanitized );
    }

    public function updateVenue( int $id, array $data ): bool {
        $validator = new VenueValidator();
        if ( ! $validator->validate( $data ) ) {
            throw new Exception( json_encode( $validator->getErrors() ) );
        }

        $sanitized = [
            'name'           => sanitize_text_field( $data['name'] ),
            'address'        => sanitize_textarea_field( $data['address'] ?? '' ),
            'city'           => sanitize_text_field( $data['city'] ?? '' ),
            'country'        => sanitize_text_field( $data['country'] ?? '' ),
            'latitude'       => isset( $data['latitude'] ) && $data['latitude'] !== '' ? (float) $data['latitude'] : null,
            'longitude'      => isset( $data['longitude'] ) && $data['longitude'] !== '' ? (float) $data['longitude'] : null,
            'google_map_url' => esc_url_raw( $data['google_map_url'] ?? '' ),
        ];

        return $this->repository->update( $id, $sanitized );
    }

    public function deleteVenue( int $id ): bool {
        return $this->repository->delete( $id );
    }

    public function getVenue( int $id ) {
        return $this->repository->find( $id );
    }

    public function getVenueRepository(): VenueRepository {
        return $this->repository;
    }
}
