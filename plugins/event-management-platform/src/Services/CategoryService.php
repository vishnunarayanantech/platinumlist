<?php

namespace EventManagementPlatform\Services;

use EventManagementPlatform\Repositories\CategoryRepository;
use Exception;

class CategoryService {
    private CategoryRepository $repository;

    public function __construct() {
        $this->repository = new CategoryRepository();
    }

    public function createCategory( array $data ) {
        $errors = [];
        if ( empty( $data['name'] ) ) {
            $errors['name'] = __( 'Category name is required.', 'event-management-platform' );
        }

        $slug = empty( $data['slug'] ) ? sanitize_title( $data['name'] ?? '' ) : sanitize_title( $data['slug'] );
        if ( empty( $slug ) ) {
            $errors['slug'] = __( 'Category slug is required.', 'event-management-platform' );
        } elseif ( $this->repository->findBySlug( $slug ) ) {
            $errors['slug'] = __( 'Category slug must be unique.', 'event-management-platform' );
        }

        if ( ! empty( $errors ) ) {
            throw new Exception( json_encode( $errors ) );
        }

        $sanitized = [
            'name'      => sanitize_text_field( $data['name'] ),
            'slug'      => $slug,
            'parent_id' => ! empty( $data['parent_id'] ) ? (int) $data['parent_id'] : null,
        ];

        return $this->repository->create( $sanitized );
    }

    public function updateCategory( int $id, array $data ): bool {
        $errors = [];
        if ( empty( $data['name'] ) ) {
            $errors['name'] = __( 'Category name is required.', 'event-management-platform' );
        }

        $slug = empty( $data['slug'] ) ? sanitize_title( $data['name'] ?? '' ) : sanitize_title( $data['slug'] );
        if ( empty( $slug ) ) {
            $errors['slug'] = __( 'Category slug is required.', 'event-management-platform' );
        } else {
            $existing = $this->repository->findBySlug( $slug );
            if ( $existing && $existing->id !== $id ) {
                $errors['slug'] = __( 'Category slug must be unique.', 'event-management-platform' );
            }
        }

        if ( ! empty( $errors ) ) {
            throw new Exception( json_encode( $errors ) );
        }

        $sanitized = [
            'name'      => sanitize_text_field( $data['name'] ),
            'slug'      => $slug,
            'parent_id' => ! empty( $data['parent_id'] ) ? (int) $data['parent_id'] : null,
        ];

        return $this->repository->update( $id, $sanitized );
    }

    public function deleteCategory( int $id ): bool {
        return $this->repository->delete( $id );
    }

    public function getCategory( int $id ) {
        return $this->repository->find( $id );
    }

    public function getCategoryRepository(): CategoryRepository {
        return $this->repository;
    }
}
