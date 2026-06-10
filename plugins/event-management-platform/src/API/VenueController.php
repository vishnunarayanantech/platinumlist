<?php

namespace EventManagementPlatform\API;

use EventManagementPlatform\Services\VenueService;
use WP_REST_Request;
use Exception;

class VenueController extends BaseController {
    public $rest_base = 'venues';
    private VenueService $service;

    public function __construct() {
        $this->service = new VenueService();
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
            'search'  => sanitize_text_field( $request->get_param( 'search' ) ),
            'city'    => sanitize_text_field( $request->get_param( 'city' ) ),
            'country' => sanitize_text_field( $request->get_param( 'country' ) ),
        ];

        $results = $this->service->getVenueRepository()->paginate( $page, $per_page, $filters );

        $results['items'] = array_map( fn( $venue ) => $venue->toArray(), $results['items'] );

        return $this->respondSuccess( $results );
    }

    public function getItem( WP_REST_Request $request ) {
        $id = (int) $request->get_param( 'id' );
        $venue = $this->service->getVenue( $id );

        if ( ! $venue ) {
            return $this->respondError( __( 'Venue not found.', 'event-management-platform' ), 'not_found', 404 );
        }

        return $this->respondSuccess( $venue->toArray() );
    }

    public function createItem( WP_REST_Request $request ) {
        $params = $request->get_params();

        try {
            $venue_id = $this->service->createVenue( $params );
            if ( ! $venue_id ) {
                return $this->respondError( __( 'Failed to create venue.', 'event-management-platform' ), 'creation_failed', 500 );
            }
            return $this->respondSuccess( [ 'id' => $venue_id ], 201 );
        } catch ( Exception $e ) {
            return $this->respondError( $e->getMessage(), 'validation_failed', 400 );
        }
    }

    public function updateItem( WP_REST_Request $request ) {
        $id = (int) $request->get_param( 'id' );
        $venue = $this->service->getVenue( $id );

        if ( ! $venue ) {
            return $this->respondError( __( 'Venue not found.', 'event-management-platform' ), 'not_found', 404 );
        }

        $params = $request->get_params();

        try {
            $updated = $this->service->updateVenue( $id, $params );
            if ( ! $updated ) {
                return $this->respondError( __( 'Failed to update venue.', 'event-management-platform' ), 'update_failed', 500 );
            }
            return $this->respondSuccess( [ 'message' => __( 'Venue updated successfully.', 'event-management-platform' ) ] );
        } catch ( Exception $e ) {
            return $this->respondError( $e->getMessage(), 'validation_failed', 400 );
        }
    }

    public function deleteItem( WP_REST_Request $request ) {
        $id = (int) $request->get_param( 'id' );
        $venue = $this->service->getVenue( $id );

        if ( ! $venue ) {
            return $this->respondError( __( 'Venue not found.', 'event-management-platform' ), 'not_found', 404 );
        }

        $deleted = $this->service->deleteVenue( $id );
        if ( ! $deleted ) {
            return $this->respondError( __( 'Failed to delete venue.', 'event-management-platform' ), 'delete_failed', 500 );
        }

        return $this->respondSuccess( [ 'message' => __( 'Venue deleted successfully.', 'event-management-platform' ) ] );
    }
}
