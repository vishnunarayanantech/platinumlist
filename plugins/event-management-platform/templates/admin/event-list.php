<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="emp-admin-wrap">
    <div class="emp-admin-header">
        <h1><?php esc_html_e( 'Manage Events', 'event-management-platform' ); ?></h1>
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=emp-events&action=add' ) ); ?>" class="emp-btn">
            <?php esc_html_e( 'Add New Event', 'event-management-platform' ); ?>
        </a>
    </div>

    <?php if ( ! empty( $notice ) ) : ?>
        <div class="emp-notice"><?php echo esc_html( $notice ); ?></div>
    <?php endif; ?>

    <!-- Search Form -->
    <div class="emp-card" style="padding: 16px; margin-bottom: 24px;">
        <form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>" style="display: flex; gap: 12px; align-items: center;">
            <input type="hidden" name="page" value="emp-events" />
            <input type="text" name="s" value="<?php echo esc_attr( $search ); ?>" class="emp-form-control" placeholder="<?php esc_attr_e( 'Search events...', 'event-management-platform' ); ?>" style="max-width: 300px; margin: 0;" />
            <button type="submit" class="emp-btn" style="padding: 8px 16px;"><?php esc_html_e( 'Search', 'event-management-platform' ); ?></button>
            <?php if ( ! empty( $search ) ) : ?>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=emp-events' ) ); ?>" class="emp-btn emp-btn-secondary" style="padding: 8px 16px;"><?php esc_html_e( 'Clear', 'event-management-platform' ); ?></a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Table -->
    <div class="emp-card">
        <?php if ( empty( $events ) ) : ?>
            <p style="color: var(--emp-text-muted); text-align: center; padding: 20px 0;"><?php esc_html_e( 'No events found.', 'event-management-platform' ); ?></p>
        <?php else : ?>
            <div class="emp-table-wrap">
                <table class="emp-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Title', 'event-management-platform' ); ?></th>
                            <th><?php esc_html_e( 'Slug', 'event-management-platform' ); ?></th>
                            <th><?php esc_html_e( 'Status', 'event-management-platform' ); ?></th>
                            <th><?php esc_html_e( 'Schedule', 'event-management-platform' ); ?></th>
                            <th><?php esc_html_e( 'Timezone', 'event-management-platform' ); ?></th>
                            <th><?php esc_html_e( 'Actions', 'event-management-platform' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $events as $ev ) : ?>
                            <tr>
                                <td>
                                    <strong style="font-size: 15px; color: #fff;"><?php echo esc_html( $ev->title ); ?></strong>
                                </td>
                                <td><code><?php echo esc_html( $ev->slug ); ?></code></td>
                                <td>
                                    <span class="emp-badge emp-badge-<?php echo esc_attr( $ev->status ); ?>">
                                        <?php echo esc_html( ucfirst( $ev->status ) ); ?>
                                    </span>
                                </td>
                                <td>
                                    <span style="font-size: 13px; color: var(--emp-text-muted);">
                                        <?php echo esc_html( $ev->start_datetime ?: '-' ); ?>
                                    </span>
                                </td>
                                <td><?php echo esc_html( $ev->timezone ); ?></td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=emp-events&action=edit&id=' . $ev->id ) ); ?>" class="emp-btn emp-btn-secondary" style="padding: 6px 12px; font-size: 12px;">
                                            <?php esc_html_e( 'Edit', 'event-management-platform' ); ?>
                                        </a>
                                        <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=emp-events&action=delete&id=' . $ev->id ), 'emp_delete_event_' . $ev->id ) ); ?>" class="emp-btn emp-btn-danger" style="padding: 6px 12px; font-size: 12px;" onclick="return confirm('<?php esc_attr_e( 'Are you sure you want to delete this event?', 'event-management-platform' ); ?>');">
                                            <?php esc_html_e( 'Delete', 'event-management-platform' ); ?>
                                        </a>
                                        <?php if ( $ev->status === 'published' ) : ?>
                                            <a href="<?php echo esc_url( home_url( '/events/' . $ev->slug ) ); ?>" target="_blank" class="emp-btn emp-btn-secondary" style="padding: 6px 12px; font-size: 12px;">
                                                <?php esc_html_e( 'View', 'event-management-platform' ); ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ( $pages > 1 ) : ?>
                <div style="display: flex; justify-content: center; gap: 8px; margin-top: 24px;">
                    <?php for ( $i = 1; $i <= $pages; $i++ ) : ?>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=emp-events&paged=' . $i . '&s=' . urlencode( $search ) ) ); ?>" class="emp-btn <?php echo $i === $page ? '' : 'emp-btn-secondary'; ?>" style="padding: 6px 12px; font-size: 12px;">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
