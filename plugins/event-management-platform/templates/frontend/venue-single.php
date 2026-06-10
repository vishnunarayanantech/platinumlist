<?php
get_header();
?>
<div class="emp-frontend-body" style="padding-top: 40px; padding-bottom: 80px;">
    <div class="emp-container">

        <!-- Back Button -->
        <div style="margin-bottom: 24px;">
            <a href="<?php echo esc_url( home_url( '/events' ) ); ?>" style="color: var(--emp-fe-primary); text-decoration: none; font-weight: 600;">
                &larr; <?php esc_html_e( 'Back to Events', 'event-management-platform' ); ?>
            </a>
        </div>

        <div class="emp-filter-card" style="margin-bottom: 40px;">
            <h1 class="emp-page-title" style="margin-bottom: 10px;"><?php echo esc_html( $venue->name ); ?></h1>
            <p style="color:#fff; font-size:16px; margin:0 0 10px 0;">📍 <?php echo esc_html( $venue->address ); ?>, <?php echo esc_html( $venue->city ); ?>, <?php echo esc_html( $venue->country ); ?></p>
            <?php if ( $venue->latitude && $venue->longitude ) : ?>
                <p style="color:var(--emp-fe-muted); font-size:13px; margin:0 0 15px 0;">Coordinates: <code><?php echo esc_html( $venue->latitude ); ?>, <?php echo esc_html( $venue->longitude ); ?></code></p>
            <?php endif; ?>

            <?php if ( $venue->google_map_url ) : ?>
                <a href="<?php echo esc_url( $venue->google_map_url ); ?>" target="_blank" class="emp-page-num active" style="display:inline-block; font-size:13px;">
                    <?php esc_html_e( 'Get Directions (Google Maps)', 'event-management-platform' ); ?>
                </a>
            <?php endif; ?>
        </div>

        <h2 style="color:#fff; font-size:24px; font-weight:700; margin-bottom:24px; border-bottom:1px solid var(--emp-fe-border); padding-bottom:10px;">
            <?php printf( esc_html__( 'Events Happening at %s', 'event-management-platform' ), esc_html( $venue->name ) ); ?>
        </h2>

        <?php if ( empty( $events ) ) : ?>
            <p style="color:var(--emp-fe-muted); font-size:15px;"><?php esc_html_e( 'No upcoming events scheduled at this venue.', 'event-management-platform' ); ?></p>
        <?php else : ?>
            <div class="emp-events-grid">
                <?php foreach ( $events as $ev ) : ?>
                    <article class="emp-event-card">
                        <div class="emp-card-media">
                            <?php 
                            $featured_url = $ev->featured_image_id ? wp_get_attachment_image_url( $ev->featured_image_id, 'large' ) : '';
                            if ( $featured_url ) : ?>
                                <img src="<?php echo esc_url( $featured_url ); ?>" alt="<?php echo esc_attr( $ev->title ); ?>" />
                            <?php else : ?>
                                <div style="width:100%; height:100%; background: linear-gradient(135deg, #1e1b4b 0%, #311042 100%); display:flex; align-items:center; justify-content:center;">
                                    <span style="font-size:32px;">📅</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="emp-card-body">
                            <div class="emp-card-date">
                                <?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $ev->start_datetime ) ) ); ?>
                            </div>
                            <h3 class="emp-card-title">
                                <a href="<?php echo esc_url( home_url( '/events/' . $ev->slug ) ); ?>" style="color:#fff; text-decoration:none;">
                                    <?php echo esc_html( $ev->title ); ?>
                                </a>
                            </h3>
                            <p class="emp-card-text"><?php echo esc_html( wp_trim_words( $ev->short_description ?: $ev->full_description, 20 ) ); ?></p>
                            <div class="emp-card-footer">
                                <span></span>
                                <a href="<?php echo esc_url( home_url( '/events/' . $ev->slug ) ); ?>" class="emp-card-link">
                                    <?php esc_html_e( 'View Details &rarr;', 'event-management-platform' ); ?>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</div>
<?php
get_footer();
?>
