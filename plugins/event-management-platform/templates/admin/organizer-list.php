<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="emp-admin-wrap">
    <div class="emp-admin-header">
        <h1><?php esc_html_e( 'Organizers', 'event-management-platform' ); ?></h1>
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=emp-organizers&action=add' ) ); ?>" class="emp-btn">
            <?php esc_html_e( 'Add New Organizer', 'event-management-platform' ); ?>
        </a>
    </div>

    <?php if ( ! empty( $notice ) ) : ?>
        <div class="emp-notice"><?php echo esc_html( $notice ); ?></div>
    <?php endif; ?>

    <div class="emp-card" style="padding: 16px; margin-bottom: 24px;">
        <form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>" style="display: flex; gap: 12px; align-items: center;">
            <input type="hidden" name="page" value="emp-organizers" />
            <input type="text" name="s" value="<?php echo esc_attr( $search ); ?>" class="emp-form-control" placeholder="<?php esc_attr_e( 'Search organizers...', 'event-management-platform' ); ?>" style="max-width: 300px; margin: 0;" />
            <button type="submit" class="emp-btn" style="padding: 8px 16px;"><?php esc_html_e( 'Search', 'event-management-platform' ); ?></button>
            <?php if ( ! empty( $search ) ) : ?>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=emp-organizers' ) ); ?>" class="emp-btn emp-btn-secondary" style="padding: 8px 16px;"><?php esc_html_e( 'Clear', 'event-management-platform' ); ?></a>
            <?php endif; ?>
        </form>
    </div>

    <div class="emp-card">
        <?php if ( empty( $organizers ) ) : ?>
            <p style="color: var(--emp-text-muted); text-align: center; padding: 20px 0;"><?php esc_html_e( 'No organizers found.', 'event-management-platform' ); ?></p>
        <?php else : ?>
            <div class="emp-table-wrap">
                <table class="emp-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Name', 'event-management-platform' ); ?></th>
                            <th><?php esc_html_e( 'Email', 'event-management-platform' ); ?></th>
                            <th><?php esc_html_e( 'Phone', 'event-management-platform' ); ?></th>
                            <th><?php esc_html_e( 'Website', 'event-management-platform' ); ?></th>
                            <th><?php esc_html_e( 'Actions', 'event-management-platform' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $organizers as $o ) : ?>
                            <tr>
                                <td><strong style="color:#fff;"><?php echo esc_html( $o->name ); ?></strong></td>
                                <td><?php echo esc_html( $o->email ?: '-' ); ?></td>
                                <td><?php echo esc_html( $o->phone ?: '-' ); ?></td>
                                <td>
                                    <?php if ( $o->website ) : ?>
                                        <a href="<?php echo esc_url( $o->website ); ?>" target="_blank" style="color: var(--emp-primary);"><?php echo esc_html( $o->website ); ?></a>
                                    <?php else : ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=emp-organizers&action=edit&id=' . $o->id ) ); ?>" class="emp-btn emp-btn-secondary" style="padding: 6px 12px; font-size: 12px;">
                                            <?php esc_html_e( 'Edit', 'event-management-platform' ); ?>
                                        </a>
                                        <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=emp-organizers&action=delete&id=' . $o->id ), 'emp_delete_organizer_' . $o->id ) ); ?>" class="emp-btn emp-btn-danger" style="padding: 6px 12px; font-size: 12px;" onclick="return confirm('<?php esc_attr_e( 'Are you sure you want to delete this organizer?', 'event-management-platform' ); ?>');">
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
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=emp-organizers&paged=' . $i . '&s=' . urlencode( $search ) ) ); ?>" class="emp-btn <?php echo $i === $page ? '' : 'emp-btn-secondary'; ?>" style="padding: 6px 12px; font-size: 12px;">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
