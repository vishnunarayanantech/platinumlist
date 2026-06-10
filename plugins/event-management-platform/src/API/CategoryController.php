<?php

namespace EventManagementPlatform\API;

use EventManagementPlatform\Services\CategoryService;
use WP_REST_Request;
use Exception;

class CategoryController extends BaseController {
    public $rest_base = 'categories';
    private CategoryService $service;

    public function __construct() {
        $this->service = new CategoryService();
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
        $per_page = (int) $request->get_param( 'per_page' ) ?: 100; // Return larger lists for dropdowns by default
        $filters  = [
            'search'    => sanitize_text_field( $request->get_param( 'search' ) ),
            'parent_id' => $request->get_param( 'parent_id' ),
        ];

        $results = $this->service->getCategoryRepository()->paginate( $page, $per_page, $filters );

        $results['items'] = array_map( fn( $cat ) => $cat->toArray(), $results['items'] );

        return $this->respondSuccess( $results );
    }

    public function getItem( WP_REST_Request $request ) {
        $id = (int) $request->get_param( 'id' );
        $category = $this->service->getCategory( $id );

        if ( ! $category ) {
            return $this->respondError( __( 'Category not found.', 'event-management-platform' ), 'not_found', 404 );
        }

        return $this->respondSuccess( $category->toArray() );
    }

    public function createItem( WP_REST_Request $request ) {
        $params = $request->get_params();

        try {
            $cat_id = $this->service->createCategory( $params );
            if ( ! $cat_id ) {
                return $this->respondError( __( 'Failed to create category.', 'event-management-platform' ), 'creation_failed', 500 );
            }
            return $this->respondSuccess( [ 'id' => $cat_id ], 201 );
        } catch ( Exception $e ) {
            return $this->respondError( $e->getMessage(), 'validation_failed', 400 );
        }
    }

    public function updateItem( WP_REST_Request $request ) {
        $id = (int) $request->get_param( 'id' );
        $category = $this->service->getCategory( $id );

        if ( ! $category ) {
            return $this->respondError( __( 'Category not found.', 'event-management-platform' ), 'not_found', 404 );
        }

        $params = $request->get_params();

        try {
            $updated = $this->service->updateCategory( $id, $params );
            if ( ! $updated ) {
                return $this->respondError( __( 'Failed to update category.', 'event-management-platform' ), 'update_failed', 500 );
            }
            return $this->respondSuccess( [ 'message' => __( 'Category updated successfully.', 'event-management-platform' ) ] );
        } catch ( Exception $e ) {
            return $this->respondError( $e->getMessage(), 'validation_failed', 400 );
        }
    }

    public function deleteItem( WP_REST_Request $request ) {
        $id = (int) $request->get_param( 'id' );
        $category = $this->service->getCategory( $id );

        if ( ! $category ) {
            return $this->respondError( __( 'Category not found.', 'event-management-platform' ), 'not_found', 404 );
        }

        $deleted = $this->service->deleteCategory( $id );
        if ( ! $deleted ) {
            return $this->respondError( __( 'Failed to delete category.', 'event-management-platform' ), 'delete_failed', 500 );
        }

        return $this->respondSuccess( [ 'message' => __( 'Category deleted successfully.', 'event-management-platform' ) ] );
    }
}
