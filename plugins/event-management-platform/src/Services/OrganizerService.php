<?php

namespace EventManagementPlatform\Services;

use EventManagementPlatform\Repositories\OrganizerRepository;
use EventManagementPlatform\Validators\OrganizerValidator;
use Exception;

class OrganizerService {
    private OrganizerRepository $repository;

    public function __construct() {
        $this->repository = new OrganizerRepository();
    }

    public function createOrganizer( array $data ) {
        $validator = new OrganizerValidator();
        if ( ! $validator->validate( $data ) ) {
            throw new Exception( json_encode( $validator->getErrors() ) );
        }

        $sanitized = [
            'name'    => sanitize_text_field( $data['name'] ),
            'email'   => ! empty( $data['email'] ) ? sanitize_email( $data['email'] ) : null,
            'phone'   => ! empty( $data['phone'] ) ? sanitize_text_field( $data['phone'] ) : null,
            'website' => ! empty( $data['website'] ) ? esc_url_raw( $data['website'] ) : null,
        ];

        return $this->repository->create( $sanitized );
    }

    public function updateOrganizer( int $id, array $data ): bool {
        $validator = new OrganizerValidator();
        if ( ! $validator->validate( $data ) ) {
            throw new Exception( json_encode( $validator->getErrors() ) );
        }

        $sanitized = [
            'name'    => sanitize_text_field( $data['name'] ),
            'email'   => ! empty( $data['email'] ) ? sanitize_email( $data['email'] ) : null,
            'phone'   => ! empty( $data['phone'] ) ? sanitize_text_field( $data['phone'] ) : null,
            'website' => ! empty( $data['website'] ) ? esc_url_raw( $data['website'] ) : null,
        ];

        return $this->repository->update( $id, $sanitized );
    }

    public function deleteOrganizer( int $id ): bool {
        return $this->repository->delete( $id );
    }

    public function getOrganizer( int $id ) {
        return $this->repository->find( $id );
    }

    public function getOrganizerRepository(): OrganizerRepository {
        return $this->repository;
    }
}
