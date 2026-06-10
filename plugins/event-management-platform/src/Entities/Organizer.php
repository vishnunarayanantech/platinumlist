<?php

namespace EventManagementPlatform\Entities;

class Organizer {
    public ?int $id = null;
    public string $name = '';
    public ?string $email = null;
    public ?string $phone = null;
    public ?string $website = null;

    public function __construct( array $data = [] ) {
        if ( ! empty( $data ) ) {
            $this->fromArray( $data );
        }
    }

    public function fromArray( array $data ): void {
        $this->id = isset( $data['id'] ) ? (int) $data['id'] : null;
        $this->name = $data['name'] ?? '';
        $this->email = $data['email'] ?? null;
        $this->phone = $data['phone'] ?? null;
        $this->website = $data['website'] ?? null;
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'website' => $this->website,
        ];
    }
}
