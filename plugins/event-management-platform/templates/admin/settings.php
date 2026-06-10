<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="emp-admin-wrap">
    <div class="emp-admin-header">
        <h1><?php esc_html_e( 'Platform Settings', 'event-management-platform' ); ?></h1>
    </div>

    <?php if ( ! empty( $notice ) ) : ?>
        <div class="emp-notice"><?php echo esc_html( $notice ); ?></div>
    <?php endif; ?>

    <div class="emp-card" style="max-width: 600px;">
        <form method="post" action="">
            <?php wp_nonce_field( 'emp_save_settings', 'emp_save_settings_nonce' ); ?>

            <h2><?php esc_html_e( 'General Configuration', 'event-management-platform' ); ?></h2>

            <div class="emp-form-group">
                <label for="emp-sett-maps-key"><?php esc_html_e( 'Google Maps API Key', 'event-management-platform' ); ?></label>
                <input type="text" id="emp-sett-maps-key" name="google_maps_api_key" value="<?php echo esc_attr( $settings['google_maps_api_key'] ); ?>" class="emp-form-control" />
                <span style="font-size:11px; color:var(--emp-text-muted);"><?php esc_html_e( 'Required to display interactive venue maps on event detail pages.', 'event-management-platform' ); ?></span>
            </div>

            <div class="emp-form-group">
                <label for="emp-sett-tz"><?php esc_html_e( 'Default Timezone', 'event-management-platform' ); ?></label>
                <input type="text" id="emp-sett-tz" name="default_timezone" value="<?php echo esc_attr( $settings['default_timezone'] ); ?>" class="emp-form-control" />
            </div>

            <div class="emp-form-group">
                <label for="emp-sett-per-page"><?php esc_html_e( 'Events per Page (Frontend)', 'event-management-platform' ); ?></label>
                <input type="number" id="emp-sett-per-page" name="events_per_page" value="<?php echo esc_attr( $settings['events_per_page'] ); ?>" class="emp-form-control" min="1" />
            </div>

            <div class="emp-form-group" style="margin-top: 30px;">
                <label style="display:flex; align-items:center; gap:10px; font-weight:normal; font-size:15px; cursor:pointer;">
                    <input type="checkbox" name="enable_frontend" value="1" <?php checked( $settings['enable_frontend'], 1 ); ?> />
                    <?php esc_html_e( 'Enable custom frontend virtual routes (/events, etc.)', 'event-management-platform' ); ?>
                </label>
            </div>

            <button type="submit" name="emp_save_settings" class="emp-btn" style="margin-top: 15px;">
                <?php esc_html_e( 'Save Settings', 'event-management-platform' ); ?>
            </button>
        </form>
    </div>
</div>
