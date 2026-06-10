<?php

namespace EventManagementPlatform\Entities;

class Venue {
    public ?int $id = null;
    public string $name = '';
    public ?string $address = null;
    public ?string $city = null;
    public ?string $country = null;
    public ?float $latitude = null;
    public ?float $longitude = null;
    public ?string $google_map_url = null;
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
        $this->address = $data['address'] ?? null;
        $this->city = $data['city'] ?? null;
        $this->country = $data['country'] ?? null;
        $this->latitude = isset( $data['latitude'] ) ? (float) $data['latitude'] : null;
        $this->longitude = isset( $data['longitude'] ) ? (float) $data['longitude'] : null;
        $this->google_map_url = $data['google_map_url'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'city' => $this->city,
            'country' => $this->country,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'google_map_url' => $this->google_map_url,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
