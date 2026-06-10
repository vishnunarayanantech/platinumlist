<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="emp-admin-wrap">
    <div class="emp-admin-header">
        <h1><?php echo $venue ? esc_html__( 'Edit Venue', 'event-management-platform' ) : esc_html__( 'Add New Venue', 'event-management-platform' ); ?></h1>
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=emp-venues' ) ); ?>" class="emp-btn emp-btn-secondary">
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

    <div class="emp-card" style="max-width: 700px;">
        <form method="post" action="">
            <?php wp_nonce_field( 'emp_save_venue', 'emp_save_venue_nonce' ); ?>

            <div class="emp-form-group">
                <label for="emp-venue-name"><?php esc_html_e( 'Venue Name', 'event-management-platform' ); ?> *</label>
                <input type="text" id="emp-venue-name" name="name" value="<?php echo esc_attr( $venue ? $venue->name : '' ); ?>" class="emp-form-control" required />
            </div>

            <div class="emp-form-group">
                <label for="emp-venue-address"><?php esc_html_e( 'Address', 'event-management-platform' ); ?></label>
                <textarea id="emp-venue-address" name="address" class="emp-form-control" rows="3"><?php echo esc_textarea( $venue ? $venue->address : '' ); ?></textarea>
            </div>

            <div class="emp-form-row">
                <div class="emp-form-group">
                    <label for="emp-venue-city"><?php esc_html_e( 'City', 'event-management-platform' ); ?></label>
                    <input type="text" id="emp-venue-city" name="city" value="<?php echo esc_attr( $venue ? $venue->city : '' ); ?>" class="emp-form-control" />
                </div>
                <div class="emp-form-group">
                    <label for="emp-venue-country"><?php esc_html_e( 'Country', 'event-management-platform' ); ?></label>
                    <input type="text" id="emp-venue-country" name="country" value="<?php echo esc_attr( $venue ? $venue->country : '' ); ?>" class="emp-form-control" />
                </div>
            </div>

            <div class="emp-form-row">
                <div class="emp-form-group">
                    <label for="emp-venue-lat"><?php esc_html_e( 'Latitude', 'event-management-platform' ); ?></label>
                    <input type="number" step="any" id="emp-venue-lat" name="latitude" value="<?php echo esc_attr( $venue ? $venue->latitude : '' ); ?>" class="emp-form-control" placeholder="e.g. 25.077" />
                </div>
                <div class="emp-form-group">
                    <label for="emp-venue-lng"><?php esc_html_e( 'Longitude', 'event-management-platform' ); ?></label>
                    <input type="number" step="any" id="emp-venue-lng" name="longitude" value="<?php echo esc_attr( $venue ? $venue->longitude : '' ); ?>" class="emp-form-control" placeholder="e.g. 55.132" />
                </div>
            </div>

            <div class="emp-form-group">
                <label for="emp-venue-map-url"><?php esc_html_e( 'Google Maps URL', 'event-management-platform' ); ?></label>
                <input type="url" id="emp-venue-map-url" name="google_map_url" value="<?php echo esc_attr( $venue ? $venue->google_map_url : '' ); ?>" class="emp-form-control" placeholder="https://maps.google.com/..." />
            </div>

            <button type="submit" name="emp_save_venue" class="emp-btn">
                <?php esc_html_e( 'Save Venue', 'event-management-platform' ); ?>
            </button>
        </form>
    </div>
</div>
