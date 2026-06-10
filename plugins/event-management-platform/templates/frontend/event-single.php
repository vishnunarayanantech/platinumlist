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

        <!-- Banner Image -->
        <div class="emp-banner-image">
            <?php 
            $featured_url = '';
            if ( $event->featured_image_id ) {
                $featured_url = wp_get_attachment_image_url( $event->featured_image_id, 'full' );
            }
            if ( $featured_url ) : ?>
                <img src="<?php echo esc_url( $featured_url ); ?>" alt="<?php echo esc_attr( $event->title ); ?>" />
            <?php else : ?>
                <div style="width:100%; height:100%; background: linear-gradient(135deg, #1e1b4b 0%, #311042 100%); display:flex; align-items:center; justify-content:center;">
                    <span style="font-size:64px;">📅</span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Details Layout Grid -->
        <div class="emp-details-layout">
            
            <!-- Left Side: Core Content -->
            <div class="emp-main-content">
                
                <!-- Description -->
                <div class="emp-rich-content">
                    <?php if ( $category ) : ?>
                        <span class="emp-card-badge" style="position:static; display:inline-block; margin-bottom:15px;">
                            <?php echo esc_html( $category->name ); ?>
                        </span>
                    <?php endif; ?>

                    <h1 style="color:#fff; font-size:36px; font-weight:800; margin-top:0; margin-bottom:20px; line-height:1.2;">
                        <?php echo esc_html( $event->title ); ?>
                    </h1>

                    <h3 style="color:#fff; font-size:18px; margin-bottom:12px; font-weight:600;"><?php esc_html_e( 'About the Event', 'event-management-platform' ); ?></h3>
                    
                    <div style="color: var(--emp-fe-text); font-size: 15px; margin-bottom: 24px;">
                        <?php
                        $event_content = $event->full_description ?: $event->short_description ?: '';
                        echo wp_kses_post( do_shortcode( wpautop( $event_content ) ) );
                        ?>
                    </div>
                </div>

                <!-- Content Sections -->
                <?php if ( ! empty( $event->sections ) ) : ?>
                    <?php foreach ( $event->sections as $section ) : ?>
                        <div class="emp-rich-content">
                            <?php if ( ! empty( $section->title ) ) : ?>
                                <h3 style="color:#fff; font-size:20px; font-weight:700; margin-top:0; margin-bottom:15px;">
                                    <?php echo esc_html( $section->title ); ?>
                                </h3>
                            <?php endif; ?>
                            <?php if ( ! empty( $section->content ) ) : ?>
                                <div style="color: var(--emp-fe-text); font-size: 15px; margin-bottom: 24px;">
                                    <?php echo wp_kses_post( do_shortcode( wpautop( $section->content ) ) ); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Gallery -->
                <?php if ( ! empty( $event->gallery ) ) : ?>
                    <div class="emp-rich-content">
                        <h3 style="color:#fff; font-size:20px; font-weight:700; margin-top:0; margin-bottom:15px;">
                            <?php esc_html_e( 'Event Gallery', 'event-management-platform' ); ?>
                        </h3>
                        <div class="emp-fe-gallery">
                            <?php foreach ( $event->gallery as $attachment_id ) : 
                                $thumbnail = wp_get_attachment_image_url( $attachment_id, 'thumbnail' );
                                $full_url  = wp_get_attachment_image_url( $attachment_id, 'full' );
                                if ( $thumbnail ) : ?>
                                    <div class="emp-fe-gallery-item">
                                        <a href="<?php echo esc_url( $full_url ); ?>" target="_blank">
                                            <img src="<?php echo esc_url( $thumbnail ); ?>" alt="Gallery Image" />
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- FAQ Accordion -->
                <?php if ( ! empty( $event->faqs ) ) : ?>
                    <div class="emp-rich-content">
                        <h3 style="color:#fff; font-size:20px; font-weight:700; margin-top:0; margin-bottom:15px;">
                            <?php esc_html_e( 'Event FAQs', 'event-management-platform' ); ?>
                        </h3>
                        <div class="emp-fe-faq-list">
                            <?php foreach ( $event->faqs as $faq ) : ?>
                                <div class="emp-fe-faq-item">
                                    <div class="emp-fe-faq-q">
                                        <span><?php echo esc_html( $faq->question ); ?></span>
                                        <span>➕</span>
                                    </div>
                                    <div class="emp-fe-faq-a">
                                        <?php echo wpautop( esc_html( $faq->answer ) ); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Organizers -->
                <?php if ( ! empty( $event->organizers ) ) : ?>
                    <div class="emp-rich-content">
                        <h3 style="color:#fff; font-size:20px; font-weight:700; margin-top:0; margin-bottom:15px;">
                            <?php esc_html_e( 'Organized By', 'event-management-platform' ); ?>
                        </h3>
                        <div style="display:flex; flex-direction:column; gap:16px;">
                            <?php foreach ( $event->organizers as $org ) : ?>
                                <div style="background-color:rgba(0,0,0,0.2); padding:15px; border-radius:8px; border:1px solid var(--emp-fe-border);">
                                    <h4 style="color:#fff; margin:0 0 8px 0; font-size:16px; font-weight:600;"><?php echo esc_html( $org->name ); ?></h4>
                                    <?php if ( $org->email ) : ?>
                                        <p style="margin:0 0 4px 0; font-size:14px; color:var(--emp-fe-muted);">📧 <?php echo esc_html( $org->email ); ?></p>
                                    <?php endif; ?>
                                    <?php if ( $org->phone ) : ?>
                                        <p style="margin:0 0 4px 0; font-size:14px; color:var(--emp-fe-muted);">📞 <?php echo esc_html( $org->phone ); ?></p>
                                    <?php endif; ?>
                                    <?php if ( $org->website ) : ?>
                                        <p style="margin:0; font-size:14px; color:var(--emp-fe-muted);">🌐 <a href="<?php echo esc_url( $org->website ); ?>" target="_blank" style="color:var(--emp-fe-primary);"><?php echo esc_html( $org->website ); ?></a></p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

            <!-- Right Side: Sticky Sidebar details -->
            <div class="emp-sidebar">
                
                <!-- Date, Time, Venue Info -->
                <div class="emp-sidebar-widget">
                    <h3 class="emp-widget-title"><?php esc_html_e( 'Event Details', 'event-management-platform' ); ?></h3>
                    
                    <div class="emp-widget-item">
                        <label><?php esc_html_e( 'Date & Time', 'event-management-platform' ); ?></label>
                        <span>
                            <?php 
                            if ( $event->start_datetime ) {
                                echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $event->start_datetime ) ) );
                            } else {
                                esc_html_e( 'TBA', 'event-management-platform' );
                            }
                            ?>
                        </span>
                    </div>

                    <?php if ( $event->end_datetime ) : ?>
                        <div class="emp-widget-item">
                            <label><?php esc_html_e( 'Ends', 'event-management-platform' ); ?></label>
                            <span>
                                <?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $event->end_datetime ) ) ); ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <div class="emp-widget-item">
                        <label><?php esc_html_e( 'Timezone', 'event-management-platform' ); ?></label>
                        <span><?php echo esc_html( $event->timezone ); ?></span>
                    </div>

                    <?php if ( $venue ) : ?>
                        <div class="emp-widget-item" style="border-top:1px solid var(--emp-fe-border); padding-top:15px; margin-top:15px;">
                            <label><?php esc_html_e( 'Venue Location', 'event-management-platform' ); ?></label>
                            <span style="font-weight:600; font-size:16px; display:block; margin-bottom:4px;">
                                <a href="<?php echo esc_url( home_url( '/venues/' . sanitize_title( $venue->name ) ) ); ?>" style="color:#fff; text-decoration:none;">
                                    <?php echo esc_html( $venue->name ); ?>
                                </a>
                            </span>
                            <span style="color:var(--emp-fe-muted); font-size:13px; display:block;">
                                <?php echo esc_html( $venue->address ); ?>, <?php echo esc_html( $venue->city ); ?>, <?php echo esc_html( $venue->country ); ?>
                            </span>
                            
                            <?php if ( $venue->google_map_url ) : ?>
                                <a href="<?php echo esc_url( $venue->google_map_url ); ?>" target="_blank" style="display:inline-block; font-size:12px; color:var(--emp-fe-primary); margin-top:8px; text-decoration:none;">
                                    🗺️ <?php esc_html_e( 'Open in Google Maps', 'event-management-platform' ); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Booking Panel (Future integration hooks) -->
                <div class="emp-sidebar-widget" style="background: linear-gradient(135deg, rgba(99,102,241,0.15) 0%, rgba(236,72,153,0.15) 100%);">
                    <h3 class="emp-widget-title" style="border-color: rgba(255,255,255,0.15);"><?php esc_html_e( 'Ticketing & Registration', 'event-management-platform' ); ?></h3>
                    <p style="font-size:13px; color:var(--emp-fe-muted); margin-bottom:20px;">
                        <?php esc_html_e( 'Secure your seats. Ticketing and seating chart modules will be integrated here.', 'event-management-platform' ); ?>
                    </p>
                    <button class="emp-filter-input" style="background: linear-gradient(to right, var(--emp-fe-primary), var(--emp-fe-accent)) !important; color:#fff !important; font-weight:700; border:none !important; cursor:not-allowed;" disabled>
                        <?php esc_html_e( 'Tickets Available Soon', 'event-management-platform' ); ?>
                    </button>
                </div>

            </div>

        </div>

    </div>
</div>
<?php
get_footer();
?>
