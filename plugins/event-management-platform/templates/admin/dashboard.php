<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="emp-admin-wrap">
    <div class="emp-admin-header">
        <h1><?php esc_html_e( 'Event Management Platform', 'event-management-platform' ); ?></h1>
        <span class="emp-badge emp-badge-published"><?php echo esc_html( EMP_VERSION ); ?></span>
    </div>

    <!-- Stats Grid -->
    <div class="emp-stats-grid">
        <div class="emp-stat-card">
            <div class="emp-stat-val"><?php echo esc_html( $events_count ); ?></div>
            <div class="emp-stat-label"><?php esc_html_e( 'Total Events', 'event-management-platform' ); ?></div>
        </div>
        <div class="emp-stat-card">
            <div class="emp-stat-val"><?php echo esc_html( $venues_count ); ?></div>
            <div class="emp-stat-label"><?php esc_html_e( 'Venues', 'event-management-platform' ); ?></div>
        </div>
        <div class="emp-stat-card">
            <div class="emp-stat-val"><?php echo esc_html( $categories_count ); ?></div>
            <div class="emp-stat-label"><?php esc_html_e( 'Categories', 'event-management-platform' ); ?></div>
        </div>
        <div class="emp-stat-card">
            <div class="emp-stat-val"><?php echo esc_html( $organizers_count ); ?></div>
            <div class="emp-stat-label"><?php esc_html_e( 'Organizers', 'event-management-platform' ); ?></div>
        </div>
    </div>

    <div style="display: flex; gap: 30px; flex-wrap: wrap;">
        <!-- Upcoming Events -->
        <div class="emp-card" style="flex: 2; min-width: 300px;">
            <h2><?php esc_html_e( 'Upcoming Published Events', 'event-management-platform' ); ?></h2>
            <?php if ( empty( $upcoming_events ) ) : ?>
                <p style="color: var(--emp-text-muted);"><?php esc_html_e( 'No upcoming published events found.', 'event-management-platform' ); ?></p>
            <?php else : ?>
                <div class="emp-table-wrap">
                    <table class="emp-table">
                        <thead>
                            <tr>
                                <th><?php esc_html_e( 'Title', 'event-management-platform' ); ?></th>
                                <th><?php esc_html_e( 'Start Datetime', 'event-management-platform' ); ?></th>
                                <th><?php esc_html_e( 'Timezone', 'event-management-platform' ); ?></th>
                                <th><?php esc_html_e( 'Actions', 'event-management-platform' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $upcoming_events as $ev ) : ?>
                                <tr>
                                    <td><strong><?php echo esc_html( $ev['title'] ); ?></strong></td>
                                    <td><?php echo esc_html( $ev['start_datetime'] ); ?></td>
                                    <td><?php echo esc_html( $ev['timezone'] ); ?></td>
                                    <td>
                                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=emp-events&action=edit&id=' . $ev['id'] ) ); ?>" class="emp-btn emp-btn-secondary" style="padding: 6px 12px; font-size: 12px;">
                                            <?php esc_html_e( 'Edit', 'event-management-platform' ); ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Quick Links -->
        <div class="emp-card" style="flex: 1; min-width: 250px;">
            <h2><?php esc_html_e( 'Quick Actions', 'event-management-platform' ); ?></h2>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=emp-events&action=add' ) ); ?>" class="emp-btn">
                    <?php esc_html_e( 'Create Event', 'event-management-platform' ); ?>
                </a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=emp-venues&action=add' ) ); ?>" class="emp-btn">
                    <?php esc_html_e( 'Add Venue', 'event-management-platform' ); ?>
                </a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=emp-categories&action=add' ) ); ?>" class="emp-btn">
                    <?php esc_html_e( 'Add Category', 'event-management-platform' ); ?>
                </a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=emp-settings' ) ); ?>" class="emp-btn emp-btn-secondary">
                    <?php esc_html_e( 'Platform Settings', 'event-management-platform' ); ?>
                </a>
            </div>
        </div>
    </div>
</div>
