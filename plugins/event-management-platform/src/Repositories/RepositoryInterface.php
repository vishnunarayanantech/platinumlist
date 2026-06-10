<?php

namespace EventManagementPlatform\Repositories;

interface RepositoryInterface {
    public function create( array $data );
    public function update( int $id, array $data ): bool;
    public function delete( int $id ): bool;
    public function find( int $id );
    public function findBySlug( string $slug );
    public function paginate( int $page, int $per_page, array $filters = [] ): array;
}
