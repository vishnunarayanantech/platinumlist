<?php

namespace EventManagementPlatform\Admin;

use EventManagementPlatform\Services\EventService;
use EventManagementPlatform\Repositories\VenueRepository;
use EventManagementPlatform\Repositories\CategoryRepository;
use EventManagementPlatform\Repositories\OrganizerRepository;
use Exception;

class EventFormController {
    private EventService $service;

    public function __construct() {
        $this->service = new EventService();
    }

    /**
     * Render and handle Events CRUD in Admin
     */
    public function render(): void {
        $action = sanitize_text_field( $_GET['action'] ?? 'list' );
        $id     = isset( $_GET['id'] ) ? (int) $_GET['id'] : null;
        $errors = [];
        $notice = '';

        // Handle Form Submissions
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['emp_save_event'] ) ) {
            check_admin_referer( 'emp_save_event', 'emp_save_event_nonce' );

            $event_data = $this->extractPostData();

            try {
                if ( $id ) {
                    $this->service->updateEvent( $id, $event_data );
                    $notice = __( 'Event updated successfully.', 'event-management-platform' );
                } else {
                    $id = $this->service->createEvent( $event_data );
                    $notice = __( 'Event created successfully.', 'event-management-platform' );
                    $action = 'edit'; // Go to edit mode
                }
            } catch ( Exception $e ) {
                $errors = json_decode( $e->getMessage(), true ) ?: [ 'general' => $e->getMessage() ];
            }
        }

        // Handle Delete Action
        if ( $action === 'delete' && $id ) {
            check_admin_referer( 'emp_delete_event_' . $id );
            if ( $this->service->deleteEvent( $id ) ) {
                wp_redirect( admin_url( 'admin.php?page=emp-events&deleted=true' ) );
                exit;
            }
        }

        if ( isset( $_GET['deleted'] ) && $_GET['deleted'] === 'true' ) {
            $notice = __( 'Event deleted successfully.', 'event-management-platform' );
        }

        // Fetch helper repositories data for selector controls
        $venueRepo = new VenueRepository();
        $catRepo   = new CategoryRepository();
        $orgRepo   = new OrganizerRepository();

        $venues     = $venueRepo->all();
        $categories = $catRepo->all();
        $organizers = $orgRepo->all();

        // View Router
        if ( $action === 'add' || $action === 'edit' ) {
            $event = null;
            if ( $action === 'edit' && $id ) {
                $event = $this->service->getEvent( $id );
            }
            include EMP_PATH . 'templates/admin/event-form.php';
        } else {
            // Paginated List
            $page     = isset( $_GET['paged'] ) ? (int) $_GET['paged'] : 1;
            $per_page = 10;
            $search   = sanitize_text_field( $_GET['s'] ?? '' );
            
            $filters = [];
            if ( ! empty( $search ) ) {
                $filters['search'] = $search;
            }
            
            // Allow viewing drafts/published in admin
            $filters['status'] = ''; 

            $results = $this->service->getEventRepository()->paginate( $page, $per_page, $filters );
            $events  = $results['items'];
            $total   = $results['total'];
            $pages   = $results['pages'];

            include EMP_PATH . 'templates/admin/event-list.php';
        }
    }

    /**
     * Extract event data from $_POST safely
     */
    private function extractPostData(): array {
        $gallery = isset( $_POST['gallery_attachment_ids'] ) ? explode( ',', sanitize_text_field( $_POST['gallery_attachment_ids'] ) ) : [];
        $gallery = array_filter( array_map( 'intval', $gallery ) );

        $faqs = [];
        if ( isset( $_POST['faqs'] ) && is_array( $_POST['faqs'] ) ) {
            foreach ( $_POST['faqs'] as $faq ) {
                if ( ! empty( $faq['question'] ) && ! empty( $faq['answer'] ) ) {
                    $faqs[] = [
                        'question' => sanitize_text_field( $faq['question'] ),
                        'answer'   => wp_kses_post( $faq['answer'] ),
                    ];
                }
            }
        }

        $organizer_ids = isset( $_POST['organizer_ids'] ) && is_array( $_POST['organizer_ids'] ) ? array_map( 'intval', $_POST['organizer_ids'] ) : [];
        $tags = isset( $_POST['tags'] ) ? sanitize_text_field( $_POST['tags'] ) : '';

        return [
            'title'             => sanitize_text_field( $_POST['title'] ?? '' ),
            'slug'              => sanitize_title( $_POST['slug'] ?? '' ),
            'short_description' => sanitize_textarea_field( $_POST['short_description'] ?? '' ),
            'full_description'  => wp_kses_post( $_POST['full_description'] ?? '' ),
            'venue_id'          => ! empty( $_POST['venue_id'] ) ? (int) $_POST['venue_id'] : null,
            'category_id'       => ! empty( $_POST['category_id'] ) ? (int) $_POST['category_id'] : null,
            'status'            => sanitize_text_field( $_POST['status'] ?? 'draft' ),
            'featured_image_id' => ! empty( $_POST['featured_image_id'] ) ? (int) $_POST['featured_image_id'] : null,
            'start_datetime'    => ! empty( $_POST['start_datetime'] ) ? sanitize_text_field( $_POST['start_datetime'] ) : null,
            'end_datetime'      => ! empty( $_POST['end_datetime'] ) ? sanitize_text_field( $_POST['end_datetime'] ) : null,
            'timezone'          => sanitize_text_field( $_POST['timezone'] ?? 'UTC' ),
            'seo_title'         => sanitize_text_field( $_POST['seo_title'] ?? '' ),
            'seo_description'   => sanitize_textarea_field( $_POST['seo_description'] ?? '' ),
            'gallery'           => $gallery,
            'faqs'              => $faqs,
            'organizer_ids'     => $organizer_ids,
            'tags'              => $tags,
        ];
    }
}
