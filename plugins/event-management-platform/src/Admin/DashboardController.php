<?php

namespace EventManagementPlatform\Admin;

class DashboardController {
    /**
     * Render the admin dashboard
     */
    public function render(): void {
        global $wpdb;

        // Fetch Stats
        $events_count     = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}emp_events" );
        $venues_count     = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}emp_venues" );
        $categories_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}emp_categories" );
        $organizers_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}emp_organizers" );

        // Upcoming events
        $upcoming_events = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}emp_events 
             WHERE status = 'published' AND start_datetime >= %s 
             ORDER BY start_datetime ASC LIMIT 5",
            current_time( 'mysql' )
        ), ARRAY_A );

        // Load dashboard template
        include EMP_PATH . 'templates/admin/dashboard.php';
    }
}
