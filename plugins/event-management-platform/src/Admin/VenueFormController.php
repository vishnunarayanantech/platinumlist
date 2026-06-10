<?php

namespace EventManagementPlatform\Admin;

use EventManagementPlatform\Services\VenueService;
use Exception;

class VenueFormController {
    private VenueService $service;

    public function __construct() {
        $this->service = new VenueService();
    }

    public function render(): void {
        $action = sanitize_text_field( $_GET['action'] ?? 'list' );
        $id     = isset( $_GET['id'] ) ? (int) $_GET['id'] : null;
        $errors = [];
        $notice = '';

        if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['emp_save_venue'] ) ) {
            check_admin_referer( 'emp_save_venue', 'emp_save_venue_nonce' );

            $venue_data = [
                'name'           => sanitize_text_field( $_POST['name'] ?? '' ),
                'address'        => sanitize_textarea_field( $_POST['address'] ?? '' ),
                'city'           => sanitize_text_field( $_POST['city'] ?? '' ),
                'country'        => sanitize_text_field( $_POST['country'] ?? '' ),
                'latitude'       => $_POST['latitude'] !== '' ? (float) $_POST['latitude'] : '',
                'longitude'      => $_POST['longitude'] !== '' ? (float) $_POST['longitude'] : '',
                'google_map_url' => esc_url_raw( $_POST['google_map_url'] ?? '' ),
            ];

            try {
                if ( $id ) {
                    $this->service->updateVenue( $id, $venue_data );
                    $notice = __( 'Venue updated successfully.', 'event-management-platform' );
                } else {
                    $id = $this->service->createVenue( $venue_data );
                    $notice = __( 'Venue created successfully.', 'event-management-platform' );
                    $action = 'edit';
                }
            } catch ( Exception $e ) {
                $errors = json_decode( $e->getMessage(), true ) ?: [ 'general' => $e->getMessage() ];
            }
        }

        if ( $action === 'delete' && $id ) {
            check_admin_referer( 'emp_delete_venue_' . $id );
            if ( $this->service->deleteVenue( $id ) ) {
                wp_redirect( admin_url( 'admin.php?page=emp-venues&deleted=true' ) );
                exit;
            }
        }

        if ( isset( $_GET['deleted'] ) && $_GET['deleted'] === 'true' ) {
            $notice = __( 'Venue deleted successfully.', 'event-management-platform' );
        }

        if ( $action === 'add' || $action === 'edit' ) {
            $venue = null;
            if ( $action === 'edit' && $id ) {
                $venue = $this->service->getVenue( $id );
            }
            include EMP_PATH . 'templates/admin/venue-form.php';
        } else {
            $page     = isset( $_GET['paged'] ) ? (int) $_GET['paged'] : 1;
            $per_page = 10;
            $search   = sanitize_text_field( $_GET['s'] ?? '' );
            
            $filters = [];
            if ( ! empty( $search ) ) {
                $filters['search'] = $search;
            }

            $results = $this->service->getVenueRepository()->paginate( $page, $per_page, $filters );
            $venues  = $results['items'];
            $total   = $results['total'];
            $pages   = $results['pages'];

            include EMP_PATH . 'templates/admin/venue-list.php';
        }
    }
}
