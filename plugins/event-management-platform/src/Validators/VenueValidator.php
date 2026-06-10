<?php

namespace EventManagementPlatform\Validators;

class VenueValidator implements ValidatorInterface {
    private array $errors = [];

    public function validate( array $data ): bool {
        $this->errors = [];

        if ( empty( $data['name'] ) ) {
            $this->errors['name'] = __( 'Venue name is required.', 'event-management-platform' );
        }

        if ( isset( $data['latitude'] ) && $data['latitude'] !== '' ) {
            $lat = (float) $data['latitude'];
            if ( $lat < -90 || $lat > 90 ) {
                $this->errors['latitude'] = __( 'Latitude must be between -90 and 90.', 'event-management-platform' );
            }
        }

        if ( isset( $data['longitude'] ) && $data['longitude'] !== '' ) {
            $lng = (float) $data['longitude'];
            if ( $lng < -180 || $lng > 180 ) {
                $this->errors['longitude'] = __( 'Longitude must be between -180 and 180.', 'event-management-platform' );
            }
        }

        return empty( $this->errors );
    }

    public function getErrors(): array {
        return $this->errors;
    }
}
