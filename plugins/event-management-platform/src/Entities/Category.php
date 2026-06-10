<?php

namespace EventManagementPlatform\Entities;

class Category {
    public ?int $id = null;
    public string $name = '';
    public string $slug = '';
    public ?int $parent_id = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    public function __construct( array $data = [] ) {
        if ( ! empty( $data ) ) {
            $this->fromArray( $data );
        }
    }

    public function fromArray( array $data ): void {
        $this->id = isset( $data['id'] ) ? (int) $data['id'] : null;
        $this->name = $data['name'] ?? '';
        $this->slug = $data['slug'] ?? '';
        $this->parent_id = isset( $data['parent_id'] ) && $data['parent_id'] !== '' ? (int) $data['parent_id'] : null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'parent_id' => $this->parent_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
