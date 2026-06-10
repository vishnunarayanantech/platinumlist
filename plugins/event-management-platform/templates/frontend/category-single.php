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
            <span style="font-size:12px; color:var(--emp-fe-accent); text-transform:uppercase; font-weight:600; letter-spacing:1px;"><?php esc_html_e( 'Category Archive', 'event-management-platform' ); ?></span>
            <h1 class="emp-page-title" style="margin-top: 5px; margin-bottom: 0;"><?php echo esc_html( $category->name ); ?></h1>
        </div>

        <h2 style="color:#fff; font-size:24px; font-weight:700; margin-bottom:24px; border-bottom:1px solid var(--emp-fe-border); padding-bottom:10px;">
            <?php printf( esc_html__( 'Events in %s', 'event-management-platform' ), esc_html( $category->name ) ); ?>
        </h2>

        <?php if ( empty( $events ) ) : ?>
            <p style="color:var(--emp-fe-muted); font-size:15px;"><?php esc_html_e( 'No upcoming events found under this category.', 'event-management-platform' ); ?></p>
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
