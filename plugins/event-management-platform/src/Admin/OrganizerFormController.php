<?php

namespace EventManagementPlatform\Admin;

use EventManagementPlatform\Services\OrganizerService;
use Exception;

class OrganizerFormController {
    private OrganizerService $service;

    public function __construct() {
        $this->service = new OrganizerService();
    }

    public function render(): void {
        $action = sanitize_text_field( $_GET['action'] ?? 'list' );
        $id     = isset( $_GET['id'] ) ? (int) $_GET['id'] : null;
        $errors = [];
        $notice = '';

        if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['emp_save_organizer'] ) ) {
            check_admin_referer( 'emp_save_organizer', 'emp_save_organizer_nonce' );

            $org_data = [
                'name'    => sanitize_text_field( $_POST['name'] ?? '' ),
                'email'   => sanitize_text_field( $_POST['email'] ?? '' ),
                'phone'   => sanitize_text_field( $_POST['phone'] ?? '' ),
                'website' => sanitize_text_field( $_POST['website'] ?? '' ),
            ];

            try {
                if ( $id ) {
                    $this->service->updateOrganizer( $id, $org_data );
                    $notice = __( 'Organizer updated successfully.', 'event-management-platform' );
                } else {
                    $id = $this->service->createOrganizer( $org_data );
                    $notice = __( 'Organizer created successfully.', 'event-management-platform' );
                    $action = 'edit';
                }
            } catch ( Exception $e ) {
                $errors = json_decode( $e->getMessage(), true ) ?: [ 'general' => $e->getMessage() ];
            }
        }

        if ( $action === 'delete' && $id ) {
            check_admin_referer( 'emp_delete_organizer_' . $id );
            if ( $this->service->deleteOrganizer( $id ) ) {
                wp_redirect( admin_url( 'admin.php?page=emp-organizers&deleted=true' ) );
                exit;
            }
        }

        if ( isset( $_GET['deleted'] ) && $_GET['deleted'] === 'true' ) {
            $notice = __( 'Organizer deleted successfully.', 'event-management-platform' );
        }

        if ( $action === 'add' || $action === 'edit' ) {
            $organizer = null;
            if ( $action === 'edit' && $id ) {
                $organizer = $this->service->getOrganizer( $id );
            }
            include EMP_PATH . 'templates/admin/organizer-form.php';
        } else {
            $page     = isset( $_GET['paged'] ) ? (int) $_GET['paged'] : 1;
            $per_page = 10;
            $search   = sanitize_text_field( $_GET['s'] ?? '' );
            
            $filters = [];
            if ( ! empty( $search ) ) {
                $filters['search'] = $search;
            }

            $results    = $this->service->getOrganizerRepository()->paginate( $page, $per_page, $filters );
            $organizers = $results['items'];
            $total      = $results['total'];
            $pages      = $results['pages'];

            include EMP_PATH . 'templates/admin/organizer-list.php';
        }
    }
}
