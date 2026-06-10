<?php

namespace EventManagementPlatform\Entities;

class Event {
    public ?int $id = null;
    public string $title = '';
    public string $slug = '';
    public ?string $short_description = null;
    public ?string $full_description = null;
    public ?int $venue_id = null;
    public ?int $category_id = null;
    public string $status = 'draft';
    public ?int $featured_image_id = null;
    public ?string $start_datetime = null;
    public ?string $end_datetime = null;
    public string $timezone = 'UTC';
    public ?string $seo_title = null;
    public ?string $seo_description = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    // Associated Entities (loaded dynamically or via service/repository)
    public array $gallery = []; // Array of attachment IDs or structures
    public array $faqs = [];    // Array of Faq entities
    public array $organizers = []; // Array of Organizer entities
    public array $tags = [];       // Array of tag names or objects

    public function __construct( array $data = [] ) {
        if ( ! empty( $data ) ) {
            $this->fromArray( $data );
        }
    }

    public function fromArray( array $data ): void {
        $this->id = isset( $data['id'] ) ? (int) $data['id'] : null;
        $this->title = $data['title'] ?? '';
        $this->slug = $data['slug'] ?? '';
        $this->short_description = $data['short_description'] ?? null;
        $this->full_description = $data['full_description'] ?? null;
        $this->venue_id = isset( $data['venue_id'] ) && $data['venue_id'] !== '' ? (int) $data['venue_id'] : null;
        $this->category_id = isset( $data['category_id'] ) && $data['category_id'] !== '' ? (int) $data['category_id'] : null;
        $this->status = $data['status'] ?? 'draft';
        $this->featured_image_id = isset( $data['featured_image_id'] ) && $data['featured_image_id'] !== '' ? (int) $data['featured_image_id'] : null;
        $this->start_datetime = $data['start_datetime'] ?? null;
        $this->end_datetime = $data['end_datetime'] ?? null;
        $this->timezone = $data['timezone'] ?? 'UTC';
        $this->seo_title = $data['seo_title'] ?? null;
        $this->seo_description = $data['seo_description'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;

        if ( isset( $data['gallery'] ) && is_array( $data['gallery'] ) ) {
            $this->gallery = $data['gallery'];
        }
        if ( isset( $data['faqs'] ) && is_array( $data['faqs'] ) ) {
            $this->faqs = array_map( function( $faq ) {
                return $faq instanceof Faq ? $faq : new Faq( (array) $faq );
            }, $data['faqs'] );
        }
        if ( isset( $data['organizers'] ) && is_array( $data['organizers'] ) ) {
            $this->organizers = array_map( function( $org ) {
                return $org instanceof Organizer ? $org : new Organizer( (array) $org );
            }, $data['organizers'] );
        }
        if ( isset( $data['tags'] ) && is_array( $data['tags'] ) ) {
            $this->tags = $data['tags'];
        }
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'short_description' => $this->short_description,
            'full_description' => $this->full_description,
            'venue_id' => $this->venue_id,
            'category_id' => $this->category_id,
            'status' => $this->status,
            'featured_image_id' => $this->featured_image_id,
            'start_datetime' => $this->start_datetime,
            'end_datetime' => $this->end_datetime,
            'timezone' => $this->timezone,
            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
