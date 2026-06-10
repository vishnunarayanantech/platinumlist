<?php

namespace EventManagementPlatform\Admin;

class SettingsController {
    /**
     * Render and handle settings updates
     */
    public function render(): void {
        $notice = '';

        if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['emp_save_settings'] ) ) {
            check_admin_referer( 'emp_save_settings', 'emp_save_settings_nonce' );

            $settings = [
                'google_maps_api_key' => sanitize_text_field( $_POST['google_maps_api_key'] ?? '' ),
                'default_timezone'    => sanitize_text_field( $_POST['default_timezone'] ?? 'UTC' ),
                'enable_frontend'     => isset( $_POST['enable_frontend'] ) ? 1 : 0,
                'events_per_page'     => max( 1, (int) ($_POST['events_per_page'] ?? 10) ),
            ];

            update_option( 'emp_settings', $settings );
            $notice = __( 'Settings saved successfully.', 'event-management-platform' );
        }

        // Get current settings
        $settings = get_option( 'emp_settings', [
            'google_maps_api_key' => '',
            'default_timezone'    => 'UTC',
            'enable_frontend'     => 1,
            'events_per_page'     => 10,
        ] );

        include EMP_PATH . 'templates/admin/settings.php';
    }
}
