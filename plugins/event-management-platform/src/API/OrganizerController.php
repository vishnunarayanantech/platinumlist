<?php

namespace EventManagementPlatform\API;

use EventManagementPlatform\Services\OrganizerService;
use WP_REST_Request;
use Exception;

class OrganizerController extends BaseController {
    public $rest_base = 'organizers';
    private OrganizerService $service;

    public function __construct() {
        $this->service = new OrganizerService();
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
            'search' => sanitize_text_field( $request->get_param( 'search' ) ),
        ];

        $results = $this->service->getOrganizerRepository()->paginate( $page, $per_page, $filters );

        $results['items'] = array_map( fn( $org ) => $org->toArray(), $results['items'] );

        return $this->respondSuccess( $results );
    }

    public function getItem( WP_REST_Request $request ) {
        $id = (int) $request->get_param( 'id' );
        $organizer = $this->service->getOrganizer( $id );

        if ( ! $organizer ) {
            return $this->respondError( __( 'Organizer not found.', 'event-management-platform' ), 'not_found', 404 );
        }

        return $this->respondSuccess( $organizer->toArray() );
    }

    public function createItem( WP_REST_Request $request ) {
        $params = $request->get_params();

        try {
            $org_id = $this->service->createOrganizer( $params );
            if ( ! $org_id ) {
                return $this->respondError( __( 'Failed to create organizer.', 'event-management-platform' ), 'creation_failed', 500 );
            }
            return $this->respondSuccess( [ 'id' => $org_id ], 201 );
        } catch ( Exception $e ) {
            return $this->respondError( $e->getMessage(), 'validation_failed', 400 );
        }
    }

    public function updateItem( WP_REST_Request $request ) {
        $id = (int) $request->get_param( 'id' );
        $organizer = $this->service->getOrganizer( $id );

        if ( ! $organizer ) {
            return $this->respondError( __( 'Organizer not found.', 'event-management-platform' ), 'not_found', 404 );
        }

        $params = $request->get_params();

        try {
            $updated = $this->service->updateOrganizer( $id, $params );
            if ( ! $updated ) {
                return $this->respondError( __( 'Failed to update organizer.', 'event-management-platform' ), 'update_failed', 500 );
            }
            return $this->respondSuccess( [ 'message' => __( 'Organizer updated successfully.', 'event-management-platform' ) ] );
        } catch ( Exception $e ) {
            return $this->respondError( $e->getMessage(), 'validation_failed', 400 );
        }
    }

    public function deleteItem( WP_REST_Request $request ) {
        $id = (int) $request->get_param( 'id' );
        $organizer = $this->service->getOrganizer( $id );

        if ( ! $organizer ) {
            return $this->respondError( __( 'Organizer not found.', 'event-management-platform' ), 'not_found', 404 );
        }

        $deleted = $this->service->deleteOrganizer( $id );
        if ( ! $deleted ) {
            return $this->respondError( __( 'Failed to delete organizer.', 'event-management-platform' ), 'delete_failed', 500 );
        }

        return $this->respondSuccess( [ 'message' => __( 'Organizer deleted successfully.', 'event-management-platform' ) ] );
    }
}
