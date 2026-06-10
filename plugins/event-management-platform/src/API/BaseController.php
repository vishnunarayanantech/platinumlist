<?php

namespace EventManagementPlatform\API;

use WP_REST_Controller;
use WP_REST_Response;
use WP_Error;

abstract class BaseController extends WP_REST_Controller {
    public $namespace = 'event-platform/v1';

    /**
     * Format a success response
     */
    protected function respondSuccess( $data, int $status_code = 200 ): WP_REST_Response {
        return new WP_REST_Response( [
            'success' => true,
            'data'    => $data
        ], $status_code );
    }

    /**
     * Format an error response
     */
    protected function respondError( string $message, string $code = 'error', int $status_code = 400 ): WP_Error {
        return new WP_Error( $code, $message, [ 'status' => $status_code ] );
    }

    /**
     * Default permission check for read operations (public)
     */
    public function checkReadPermission( $request ): bool {
        return true;
    }

    /**
     * Default permission check for write operations (restricted to admins)
     */
    public function checkWritePermission( $request ): bool {
        return current_user_can( 'manage_options' );
    }
}
