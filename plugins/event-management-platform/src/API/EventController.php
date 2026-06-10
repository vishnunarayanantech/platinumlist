<?php

namespace EventManagementPlatform\API;

use EventManagementPlatform\Services\EventService;
use WP_REST_Request;
use Exception;

class EventController extends BaseController {
    public $rest_base = 'events';
    private EventService $service;

    public function __construct() {
        $this->service = new EventService();
    }

    public function register_routes(): void {
        register_rest_route( $this->namespace, '/' . $this->rest_base, [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'getItems' ],
                'permission_callback' => [ $this, 'checkReadPermission' ],
            ],
            [
                'methods'             => 'POST',
                'callback'            => [ $this, 'createItem' ],
                'permission_callback' => [ $this, 'checkWritePermission' ],
            ],
        ] );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>\d+)', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'getItem' ],
                'permission_callback' => [ $this, 'checkReadPermission' ],
            ],
            [
                'methods'             => 'PUT',
                'callback'            => [ $this, 'updateItem' ],
                'permission_callback' => [ $this, 'checkWritePermission' ],
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [ $this, 'deleteItem' ],
                'permission_callback' => [ $this, 'checkWritePermission' ],
            ],
        ] );
    }

    public function getItems( WP_REST_Request $request ) {
        $page     = (int) $request->get_param( 'page' ) ?: 1;
        $per_page = (int) $request->get_param( 'per_page' ) ?: 10;
        $filters  = [
            'search'      => sanitize_text_field( $request->get_param( 'search' ) ),
            'status'      => sanitize_text_field( $request->get_param( 'status' ) ) ?: 'published',
            'venue_id'    => $request->get_param( 'venue_id' ),
            'category_id' => $request->get_param( 'category_id' ),
            'date_from'   => sanitize_text_field( $request->get_param( 'date_from' ) ),
            'date_to'     => sanitize_text_field( $request->get_param( 'date_to' ) ),
        ];

        // Admins can view any status, public can only see 'published'
        if ( ! current_user_can( 'manage_options' ) ) {
            $filters['status'] = 'published';
        }

        $results = $this->service->getEventRepository()->paginate( $page, $per_page, $filters );

        // Convert Event entities to arrays for response
        $results['items'] = array_map( function( $event ) {
            $arr = $event->toArray();
            $arr['gallery'] = $event->gallery;
            $arr['faqs'] = array_map( fn($faq) => $faq->toArray(), $event->faqs );
            $arr['organizers'] = array_map( fn($org) => $org->toArray(), $event->organizers );
            $arr['tags'] = $event->tags;
            return $arr;
        }, $results['items'] );

        return $this->respondSuccess( $results );
    }

    public function getItem( WP_REST_Request $request ) {
        $id = (int) $request->get_param( 'id' );
        $event = $this->service->getEvent( $id );

        if ( ! $event ) {
            return $this->respondError( __( 'Event not found.', 'event-management-platform' ), 'not_found', 404 );
        }

        // Check if draft/cancelled and user doesn't have permissions
        if ( $event->status !== 'published' && ! current_user_can( 'manage_options' ) ) {
            return $this->respondError( __( 'Access denied.', 'event-management-platform' ), 'forbidden', 403 );
        }

        $arr = $event->toArray();
        $arr['gallery'] = $event->gallery;
        $arr['faqs'] = array_map( fn($faq) => $faq->toArray(), $event->faqs );
        $arr['organizers'] = array_map( fn($org) => $org->toArray(), $event->organizers );
        $arr['tags'] = $event->tags;

        return $this->respondSuccess( $arr );
    }

    public function createItem( WP_REST_Request $request ) {
        $params = $request->get_params();

        try {
            $event_id = $this->service->createEvent( $params );
            if ( ! $event_id ) {
                return $this->respondError( __( 'Failed to create event.', 'event-management-platform' ), 'creation_failed', 500 );
            }
            return $this->respondSuccess( [ 'id' => $event_id ], 201 );
        } catch ( Exception $e ) {
            return $this->respondError( $e->getMessage(), 'validation_failed', 400 );
        }
    }

    public function updateItem( WP_REST_Request $request ) {
        $id = (int) $request->get_param( 'id' );
        $event = $this->service->getEvent( $id );

        if ( ! $event ) {
            return $this->respondError( __( 'Event not found.', 'event-management-platform' ), 'not_found', 404 );
        }

        $params = $request->get_params();

        try {
            $updated = $this->service->updateEvent( $id, $params );
            if ( ! $updated ) {
                return $this->respondError( __( 'Failed to update event.', 'event-management-platform' ), 'update_failed', 500 );
            }
            return $this->respondSuccess( [ 'message' => __( 'Event updated successfully.', 'event-management-platform' ) ] );
        } catch ( Exception $e ) {
            return $this->respondError( $e->getMessage(), 'validation_failed', 400 );
        }
    }

    public function deleteItem( WP_REST_Request $request ) {
        $id = (int) $request->get_param( 'id' );
        $event = $this->service->getEvent( $id );

        if ( ! $event ) {
            return $this->respondError( __( 'Event not found.', 'event-management-platform' ), 'not_found', 404 );
        }

        $deleted = $this->service->deleteEvent( $id );
        if ( ! $deleted ) {
            return $this->respondError( __( 'Failed to delete event.', 'event-management-platform' ), 'delete_failed', 500 );
        }

        return $this->respondSuccess( [ 'message' => __( 'Event deleted successfully.', 'event-management-platform' ) ] );
    }
}
