<?php

namespace EventManagementPlatform\Validators;

class OrganizerValidator implements ValidatorInterface {
    private array $errors = [];

    public function validate( array $data ): bool {
        $this->errors = [];

        if ( empty( $data['name'] ) ) {
            $this->errors['name'] = __( 'Organizer name is required.', 'event-management-platform' );
        }

        if ( ! empty( $data['email'] ) ) {
            if ( ! filter_var( $data['email'], FILTER_VALIDATE_EMAIL ) ) {
                $this->errors['email'] = __( 'Please enter a valid email address.', 'event-management-platform' );
            }
        }

        if ( ! empty( $data['website'] ) ) {
            if ( ! filter_var( $data['website'], FILTER_VALIDATE_URL ) ) {
                $this->errors['website'] = __( 'Please enter a valid URL.', 'event-management-platform' );
            }
        }

        return empty( $this->errors );
    }

    public function getErrors(): array {
        return $this->errors;
    }
}
