<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="emp-admin-wrap">
    <div class="emp-admin-header">
        <h1><?php echo $organizer ? esc_html__( 'Edit Organizer', 'event-management-platform' ) : esc_html__( 'Add New Organizer', 'event-management-platform' ); ?></h1>
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=emp-organizers' ) ); ?>" class="emp-btn emp-btn-secondary">
            <?php esc_html_e( 'Back to List', 'event-management-platform' ); ?>
        </a>
    </div>

    <?php if ( ! empty( $notice ) ) : ?>
        <div class="emp-notice"><?php echo esc_html( $notice ); ?></div>
    <?php endif; ?>

    <?php if ( ! empty( $errors ) ) : ?>
        <div class="emp-notice emp-notice-error">
            <ul style="margin: 0; padding-left: 20px;">
                <?php foreach ( $errors as $field => $err ) : ?>
                    <li><strong><?php echo esc_html( ucfirst( $field ) ); ?>:</strong> <?php echo esc_html( $err ); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="emp-card" style="max-width: 600px;">
        <form method="post" action="">
            <?php wp_nonce_field( 'emp_save_organizer', 'emp_save_organizer_nonce' ); ?>

            <div class="emp-form-group">
                <label for="emp-org-name"><?php esc_html_e( 'Organizer Name', 'event-management-platform' ); ?> *</label>
                <input type="text" id="emp-org-name" name="name" value="<?php echo esc_attr( $organizer ? $organizer->name : '' ); ?>" class="emp-form-control" required />
            </div>

            <div class="emp-form-group">
                <label for="emp-org-email"><?php esc_html_e( 'Email Address', 'event-management-platform' ); ?></label>
                <input type="email" id="emp-org-email" name="email" value="<?php echo esc_attr( $organizer ? $organizer->email : '' ); ?>" class="emp-form-control" />
            </div>

            <div class="emp-form-group">
                <label for="emp-org-phone"><?php esc_html_e( 'Phone Number', 'event-management-platform' ); ?></label>
                <input type="text" id="emp-org-phone" name="phone" value="<?php echo esc_attr( $organizer ? $organizer->phone : '' ); ?>" class="emp-form-control" />
            </div>

            <div class="emp-form-group">
                <label for="emp-org-website"><?php esc_html_e( 'Website URL', 'event-management-platform' ); ?></label>
                <input type="url" id="emp-org-website" name="website" value="<?php echo esc_attr( $organizer ? $organizer->website : '' ); ?>" class="emp-form-control" placeholder="https://..." />
            </div>

            <button type="submit" name="emp_save_organizer" class="emp-btn">
                <?php esc_html_e( 'Save Organizer', 'event-management-platform' ); ?>
            </button>
        </form>
    </div>
</div>
