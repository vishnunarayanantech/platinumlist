<?php

namespace EventManagementPlatform\Admin;

class AdminMenu {
    /**
     * Hook administration menus and scripts
     */
    public function init(): void {
        add_action( 'admin_menu', [ $this, 'registerMenus' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueueAssets' ] );
    }

    /**
     * Register Admin Menus
     */
    public function registerMenus(): void {
        // Main Menu
        add_menu_page(
            __( 'Event Platform', 'event-management-platform' ),
            __( 'Event Platform', 'event-management-platform' ),
            'manage_options',
            'emp-dashboard',
            [ $this, 'renderDashboard' ],
            'dashicons-calendar-alt',
            25
        );

        // Submenus
        add_submenu_page(
            'emp-dashboard',
            __( 'Dashboard', 'event-management-platform' ),
            __( 'Dashboard', 'event-management-platform' ),
            'manage_options',
            'emp-dashboard',
            [ $this, 'renderDashboard' ]
        );

        add_submenu_page(
            'emp-dashboard',
            __( 'Events', 'event-management-platform' ),
            __( 'Events', 'event-management-platform' ),
            'manage_options',
            'emp-events',
            [ $this, 'renderEvents' ]
        );

        add_submenu_page(
            'emp-dashboard',
            __( 'Venues', 'event-management-platform' ),
            __( 'Venues', 'event-management-platform' ),
            'manage_options',
            'emp-venues',
            [ $this, 'renderVenues' ]
        );

        add_submenu_page(
            'emp-dashboard',
            __( 'Categories', 'event-management-platform' ),
            __( 'Categories', 'event-management-platform' ),
            'manage_options',
            'emp-categories',
            [ $this, 'renderCategories' ]
        );

        add_submenu_page(
            'emp-dashboard',
            __( 'Organizers', 'event-management-platform' ),
            __( 'Organizers', 'event-management-platform' ),
            'manage_options',
            'emp-organizers',
            [ $this, 'renderOrganizers' ]
        );

        add_submenu_page(
            'emp-dashboard',
            __( 'Settings', 'event-management-platform' ),
            __( 'Settings', 'event-management-platform' ),
            'manage_options',
            'emp-settings',
            [ $this, 'renderSettings' ]
        );
    }

    /**
     * Enqueue Admin Styles and Scripts
     */
    public function enqueueAssets( string $hook ): void {
        // Enqueue media library scripts for our media loaders
        if ( strpos( $hook, 'emp-' ) !== false ) {
            wp_enqueue_media();
            
            // Enqueue WordPress code editor for descriptions if desired
            wp_enqueue_editor();

            // Font Outfit/Inter
            wp_enqueue_style( 'emp-google-fonts', 'https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap', [], EMP_VERSION );

            // Admin CSS
            wp_enqueue_style( 'emp-admin-style', EMP_URL . 'assets/css/admin.css', [], EMP_VERSION );

            // Admin JS
            wp_enqueue_script( 'emp-admin-script', EMP_URL . 'assets/js/admin.js', [ 'jquery' ], EMP_VERSION, true );

            // Nonce and APIs config for Javascript
            wp_localize_script( 'emp-admin-script', 'empAdminConfig', [
                'apiUrl' => esc_url_raw( rest_url( 'event-platform/v1' ) ),
                'nonce'  => wp_create_nonce( 'wp_rest' ),
            ] );
        }
    }

    /**
     * Handlers for Rendering Pages
     */
    public function renderDashboard(): void {
        $controller = new DashboardController();
        $controller->render();
    }

    public function renderEvents(): void {
        $controller = new EventFormController();
        $controller->render();
    }

    public function renderVenues(): void {
        $controller = new VenueFormController();
        $controller->render();
    }

    public function renderCategories(): void {
        $controller = new CategoryFormController();
        $controller->render();
    }

    public function renderOrganizers(): void {
        $controller = new OrganizerFormController();
        $controller->render();
    }

    public function renderSettings(): void {
        $controller = new SettingsController();
        $controller->render();
    }
}
