<?php

namespace EventManagementPlatform\API;

class Router {
    /**
     * Initialize REST API Router
     */
    public function init(): void {
        add_action( 'rest_api_init', [ $this, 'registerRoutes' ] );
    }

    /**
     * Register all platform routes
     */
    public function registerRoutes(): void {
        $controllers = [
            new EventController(),
            new VenueController(),
            new CategoryController(),
            new OrganizerController(),
        ];

        foreach ( $controllers as $controller ) {
            $controller->register_routes();
        }
    }
}
