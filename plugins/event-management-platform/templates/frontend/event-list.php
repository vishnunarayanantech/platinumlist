<?php
get_header();
?>
<div class="emp-frontend-body" style="padding-top: 40px; padding-bottom: 80px;">
    <div class="emp-container">
        
        <!-- Header Section -->
        <div class="emp-header-section">
            <h1 class="emp-page-title"><?php esc_html_e( 'Discover Events', 'event-management-platform' ); ?></h1>
            <p class="emp-page-subtitle"><?php esc_html_e( 'Find and book the best concerts, festivals, conferences, and more.', 'event-management-platform' ); ?></p>
        </div>

        <!-- Filters Section -->
        <div class="emp-filter-card">
            <form method="get" action="<?php echo esc_url( home_url( '/events' ) ); ?>">
                <div class="emp-filter-grid">
                    
                    <div class="emp-filter-group">
                        <label for="emp-fe-search"><?php esc_html_e( 'Search', 'event-management-platform' ); ?></label>
                        <input type="text" id="emp-fe-search" name="s" value="<?php echo esc_attr( $filters['search'] ); ?>" class="emp-filter-input" placeholder="<?php esc_attr_e( 'Event title or keywords...', 'event-management-platform' ); ?>" />
                    </div>

                    <div class="emp-filter-group">
                        <label for="emp-fe-cat"><?php esc_html_e( 'Category', 'event-management-platform' ); ?></label>
                        <select id="emp-fe-cat" name="category" class="emp-filter-input">
                            <option value=""><?php esc_html_e( 'All Categories', 'event-management-platform' ); ?></option>
                            <?php foreach ( $categories as $cat ) : ?>
                                <option value="<?php echo esc_attr( $cat->id ); ?>" <?php selected( $filters['category_id'], $cat->id ); ?>>
                                    <?php echo esc_html( $cat->name ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="emp-filter-group">
                        <label for="emp-fe-venue"><?php esc_html_e( 'Venue', 'event-management-platform' ); ?></label>
                        <select id="emp-fe-venue" name="venue" class="emp-filter-input">
                            <option value=""><?php esc_html_e( 'All Venues', 'event-management-platform' ); ?></option>
                            <?php foreach ( $venues as $ven ) : ?>
                                <option value="<?php echo esc_attr( $ven->id ); ?>" <?php selected( $filters['venue_id'], $ven->id ); ?>>
                                    <?php echo esc_html( $ven->name ); ?> (<?php echo esc_html( $ven->city ); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="emp-filter-group">
                        <label for="emp-fe-date-from"><?php esc_html_e( 'From Date', 'event-management-platform' ); ?></label>
                        <input type="date" id="emp-fe-date-from" name="date_from" value="<?php echo esc_attr( $filters['date_from'] ); ?>" class="emp-filter-input" />
                    </div>

                    <div class="emp-filter-group">
                        <button type="submit" class="emp-filter-input" style="background-color: var(--emp-fe-primary) !important; color:#fff !important; cursor:pointer; font-weight:600; border:none !important;">
                            <?php esc_html_e( 'Filter Events', 'event-management-platform' ); ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Events List -->
        <?php if ( empty( $events ) ) : ?>
            <div class="emp-filter-card" style="text-align: center; padding: 60px 20px;">
                <h3 style="color:#fff; margin-top:0; font-size:22px;"><?php esc_html_e( 'No Events Found', 'event-management-platform' ); ?></h3>
                <p style="color:var(--emp-fe-muted); margin-bottom: 20px;"><?php esc_html_e( 'Try clearing your filters or searching for something else.', 'event-management-platform' ); ?></p>
                <a href="<?php echo esc_url( home_url( '/events' ) ); ?>" class="emp-page-num active" style="display:inline-block; font-size:14px;"><?php esc_html_e( 'Reset Filters', 'event-management-platform' ); ?></a>
            </div>
        <?php else : ?>
            <div class="emp-events-grid">
                <?php foreach ( $events as $ev ) : ?>
                    <article class="emp-event-card">
                        
                        <!-- Media Featured -->
                        <div class="emp-card-media">
                            <?php 
                            $featured_url = '';
                            if ( $ev->featured_image_id ) {
                                $featured_url = wp_get_attachment_image_url( $ev->featured_image_id, 'large' );
                            }
                            
                            if ( $featured_url ) : ?>
                                <img src="<?php echo esc_url( $featured_url ); ?>" alt="<?php echo esc_attr( $ev->title ); ?>" />
                            <?php else : ?>
                                <div style="width:100%; height:100%; background: linear-gradient(135deg, #1e1b4b 0%, #311042 100%); display:flex; align-items:center; justify-content:center;">
                                    <span style="font-size:32px;">📅</span>
                                </div>
                            <?php endif; ?>

                            <!-- Category Badge -->
                            <?php 
                            $cat_name = '';
                            $cat_slug = '';
                            foreach ( $categories as $c ) {
                                if ( $c->id === $ev->category_id ) {
                                    $cat_name = $c->name;
                                    $cat_slug = $c->slug;
                                    break;
                                }
                            }
                            
                            if ( $cat_name ) : ?>
                                <a href="<?php echo esc_url( home_url( '/categories/' . $cat_slug ) ); ?>" class="emp-card-badge">
                                    <?php echo esc_html( $cat_name ); ?>
                                </a>
                            <?php endif; ?>
                        </div>

                        <!-- Card Body -->
                        <div class="emp-card-body">
                            <div class="emp-card-date">
                                <?php 
                                if ( $ev->start_datetime ) {
                                    echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $ev->start_datetime ) ) );
                                } else {
                                    esc_html_e( 'TBA', 'event-management-platform' );
                                }
                                ?>
                            </div>
                            
                            <h2 class="emp-card-title">
                                <a href="<?php echo esc_url( home_url( '/events/' . $ev->slug ) ); ?>" style="color:#fff; text-decoration:none;">
                                    <?php echo esc_html( $ev->title ); ?>
                                </a>
                            </h2>

                            <p class="emp-card-text">
                                <?php echo esc_html( wp_trim_words( $ev->short_description ?: $ev->full_description, 20 ) ); ?>
                            </p>

                            <!-- Footer details -->
                            <div class="emp-card-footer">
                                <div class="emp-card-venue">
                                    <span>📍</span>
                                    <span>
                                        <?php 
                                        $v_name = '';
                                        $v_slug = '';
                                        foreach ( $venues as $v ) {
                                            if ( $v->id === $ev->venue_id ) {
                                                $v_name = $v->name;
                                                $v_slug = sanitize_title( $v->name );
                                                break;
                                            }
                                        }
                                        
                                        if ( $v_name ) : ?>
                                            <a href="<?php echo esc_url( home_url( '/venues/' . $v_slug ) ); ?>" style="color:var(--emp-fe-muted); text-decoration:none;">
                                                <?php echo esc_html( $v_name ); ?>
                                            </a>
                                        <?php else :
                                            esc_html_e( 'TBA', 'event-management-platform' );
                                        endif; ?>
                                    </span>
                                </div>
                                
                                <a href="<?php echo esc_url( home_url( '/events/' . $ev->slug ) ); ?>" class="emp-card-link">
                                    <?php esc_html_e( 'View Details &rarr;', 'event-management-platform' ); ?>
                                </a>
                            </div>
                        </div>

                    </article>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ( $pages > 1 ) : ?>
                <div class="emp-pagination">
                    <?php for ( $i = 1; $i <= $pages; $i++ ) : ?>
                        <a href="<?php echo esc_url( add_query_arg( 'paged', $i ) ); ?>" class="emp-page-num <?php echo $i === $page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>

        <?php endif; ?>

    </div>
</div>
<?php
get_footer();
?>
