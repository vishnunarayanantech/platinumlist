<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="emp-admin-wrap">
    <div class="emp-admin-header">
        <h1><?php esc_html_e( 'Venues', 'event-management-platform' ); ?></h1>
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=emp-venues&action=add' ) ); ?>" class="emp-btn">
            <?php esc_html_e( 'Add New Venue', 'event-management-platform' ); ?>
        </a>
    </div>

    <?php if ( ! empty( $notice ) ) : ?>
        <div class="emp-notice"><?php echo esc_html( $notice ); ?></div>
    <?php endif; ?>

    <div class="emp-card" style="padding: 16px; margin-bottom: 24px;">
        <form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>" style="display: flex; gap: 12px; align-items: center;">
            <input type="hidden" name="page" value="emp-venues" />
            <input type="text" name="s" value="<?php echo esc_attr( $search ); ?>" class="emp-form-control" placeholder="<?php esc_attr_e( 'Search venues...', 'event-management-platform' ); ?>" style="max-width: 300px; margin: 0;" />
            <button type="submit" class="emp-btn" style="padding: 8px 16px;"><?php esc_html_e( 'Search', 'event-management-platform' ); ?></button>
            <?php if ( ! empty( $search ) ) : ?>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=emp-venues' ) ); ?>" class="emp-btn emp-btn-secondary" style="padding: 8px 16px;"><?php esc_html_e( 'Clear', 'event-management-platform' ); ?></a>
            <?php endif; ?>
        </form>
    </div>

    <div class="emp-card">
        <?php if ( empty( $venues ) ) : ?>
            <p style="color: var(--emp-text-muted); text-align: center; padding: 20px 0;"><?php esc_html_e( 'No venues found.', 'event-management-platform' ); ?></p>
        <?php else : ?>
            <div class="emp-table-wrap">
                <table class="emp-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Name', 'event-management-platform' ); ?></th>
                            <th><?php esc_html_e( 'City', 'event-management-platform' ); ?></th>
                            <th><?php esc_html_e( 'Country', 'event-management-platform' ); ?></th>
                            <th><?php esc_html_e( 'Latitude', 'event-management-platform' ); ?></th>
                            <th><?php esc_html_e( 'Longitude', 'event-management-platform' ); ?></th>
                            <th><?php esc_html_e( 'Actions', 'event-management-platform' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $venues as $v ) : ?>
                            <tr>
                                <td><strong style="color:#fff;"><?php echo esc_html( $v->name ); ?></strong></td>
                                <td><?php echo esc_html( $v->city ?: '-' ); ?></td>
                                <td><?php echo esc_html( $v->country ?: '-' ); ?></td>
                                <td><code><?php echo esc_html( $v->latitude ?: '-' ); ?></code></td>
                                <td><code><?php echo esc_html( $v->longitude ?: '-' ); ?></code></td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=emp-venues&action=edit&id=' . $v->id ) ); ?>" class="emp-btn emp-btn-secondary" style="padding: 6px 12px; font-size: 12px;">
                                            <?php esc_html_e( 'Edit', 'event-management-platform' ); ?>
                                        </a>
                                        <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=emp-venues&action=delete&id=' . $v->id ), 'emp_delete_venue_' . $v->id ) ); ?>" class="emp-btn emp-btn-danger" style="padding: 6px 12px; font-size: 12px;" onclick="return confirm('<?php esc_attr_e( 'Are you sure you want to delete this venue? This will reset the venue on associated events.', 'event-management-platform' ); ?>');">
                                            <?php esc_html_e( 'Delete', 'event-management-platform' ); ?>
                                        </a>
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
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=emp-venues&paged=' . $i . '&s=' . urlencode( $search ) ) ); ?>" class="emp-btn <?php echo $i === $page ? '' : 'emp-btn-secondary'; ?>" style="padding: 6px 12px; font-size: 12px;">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
